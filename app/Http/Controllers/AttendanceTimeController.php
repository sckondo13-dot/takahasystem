<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceTime;

class AttendanceTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendanceTimes = AttendanceTime::orderBy('name')
            ->get();

        return view(
            'attendance-times.index',
            compact('attendanceTimes')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('attendance-times.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',

            'start_time' => 'required',

            'end_time' => 'required',

        ]);

        AttendanceTime::create([

            'name' => $request->name,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,

            'note' => $request->note,

        ]);

        return redirect()
            ->route('attendance-times.index')
            ->with('success', '登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceTime $attendanceTime)
    {
        return view(
            'attendance-times.edit',
            compact('attendanceTime')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceTime $attendanceTime)
    {
        $request->validate([

            'name' => 'required',

            'start_time' => 'required',

            'end_time' => 'required',

        ]);

        $attendanceTime->update([

            'name' => $request->name,

            'start_time' => $request->start_time,

            'end_time' => $request->end_time,

            'note' => $request->note,

        ]);

        return redirect()
            ->route('attendance-times.index')
            ->with('success', '更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceTime $attendanceTime)
    {
        $attendanceTime->delete();

        return redirect()
            ->route('attendance-times.index')
            ->with('success', '削除しました');
    }
}
