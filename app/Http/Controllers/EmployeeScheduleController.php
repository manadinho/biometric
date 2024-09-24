<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Shift;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeScheduleController extends Controller
{
    function index() 
    {
        $timetables = Timetable::all();
        $shifts = Shift::all();
        $departments = Department::whereNull('parent_id')->withCount('users')->get();
        return view('employee-schedules.index', compact('departments', 'shifts', 'timetables'));
    }

    function store() 
    {
        $data = request()->validate([
            'users' => 'required|array',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Invalid data']);
        }

        // check if shift is already assigned to user
        foreach ($data['users'] as $userId) {
            $user = User::find($userId);
            $userShift = $user->shifts()->where('start_date', '<=', $data['start_date'])
                ->where('end_date', '>=', $data['end_date'])
                ->first();

            if ($userShift) {
                return response()->json(['success' => false, 'message' => 'Shift already assigned to user']);
            }
        }

        $insertData = [];
        foreach ($data['users'] as $userId) {
            $insertData[] = [
                'user_id' => $userId,
                'shift_id' => $data['shift_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => auth()->id(),
            ];  
        }

        DB::table('shift_user')->insert($insertData);

        return response()->json(['success' => true, 'message' => 'Shift assigned to user successfully']);
    }
}
