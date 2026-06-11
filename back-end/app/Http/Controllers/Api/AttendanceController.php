<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendancesRequest;
use App\Http\Requests\UpdateAttendancesRequest;
use App\Http\Resources\AttendancesResource;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AttendancesResource::collection(Attendance::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendancesRequest $request)
    {
        $attendance = Attendance::create($request->validated());

        return new AttendancesResource($attendance);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $attendance = Attendance::with(['student', 'classRoom'])->find($id);

        if (! $attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        return new AttendancesResource($attendance);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendancesRequest $request, string $id)
    {
        $attendance = Attendance::find($id);

        if (! $attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        $attendance->update($request->validated());

        return new AttendancesResource($attendance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $attendance = Attendance::find($id);

        if (! $attendance) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }

        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully']);
    }
}
