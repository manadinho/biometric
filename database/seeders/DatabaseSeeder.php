<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // create Role for Super Admin
        Role::create(['name' => 'Super-Admin']);

        // create permissions for Super Admin
        $permissions = [
            ['key' => 'ROLES_AND_PERMISSIONS', 'name' => 'Roles and Permissions'],
            ['key' => 'DEPARTMENTS', 'name' => 'Departments'],
            ['key' => 'USERS', 'name' => 'Users'],
            ['key' => 'SHIFTS', 'name' => 'Shifts'],
            ['key' => 'DAYS', 'name' => 'Days'],
            ['key' => 'ATTENDANCE', 'name' => 'Attendance'],
            ['key' => 'REPORTS', 'name' => 'Reports'],
        ];
        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }

        // attach permissions to Super Admin Role
        $role = Role::find(1);
        $role->permissions()->sync(\App\Models\Permission::all());


        // create Admin Department for Super Admin
        Department::create(['name' => 'Admin', 'parent_id' => null]);

        // create Days 
        $days = ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'];
        foreach ($days as $day) {
            \App\Models\Day::create(['name' => $day]);
        }

        // create Super Admin
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@biometric.com',
            'password' => bcrypt('password'),
            'department_id' => 1,
        ]);

        // attach Super Admin Role to Super Admin
        $user = User::find(1);
        $user->roles()->sync([1]);
    }
}
