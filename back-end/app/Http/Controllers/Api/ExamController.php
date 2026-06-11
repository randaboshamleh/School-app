<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $midterms = Exam::where('exam_type', 'midterm')
            ->with('subject:id,name')
            ->orderBy('exam_date', 'asc')
            ->get()
            ->map(function ($exam) {
                return [
                    'day' => \Carbon\Carbon::parse($exam->exam_date)->locale('ar')->dayName,
                    'date' => $exam->exam_date,
                    'subject' => $exam->subject->name ?? '',
                    'syllabus' => $exam->syllabus ?? '',
                    'duration' => ($exam->start_time ?? '-').' → '.($exam->end_time ?? '-'),
                ];
            });

        $finals = Exam::where('exam_type', 'final')
            ->with('subject:id,name')
            ->get()
            ->map(function ($exam) {
                return [
                    'day' => \Carbon\Carbon::parse($exam->exam_date)->locale('ar')->dayName,
                    'date' => $exam->exam_date,
                    'subject' => $exam->subject->name ?? '',
                    'syllabus' => $exam->syllabus ?? '',
                    'duration' => ($exam->start_time ?? '-').' → '.($exam->end_time ?? '-'),
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'midterms' => $midterms,
                'finals' => $finals,
            ],
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'exam_code' => 'required|string|unique:exams,exam_code|max:50',
            'subject_id' => 'required|exists:subjects,id',
            'classrooms_id' => 'required|exists:classrooms,id',
            'exam_date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'duration_minutes' => 'nullable|integer|min:10',
            'instructions' => 'nullable|string',
            'syllabus' => 'string',
            'is_active' => 'boolean',
            'exam_type' => 'required|in:midterm,final',
        ], [
            'title.required' => 'يجب إدخال عنوان الامتحان',
            'exam_code.required' => 'يجب إدخال كود الامتحان',
            'exam_code.unique' => 'كود الامتحان مستخدم مسبقاً',
            'subject_id.required' => 'يجب اختيار المادة',
            'subject_id.exists' => 'المادة غير موجودة',
            'classrooms_id.required' => 'يجب اختيار الصف',
            'classrooms_id.exists' => 'الصف غير موجود',
            'exam_date.required' => 'يجب إدخال تاريخ الامتحان',
            'exam_date.after_or_equal' => 'تاريخ الامتحان يجب أن يكون اليوم أو بعده',
            'start_time.nullable' => 'يجب إدخال وقت البداية',
            'end_time.nullable' => 'يجب إدخال وقت النهاية',
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية',
            'duration_minutes.required' => 'يجب تحديد مدة الامتحان بالدقائق',
            'duration_minutes.min' => 'الامتحان يجب أن لا يقل عن 10 دقائق',
            'exam_type.required' => 'يجب تحديد نوع الامتحان',
            'exam_type.in' => 'نوع الامتحان يجب أن يكون نصفي أو نهائي',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $exam = Exam::create($validator->validated());

        return new ExamResource($exam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exam = Exam::find($id);

        if (! $exam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'exam_code' => 'sometimes|required|string|max:50|unique:exams,exam_code,'.$exam->id,
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'classrooms_id' => 'sometimes|required|exists:classrooms,id',
            'exam_date' => 'sometimes|required|date|after_or_equal:today',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'duration_minutes' => 'sometimes|required|integer|min:10',
            'instructions' => 'nullable|string',
            'syllabus' => 'string',
            'is_active' => 'boolean',
            'exam_type' => 'sometimes|required|in:midterm,final',
        ], [
            'title.required' => 'يجب إدخال عنوان الامتحان',
            'exam_code.required' => 'يجب إدخال كود الامتحان',
            'exam_code.unique' => 'كود الامتحان مستخدم مسبقاً',
            'subject_id.required' => 'يجب اختيار المادة',
            'subject_id.exists' => 'المادة غير موجودة',
            'classrooms_id.required' => 'يجب اختيار الصف',
            'classrooms_id.exists' => 'الصف غير موجود',
            'exam_date.required' => 'يجب إدخال تاريخ الامتحان',
            'exam_date.after_or_equal' => 'تاريخ الامتحان يجب أن يكون اليوم أو بعده',
            'start_time.nullable' => 'يجب إدخال وقت البداية',
            'end_time.nullable' => 'يجب إدخال وقت النهاية',
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية',
            'duration_minutes.required' => 'يجب تحديد مدة الامتحان بالدقائق',
            'duration_minutes.min' => 'الامتحان يجب أن لا يقل عن 10 دقائق',
            'exam_type.required' => 'يجب تحديد نوع الامتحان',
            'exam_type.in' => 'نوع الامتحان يجب أن يكون نصفي أو نهائي',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $exam->update($validator->validated());

        return new ExamResource($exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $exam = Exam::find($id);

        if (! $exam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }

        $exam->delete();

        return response()->json(['message' => 'Exam deleted successfully']);
    }
}
