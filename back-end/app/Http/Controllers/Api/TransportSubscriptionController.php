<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\transport_subscribtions;
use App\Models\transportRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TransportSubscriptionController extends Controller
{
    protected function currentAcademicYear()
    {
        $now = now();
        $yearStart = $now->month >= 9 ? $now->year : $now->year - 1;

        // نجيب السجل من جدول academic_years حسب بداية السنة (رقم)
        $yearLabel = $yearStart.'-'.($yearStart + 1);

        return AcademicYear::where('is_current', true)
            ->orWhere('year', (string) $yearStart)
            ->orWhere('year', $yearLabel)
            ->firstOrFail();
    }

    // GET /api/transport/status
    public function status(Request $request)
    {
        $studentId = $request->user()->student_id; // الاعتماد حصراً على الطالب
        $academicYear = $this->currentAcademicYear();

        $sub = transport_subscribtions::where('student_id', $studentId)
            ->where('academic_year_id', $academicYear->id)
            ->latest()
            ->first();

        return response()->json([
            'academic_year' => $academicYear->year,
            'status' => $sub ? $this->subscriptionIsActive($sub) : false,
            'route_id' => $sub?->route_id,
        ]);
    }

    // POST /api/transport/subscribe
    public function subscribe(Request $request)
    {
        $request->validate([
            'route_id' => 'required|integer|exists:transport_routes,id',
        ]);

        $studentId = $request->user()->student_id;
        $academicYear = $this->currentAcademicYear();
        $userId = Auth::id();

        // إنشاء أو تحديث الاشتراك
        $sub = transport_subscribtions::updateOrCreate(
            [
                'student_id' => $studentId,
                'academic_year_id' => $academicYear->id,
                'route_id' => $request->route_id,
            ],
            $this->subscriptionActivationPayload($userId)
        );

        // إضافة دفعة جديدة
        $route = transportRoutes::findOrFail($request->route_id);
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('academic_year_id', $academicYear->id)
            ->where('is_current', true) // لو عندك أكثر من سجل
            ->first();

        if (! $enrollment) {
            return response()->json([
                'message' => 'الطالب غير مسجّل في أي صف لهذه السنة',
            ], 400);
        }

        $payment = Payment::create([
            'student_id' => $studentId,
            'amount' => $route->price,
            'status' => 'pending',
            'description' => 'اشتراك نقل للخط: '.$route->name,
            'subscription_id' => $sub->id ?? null,
            'enrollment_id' => $enrollment->id,
        ]);

        return response()->json([
            'message' => 'تم الاشتراك وإضافة المبلغ المطلوب للدفع',
            'subscription' => $sub,
            'payment' => $payment,
        ]);
    }

    // DELETE /api/transport/subscribe/{routeId}
    public function cancel(Request $request, $routeId)
    {
        $studentId = $request->user()->student_id;
        $academicYear = $this->currentAcademicYear();

        $sub = transport_subscribtions::where('student_id', $studentId)
            ->where('academic_year_id', $academicYear->id)
            ->first();

        if (! $sub || ! $this->subscriptionIsActive($sub)) {
            return response()->json(['message' => 'غير مشترك حالياً'], 400);
        }

        $sub->update($this->subscriptionCancellationPayload());

        return response()->json(['message' => 'تم إلغاء الاشتراك']);
    }

    private function subscriptionIsActive(transport_subscribtions $subscription): bool
    {
        if (Schema::hasColumn('transport_subscribtions', 'is_active')) {
            return (bool) $subscription->is_active;
        }

        return $subscription->status === 'active' || $subscription->status === true || $subscription->status === 1;
    }

    private function subscriptionActivationPayload(?int $userId): array
    {
        $payload = [];

        if (Schema::hasColumn('transport_subscribtions', 'user_id')) {
            $payload['user_id'] = $userId;
        }

        if (Schema::hasColumn('transport_subscribtions', 'is_active')) {
            $payload['is_active'] = true;
        } else {
            $payload['status'] = 'active';
        }

        return $payload;
    }

    private function subscriptionCancellationPayload(): array
    {
        if (Schema::hasColumn('transport_subscribtions', 'is_active')) {
            return ['is_active' => false];
        }

        return ['status' => 'canceled'];
    }
}
