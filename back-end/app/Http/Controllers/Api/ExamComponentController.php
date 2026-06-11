<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\exam_component;
use Illuminate\Http\Request;

class ExamComponentController extends Controller
{
    /**
     * عرض جميع مكونات الامتحانات.
     */
    public function index()
    {
        $components = exam_component::all();

        return response()->json([
            'success' => true,
            'data' => $components,
        ], 200);
    }

    /**
     * عرض مكون امتحان معين.
     */
    public function show($id)
    {
        $component = exam_component::find($id);

        if (! $component) {
            return response()->json([
                'success' => false,
                'message' => 'مكون الامتحان غير موجود',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $component,
        ], 200);
    }

    /**
     * إنشاء مكون امتحان جديد.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|integer|exists:exams,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_room_id' => 'nullable|integer|exists:class_rooms,id',
            'component_name' => 'required|string|max:255',
            'weight_percentage' => 'nullable|numeric|min:0|max:100',
            'max_marks' => 'required|numeric|min:0',
            'min_marks' => 'required|numeric|min:0',
        ]);

        $component = exam_component::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة مكون الامتحان بنجاح',
            'data' => $component,
        ], 201);
    }

    /**
     * تحديث مكون امتحان موجود.
     */
    public function update(Request $request, $id)
    {
        $component = exam_component::find($id);

        if (! $component) {
            return response()->json([
                'success' => false,
                'message' => 'مكون الامتحان غير موجود',
            ], 404);
        }

        $validated = $request->validate([
            'exam_id' => 'sometimes|integer|exists:exams,id',
            'subject_id' => 'sometimes|integer|exists:subjects,id',
            'class_room_id' => 'sometimes|integer|exists:class_rooms,id',
            'component_name' => 'sometimes|string|max:255',
            'weight_percentage' => 'sometimes|numeric|min:0|max:100',
            'max_marks' => 'sometimes|numeric|min:0',
            'min_marks' => 'sometimes|numeric|min:0',
        ]);

        $component->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث مكون الامتحان بنجاح',
            'data' => $component,
        ], 200);
    }

    /**
     * حذف مكون امتحان.
     */
    public function destroy($id)
    {
        $component = exam_component::find($id);

        if (! $component) {
            return response()->json([
                'success' => false,
                'message' => 'مكون الامتحان غير موجود',
            ], 404);
        }

        $component->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف مكون الامتحان بنجاح',
        ], 200);
    }
}
