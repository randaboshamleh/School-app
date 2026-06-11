<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AcademicYearResource::collection(AcademicYear::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicYearRequest $request)
    {
        $academicYear = AcademicYear::create($request->validated());

        return new AcademicYearResource($academicYear);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $academicYear = AcademicYear::find($id);

        if (! $academicYear) {
            return response()->json(['message' => 'Academic Year not found'], 404);
        }

        return new AcademicYearResource($academicYear);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicYearRequest $request, string $id)
    {
        $academicYear = AcademicYear::find($id);

        if (! $academicYear) {
            return response()->json(['message' => 'Academic Year not found'], 404);
        }

        $academicYear->update($request->validated());

        return new AcademicYearResource($academicYear);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $academicYear = AcademicYear::find($id);

        if (! $academicYear) {
            return response()->json(['message' => 'Academic Year not found'], 404);
        }

        $academicYear->delete();

        return response()->json(['message' => 'Academic Year deleted successfully']);
    }
}
