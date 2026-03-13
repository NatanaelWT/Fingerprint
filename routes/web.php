<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\ExportStaffController;

Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
Route::get('/staff/calendar', [StaffController::class, 'calendar'])->name('staff.calendar');
Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
Route::get('/staff/{staff}/export/month', [ExportStaffController::class, 'exportStaffMonth'])->name('staff.export.staff-month')->whereNumber('staff');
Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('staff.show')->whereNumber('staff');
Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
Route::get('/staff/export', [ExportStaffController::class, 'export'])->name('staff.export');
Route::get('/staff/export/month', [ExportStaffController::class, 'exportMonth'])->name('staff.export.month');


Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
