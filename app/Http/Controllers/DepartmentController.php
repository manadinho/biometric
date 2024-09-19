<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    function index() 
    {
        $departments = Department::whereNull('parent_id')->withCount('users')->get();

        return view('departments.index', compact('departments'));
    }

    function store()
    {
        $data = request()->validate([
            'name' => 'required|string'
        ]);

        if (!$data) {
            return back()->withErrors($data);
        }

        Department::updateOrCreate(['id' => request('department_id')], $data);

        return back()->with('success', 'Role created successfully');
    }

    function destroy(Department $department)
    {
        // check if department belongs to any user
        if ($department->users()->exists()) {
            return response()->json(['success' => false, 'message' => 'Department belongs to a user']);
        }

        $department->delete();

        return response()->json(['success' => true, 'message' => 'Department deleted successfully']);
    }

    function getUsersByDepartment() 
    {
        $id = request('department_id');

        $users = Department::find($id)->users;

        // if users are not found
        if ($users->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No user found']);
        }

        return response()->json(['success' => true, 'users' => $users]);
    }
}
