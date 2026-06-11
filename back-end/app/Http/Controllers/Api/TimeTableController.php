<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTimeTableRequest;
use App\Models\TimeTable;
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(TimeTable::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            '*.day' => 'required|string',
            '*.subject' => 'required|string',
            '*.class_room_id' => 'required|exists:classrooms,id',
        ]);

        $saved = [];

        foreach ($validated as $item) {
            $t = new TimeTable;
            $t->day = $item['day'];
            $t->subject = $item['subject'];
            $t->class_room_id = $item['class_room_id'];
            $t->save();
            $saved[] = $t;
        }

        return response()->json($saved, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return TimeTable::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeTableRequest $request, string $id)
    {
        $timeTable = TimeTable::findOrFail($id);
        $timeTable->update($request->all());

        return $timeTable;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $timeTable = TimeTable::findOrFail($id);
        $timeTable->delete();

        return response()->json(['message' => 'Schedule deleted']);
    }
}
