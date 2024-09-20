<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index() 
    {
        $timetables = Timetable::all();
        return view('timetable.index', compact('timetables'));
    }

    public function store() 
    {
        $data = request()->validate([
            'name' => 'required|string',
            'late_time' => 'required|integer',
            'leave_early_time' => 'required|integer',
            'on_time' => 'required|date_format:H:i',
            'off_time' => 'required|date_format:H:i',
            'checkin_start' => 'required|date_format:H:i',
            'checkin_end' => 'required|date_format:H:i',
            'checkout_start' => 'required|date_format:H:i',
            'checkout_end' => 'required|date_format:H:i',
        ]);

        if(!$data) {
            return back()->with('error', 'Error creating timetable');
        }

        Timetable::updateOrCreate(['id' => request('timetable_id')], $data);

        return back()->with('success', 'Timetable created successfully');
    }

    public function destroy(Timetable $timetable) 
    {
        $timetable->delete();
        return response()->json(['success' => true, 'message' => 'Timetable deleted successfully']);
    }
}
