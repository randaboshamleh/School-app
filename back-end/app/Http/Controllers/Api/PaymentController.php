<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Student;
use App\Support\SchoolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $query = Payment::query()->with('Student');

        if (SchoolAccess::canManageSchool(request()->user())) {
            $query->whereHas('Student', fn ($studentQuery) => SchoolAccess::scopeStudents($studentQuery, request()->user()));
        }

        return PaymentResource::collection($query->get());
    }

    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'method' => 'required|string',
            'reference' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        // جلب التسجيل (enrollment)
        $enrollment = \App\Models\Enrollment::findOrFail($request->enrollment_id);

        // الرسوم الأصلية (يا إما ثابتة أو مخزنة عند الـ enrollment نفسه)
        $originalAmount = $enrollment->tuition_fee ?? 1000;

        // حساب المبلغ بعد المنحة
        $finalAmount = $enrollment->getDiscountedFee($originalAmount);

        // 🔹 تحديد الحالة عند الإنشاء
        if ($request->payment_date) {
            $status = 'paid';
        } elseif ($request->due_date && now()->gt($request->due_date)) {
            $status = 'overdue';
        } else {
            $status = 'pending';
        }

        $payment = Payment::create([
            'student_id' => $enrollment->student_id,
            'enrollment_id' => $enrollment->id,
            'original_amount' => $originalAmount,
            'scholarship_percentage' => $enrollment->scholarship_percentage,
            'amount' => $finalAmount,
            'method' => $request->method,
            'reference' => $request->reference,
            'payment_date' => $request->payment_date,
            'due_date' => $request->due_date,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'تم إنشاء الدفعة بنجاح',
            'data' => new PaymentResource($payment),
        ], 201);
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return new PaymentResource($payment);
    }

    public function update(UpdatePaymentRequest $request, string $id)
    {
        $payment = Payment::find($id);

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->update($request->validated());

        return new PaymentResource($payment);
    }

    public function destroy(string $id)
    {
        $payment = Payment::find($id);

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }

    public function pay(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->is_paid) {
            return response()->json(['message' => 'Payment already completed.']);
        }

        // 🔹 احتساب الغرامة وتحديد الحالة
        $lateFee = 0;
        $status = 'paid';

        if ($payment->due_date && now()->gt($payment->due_date)) {
            $lateFee = $payment->amount * 0.10;
            $status = 'overdue'; // ✅ دفع بعد الموعد
        }

        // تحديث حالة الدفع
        $payment->update([
            'is_paid' => true,
            'late_fee' => $lateFee,
            'payment_date' => now(),
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Payment completed successfully.',
            'late_fee' => $lateFee,
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function updateDueDate(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->due_date = $request->input('due_date');

        // 🔹 إعادة تقييم الحالة إذا تم تغيير تاريخ الاستحقاق
        if (! $payment->payment_date && $payment->due_date && now()->gt($payment->due_date)) {
            $payment->status = 'overdue';
        } elseif (! $payment->payment_date) {
            $payment->status = 'pending';
        }

        $payment->save();

        return response()->json([
            'message' => 'تم تحديث آخر مهلة للدفع بنجاح',
            'payment' => $payment,
        ]);
    }

    public function markPaid(Payment $payment)
    {
        $payment->update(['status' => 'paid', 'paid_at' => now()]);

        // سجل المعاملة، أرسل إشعار إن أردت
        return back()->with('success', 'تم وضعها كمدفوعة');
    }

    public function notifyStudent($id)
    {
        $payment = Payment::with('student.user')->findOrFail($id);

        $studentUser = $payment->student->user; // علاقة Student -> User

        if (! $studentUser || ! $studentUser->fcm_token) {
            return response()->json(['message' => 'لا يوجد FCM Token لهذا الطالب.'], 404);
        }

        $title = 'تذكير بموعد الدفع';
        $body = "عزيزي {$studentUser->name}، نود تذكيرك بأن آخر موعد للدفع هو ".optional($payment->due_date)->format('Y-m-d');

        // استخدم Notification جاهزة
        $studentUser->notify(new \App\Notifications\SchoolNotification($title, $body));

        return response()->json([
            'message' => '✅ تم إرسال الإشعار بنجاح.',
        ]);
    }

    public function import(Request $r)
    {
        $r->validate(['file' => 'required|file|mimes:csv,txt']);
        $path = $r->file('file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        DB::transaction(function () use ($rows) {
            $header = array_map('trim', $rows[0]);

            for ($i = 1; $i < count($rows); $i++) {
                $row = array_combine($header, $rows[$i]);

                $student = Student::where('student_id', $row['student_id'])->first();
                if (! $student) {
                    continue;
                }

                Payment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'payment_date' => $row['payment_date'],
                    ],
                    [
                        'amount' => floatval($row['amount']),
                        'status' => $row['status'],
                    ]
                );
            }
        });

        return back()->with('success', '✅ تم استيراد الدفعات بنجاح');
    }

    // تصدير CSV للدفعات
    public function export()
    {
        $payments = Payment::with('student')->get();

        return response()->streamDownload(function () use ($payments) {
            $fh = fopen('php://output', 'w');
            // الأعمدة
            fputcsv($fh, ['student_id', 'amount', 'status', 'payment_date']);

            // البيانات
            foreach ($payments as $p) {
                fputcsv($fh, [
                    $p->student->student_id,
                    $p->amount,
                    $p->status,      // مثال: paid/unpaid/late
                    $p->payment_date, // تاريخ الدفع
                ]);
            }
            fclose($fh);
        }, 'payments_export_'.date('Ymd').'.csv');
    }
}
