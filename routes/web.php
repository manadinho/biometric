<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeTransferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROLES ROUTES
    Route::group(['prefix' => 'roles', 'as' => 'roles.'], function(){
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // DEPARTMENTS ROUTES
    Route::group(['prefix' => 'departments', 'as' => 'departments.'], function(){
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/store', [DepartmentController::class, 'store'])->name('store');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::post('/users-by-department', [DepartmentController::class, 'getUsersByDepartment'])->name('users-by-department');
    });

    // USERS ROUTES
    Route::group(['prefix' => 'users', 'as' => 'users.'], function(){
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // EMPLOYEE TRANSFER ROUTES
    Route::group(['prefix' => 'employee-transfers', 'as' => 'employee-transfers.'], function(){
        Route::get('/', [EmployeeTransferController::class, 'index'])->name('index');
        Route::post('/', [EmployeeTransferController::class, 'action'])->name('action');
    });

    // TIMETABLE ROUTES
    Route::group(['prefix' => 'timetables', 'as' => 'timetables.'], function(){
        Route::get('/', [TimetableController::class, 'index'])->name('index');
        Route::post('/store', [TimetableController::class, 'store'])->name('store');
        Route::delete('/{timetable}', [TimetableController::class, 'destroy'])->name('destroy');
    });
});

Route::get('/device-info', function () {
    return response()->json(['status' => 'success', 'message' => 'API is reachable']);
});

require __DIR__.'/auth.php';
