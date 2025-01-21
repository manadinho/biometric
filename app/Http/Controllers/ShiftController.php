<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Timetable;

class ShiftController extends Controller
{
    public function index() 
    {
        $shifts = Shift::all();
        $timetables = Timetable::all();
        return view('shifts.index', compact('shifts', 'timetables'));
    }

    public function store()
    {
        $data = request()->validate([
            'name' => 'required|string|max:255'
        ]);

        // if data is not validated
        if (!$data) {
            return back()->withErrors($data);
        }

        // if shift_id is present, create default timetable for shift with null values

        if(!request('shift_id')) {
            $timetables = [
                'MONDAY' => null,
                'TUESDAY' => null,
                'WEDNESDAY' => null,
                'THURSDAY' => null,
                'FRIDAY' => null,
                'SATURDAY' => null,
                'SUNDAY' => null,
            ];

            $data['timetables'] = json_encode($timetables);
        }

        Shift::updateOrCreate(['id' => request('shift_id')], $data);
        
        return back()->with('success', 'Shift created successfully');
    }   

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return response()->json(['success' => true, 'message' => 'Shift deleted successfully']);
    }

    public function addTimetable() 
    {   
        $data = request()->validate([
            'id' => 'required|integer',
            'timetables' => 'required'
        ]);

        // if data is not validated
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Invalid data']);
        }

        $shift = Shift::find($data['id']);

        // if shift not found
        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'Shift not found']);
        }


        $shift->timetables = json_encode($data['timetables']);
        $shift->save();

        return response()->json(['success' => true, 'message' => 'Timetable added successfully']);
    }
}
