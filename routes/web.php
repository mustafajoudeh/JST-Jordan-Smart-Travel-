<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/admin/dashboard', 'dashboard.index')->name('admin.dashboard');
Route::view('/admin/governorates', 'dashboard.management.index', ['module' => 'governorates'])->name('admin.governorates');
Route::view('/admin/categories', 'dashboard.management.index', ['module' => 'categories'])->name('admin.categories');
Route::view('/admin/platform-settings', 'dashboard.management.index', ['module' => 'platform-settings'])->name('admin.platform-settings');
Route::view('/workspace', 'auth.workspace')->name('workspace');

Route::middleware(['auth:sanctum', 'active', 'role:system_admin'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::get('/users/{id}', [App\Http\Controllers\UserController::class, 'show']);
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);
});
