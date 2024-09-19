<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    function index() 
    {
        $parentDepartments = Department::whereNull('parent_id')->get();
        $departments = Department::whereNull('parent_id')
        ->with('children', function($query){
            $query->withCount('users');
        })
        ->get();

        return view('departments.index', compact('departments', 'parentDepartments'));
    }

    function store()
    {
        $data = request()->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:departments,id',
        ], [
            'parent_id.exists' => 'The selected parent department is invalid',
        ]);

        Department::updateOrCreate(['id' => request('id')], $data);

        return back()->with('success', 'Role created successfully');
    }
}
