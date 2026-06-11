<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    // GET /api/homeworks
    public function index()
    {
        return Homework::with(['classroom', 'subject'])->get();
    }

    // POST /api/homeworks
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'task' => 'required|string',
            'due_date' => 'nullable|date',
        ]);

        $homework = Homework::create($validated);

        return response()->json(['message' => 'تمت الإضافة بنجاح', 'data' => $homework]);
    }

    // GET /api/homeworks/{id}
    public function show(Homework $homework)
    {
        return $homework;
    }

    // PUT/PATCH /api/homeworks/{id}
    public function update(Request $request, Homework $homework)
    {
        $homework->update($request->all());

        return response()->json($homework);
    }

    // DELETE /api/homeworks/{id}
    public function destroy(Homework $homework)
    {
        $homework->delete();

        return response()->json(['message' => 'Homework deleted']);
    }
}
