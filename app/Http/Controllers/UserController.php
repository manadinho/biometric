<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $departments = Department::whereNull('parent_id')->get();
        $users = User::with(['roles', 'department'])->get();
        return view('users.index', compact('users', 'roles', 'departments'));
    }

    public function store()
    {
        $validate = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5',
            'roles' => 'required|array',
            'department_id' => 'required|exists:departments,id'
        ];

        // if request has user_id it means we are going to update user
        if (request()->has('user_id')) {
            $validate['email'] = 'required|email|unique:users,email,' . request()->user_id;
            $validate['password'] = 'nullable|string|min:5';
        }

        $data = request()->validate($validate);

        // check if validation not passes return all errors
        if (!$data) {
            return back()->withErrors($data);
        }

        // if password is available then hash it
        if(request()->has('password')) {
            $data['password'] = bcrypt($data['password']);
        }
        
        $user = User::updateOrCreate(['id' => request('user_id')], $data);

        $user->roles()->sync(request()->roles);

        return back()->with('success', 'User created successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete yourself']);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    function fingerprintRegistered() 
    {
        $userId = request('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID is required']);
        }

        $user = User::find($userId);
        $user->is_finger_print_added = true;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Fingerprint registered successfully']);
    }
}
