<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $users = User::with('roles')->get();
        return view('users.index', ['roles' => $roles, 'users' => $users]);
    }

    public function store()
    {
        $validate = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5',
            'roles' => 'required|array',
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
}
