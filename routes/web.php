<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\EmployeeTransferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
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
    return redirect('/dashboard');
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
        Route::post('/fingerprint-registered', [UserController::class, 'fingerprintRegistered'])->name('fingerprint-registered');
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

    // SHIFT ROUTES
    Route::group(['prefix' => 'shifts', 'as' => 'shifts.'], function(){
        Route::get('/', [ShiftController::class, 'index'])->name('index');
        Route::post('/store', [ShiftController::class, 'store'])->name('store');
        Route::delete('/{shift}', [ShiftController::class, 'destroy'])->name('destroy');
        Route::post('/add-timetable', [ShiftController::class, 'addTimetable'])->name('timetables');
    });

    // EMPLOYEE SCHDULE ROUTES
    Route::group(['prefix' => 'employee-schedules', 'as' => 'employee-schedules.'], function(){
        Route::get('/', [EmployeeScheduleController::class, 'index'])->name('index');
        Route::post('/store', [EmployeeScheduleController::class, 'store'])->name('store');
        Route::post('/destroy', [EmployeeScheduleController::class, 'destroy'])->name('destroy');
    });
});

Route::get('/device-info', function () {
    return response()->json(['status' => 'success', 'message' => 'API is reachable']);
});

require __DIR__.'/auth.php';
