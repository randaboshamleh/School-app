<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamScoreRequest;
use App\Http\Requests\UpdateExamScoreRequest;
use App\Http\Resources\ExamScoreResource;
use App\Http\Resources\GradeResource;
use App\Models\exam_component;
use App\Models\ExamScore;
use App\Models\Student;
use App\Support\SchoolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamScoreController extends Controller
{
    public function managementIndex()
    {
        $query = ExamScore::with(['student', 'examComponent.subject']);

        if (SchoolAccess::canManageSchool(request()->user())) {
            $query->whereHas('student', fn ($q) => SchoolAccess::scopeStudents($q, request()->user()));
        }

        return response()->json([
            'status' => true,
            'data' => $query->latest()->get(),
        ]);
    }

    /**
     * عرض كل درجات الامتحانات حسب student_id
     */
    public function index(Request $request)
    {
        $studentId = $request->query('student_id');

        if (! $studentId) {
            return response()->json([
                'status' => false,
                'message' => 'student_id is required',
            ], 400);
        }

        /**
         * نحصل على المستخدم الحالي
         *
         * @var \App\Models\User|null $user
         */
        $user = Auth::user();

        // نبدأ بناء الاستعلام مع العلاقات المطلوبة
        $query = ExamScore::with(['examComponent.subject'])
            ->where('student_id', $studentId);

        // فلترة حسب القسم والدور (الموجه)
        if ($user->hasRole('primary_female_supervisor')) {
            $query->whereHas('student', fn ($q) => $q->where('stage', 'primary')->where('gender', 'female')
            );
        } elseif ($user->hasRole('primary_male_supervisor')) {
            $query->whereHas('student', fn ($q) => $q->where('stage', 'primary')->where('gender', 'male')
            );
        } elseif ($user->hasRole('secondary_female_supervisor')) {
            $query->whereHas('student', fn ($q) => $q->where('stage', 'secondary')->where('gender', 'female')
            );
        } elseif ($user->hasRole('secondary_male_supervisor')) {
            $query->whereHas('student', fn ($q) => $q->where('stage', 'secondary')->where('gender', 'male')
            );
        } elseif ($user->hasRole('thirdy_female_supervisor')) {
            $query->whereHas('student', fn ($q) => $q->where('stage', 'thirdy')->where('gender', 'female')
            );
        } elseif ($user->hasRole('thirdy_male_supervisor')) {
            $query->whereHas('student', fn ($q) => $q->where('stage', 'thirdy')->where('gender', 'male')
            );
        }

        // تنفيذ الاستعلام
        $examScores = $query->get();

        // في حال لم توجد علامات
        if ($examScores->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => "No exam scores found for student_id {$studentId}",
                'data' => [],
            ]);
        }

        // فلترة العناصر اللي ما عندها subject_name
        $filtered = $examScores->filter(fn ($item) => ! is_null($item->examComponent?->subject?->name)
        );

        // إزالة التكرارات حسب subject_name + component_name
        $unique = $filtered->unique(fn ($item) => $item->examComponent?->subject?->name.'-'.$item->examComponent?->component_name
        )->values();

        // الاستجابة النهائية
        return response()->json([
            'status' => true,
            'message' => "List of exam scores for student_id {$studentId}",
            'data' => ExamScoreResource::collection($unique),
        ]);
    }

    /**
     * إنشاء درجة جديدة
     */
    public function store(StoreExamScoreRequest $request)
    {
        $data = $request->validated();

        if (isset($data['student_id'])) {
            $student = Student::where('student_id', $data['student_id'])->first();
            if ($student) {
                $data['student_id'] = $student->student_id;
            } else {
                return response()->json(['error' => 'Student not found'], 404);
            }
        }

        $examScore = ExamScore::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Exam score created successfully',
            'data' => new ExamScoreResource($examScore),
        ], 201);
    }

    /**
     * عرض درجات الطالب لجميع المواد مع المكونات
     */
    public function showGrades($studentId)
    {
        $examScores = ExamScore::with('examComponent.subject')
            ->where('student_id', $studentId)
            ->get()
            ->groupBy('examComponent.subject_id');

        return GradeResource::collection($examScores);
    }

    /**
     * تحديث درجة
     */
    public function update(UpdateExamScoreRequest $request, ExamScore $examScore)
    {
        $examScore->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Exam score updated successfully',
            'data' => new ExamScoreResource($examScore),
        ]);
    }

    /**
     * حذف درجة
     */
    public function destroy(ExamScore $examScore)
    {
        $examScore->delete();

        return response()->json([
            'status' => true,
            'message' => 'Exam score deleted successfully',
        ]);
    }

    /**
     * عرض درجات الطالب حسب المستخدم الحالي
     */
    public function myScores(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (! $user->hasRole('student')) {
            return response()->json(['message' => 'User does not have the right role.'], 403);
        }

        $payments = $user->payments;
        $hasOverdue = $payments->contains(fn ($p) => method_exists($p, 'isOverdue') ? $p->isOverdue() : false);

        $scores = $user->examScores()->with('examComponent.subject')->get();

        if ($hasOverdue) {
            $scores->transform(fn ($score) => $score->forceFill([
                'marks_obtained' => 'محجوبة - يرجى تسديد الرسوم',
            ]));
        }

        return response()->json([
            'student' => $user->name,
            'scores' => $scores,
        ]);
    }

    /**
     * CSV import
     */
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

                $component = exam_component::firstOrCreate([
                    'component_name' => trim($row['component_name']),
                ]);

                ExamScore::updateOrCreate(
                    [
                        'student_id' => $student->student_id,
                        'name' => trim($row['name']),
                        'exam_component_id' => $component->id,
                    ],
                    [
                        'marks_obtained' => floatval($row['marks_obtained']),
                    ]
                );
            }
        });

        return back()->with('success', 'تم استيراد العلامات');
    }

    /**
     * CSV export
     */
    public function export()
    {
        $grades = ExamScore::with(['student', 'examComponent'])->get();

        return response()->streamDownload(function () use ($grades) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['student_id', 'name', 'marks_obtained', 'component_name']);

            foreach ($grades as $g) {
                fputcsv($fh, [
                    $g->student->student_id,
                    $g->name,
                    $g->marks_obtained,
                    $g->examComponent->component_name,
                ]);
            }

            fclose($fh);
        }, 'grades_export_'.date('Ymd').'.csv');
    }
}
