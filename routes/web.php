<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::middleware('guest')->group(function () {
    Route::view('/admin/login', 'admin.login')->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::view('/admin/forgot-password', 'admin.forgot')->name('password.request');
    Route::post('/admin/forgot-password', [AuthController::class, 'email'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/admin/reset-password/{token}', fn (string $token) => view('admin.reset', compact('token')))->name('password.reset');
    Route::post('/admin/reset-password', [AuthController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/schedules/reorder', [AdminController::class, 'reorderForm']);
    Route::post('/schedules/reorder', [AdminController::class, 'reorder']);
    Route::get('/settings', [AdminController::class, 'settings']);
    Route::post('/settings', [AdminController::class, 'saveSettings']);
    Route::get('/{resource}', [AdminController::class, 'index']);
    Route::get('/{resource}/create', [AdminController::class, 'form']);
    Route::get('/{resource}/{id}/edit', [AdminController::class, 'form'])->whereNumber('id');
    Route::post('/{resource}', [AdminController::class, 'save']);
    Route::put('/{resource}/{id}', [AdminController::class, 'save'])->whereNumber('id');
    Route::delete('/{resource}/{id}', [AdminController::class, 'destroy'])->whereNumber('id');
});
