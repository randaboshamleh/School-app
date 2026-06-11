<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EnrollmentResource::collection(Enrollment::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Enrollment::create($request->validated());

        return new EnrollmentResource($enrollment);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $enrollment = Enrollment::with(['student', 'classRoom', 'academicYear'])->find($id);

        if (! $enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        return new EnrollmentResource($enrollment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnrollmentRequest $request, string $id)
    {
        $enrollment = Enrollment::find($id);

        if (! $enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        $enrollment->update($request->validated());

        return new EnrollmentResource($enrollment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $enrollment = Enrollment::find($id);

        if (! $enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        $enrollment->delete();

        return response()->json(['message' => 'Enrollment deleted successfully']);
    }
}
