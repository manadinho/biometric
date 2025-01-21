<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        // getting roles with permissions relationship
        $roles = Role::with('permissions')->get();

        // getting permissions
        $permissions = Permission::get();
        return view('roles.index', ['roles' => $roles, 'permissions' => $permissions]);
    }

    public function store() 
    {
        // validate request
        $data = request()->validate([
            'name' => 'required',
            'permissions' => 'required|array'
        ], [
            'name.required' => 'Role name is required',
            'permissions.required' => 'Please select at least one permission'
        ]);

        // check if validation passes
        if (!$data) {
            // return all validation errors
            return back()->withErrors($data);
        }

        // create or update role
        $role = Role::updateOrCreate(['id' => request('id')], [
            'name' => $data['name'],
            'created_by' => auth()->id(),
        ]);
        
        $role->permissions()->sync($data['permissions']);

        return back()->with('success', 'Role created successfully');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return response()->json(['success' => false, 'message' => 'Role cannot be deleted because it is assigned to some users']);
        }
        
        $role->delete();
        return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
    }
}
