<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRoomsSubjectTeacherRequest;
use App\Http\Requests\UpdateClassRoomsSubjectTeacherRequest;
use App\Http\Resources\ClassRoomSubjectTeacherResource;
use App\Models\ClassRoomsSubjectTeacher;

class ClassRoomsSubjectTeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ClassRoomSubjectTeacherResource::collection(ClassRoomsSubjectTeacher::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRoomsSubjectTeacherRequest $request)
    {
        $record = ClassRoomsSubjectTeacher::create($request->validated());

        return new ClassRoomSubjectTeacherResource($record);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $record = ClassRoomsSubjectTeacher::with(['classRoom', 'subject', 'teacher'])->find($id);

        if (! $record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return new ClassRoomSubjectTeacherResource($record);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassRoomsSubjectTeacherRequest $request, string $id)
    {
        $record = ClassRoomsSubjectTeacher::find($id);

        if (! $record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $record->update($request->validated());

        return new ClassRoomSubjectTeacherResource($record);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $record = ClassRoomsSubjectTeacher::find($id);

        if (! $record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $record->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}
