<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;

class EmployeeTransferController extends Controller
{
    public function index()
    {
        $departments = Department::whereNull('parent_id')->withCount('users')->get();

        return view('employee-transfer.index', compact('departments'));
    }

    public function action()
    {
        $data = request()->validate([
            'users' => 'required|array',
            'department' => 'required|exists:departments,id'
        ]);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Invalid data']);
        }

        User::whereIn('id', $data['users'])->update(['department_id' => $data['department']]);

        return response()->json(['success' => true, 'message' => 'Employee transferred successfully']);
    }
}
