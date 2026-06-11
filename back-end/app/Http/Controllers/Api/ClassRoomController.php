<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRoomRequest;
use App\Http\Requests\UpdateClassRoomRequest;
use App\Http\Resources\ClassRoomResource;
use App\Models\ClassRoom;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ClassRoomResource::collection(ClassRoom::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRoomRequest $request)
    {
        $classRoom = ClassRoom::create($request->validated());

        return new ClassRoomResource($classRoom);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $classRoom = ClassRoom::with(['academicYear', 'teacher'])->find($id);

        if (! $classRoom) {
            return response()->json(['message' => 'Class Room not found'], 404);
        }

        return new ClassRoomResource($classRoom);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassRoomRequest $request, string $id)
    {
        $classRoom = ClassRoom::find($id);

        if (! $classRoom) {
            return response()->json(['message' => 'ClassRoom not found'], 404);
        }

        $classRoom->update($request->validated());

        return new ClassRoomResource($classRoom);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $classRoom = ClassRoom::find($id);

        if (! $classRoom) {
            return response()->json(['message' => 'Class Room not found'], 404);
        }

        $classRoom->delete();

        return response()->json(['message' => 'Class Room deleted successfully']);
    }
}
