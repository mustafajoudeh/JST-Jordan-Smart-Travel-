<?php

use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
Route::get('/users/{id}', [App\Http\Controllers\UserController::class, 'show']);
Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);

Route::get('/Dashboard', [AdminDashboardController::class, 'index']);


if (app()->environment('local')) {

    Route::get('/_preview/dashboard/{role}', function (string $role) {

        $roleConfig = config("jst-dashboard.roles.{$role}");

        abort_unless(is_array($roleConfig), 404);

        $page = request()->query('page', 'overview');

        // Collect direct links and grouped links.
        $allItems = collect($roleConfig['navigation'])
            ->flatMap(function ($entry) {

                return ($entry['type'] ?? null) === 'group'
                    ? ($entry['items'] ?? [])
                    : [$entry];

            });

        $currentItem = $allItems->firstWhere('key', $page);

        abort_unless($currentItem, 404);

        return view('dashboard.preview', [

            'brand' => config('jst-dashboard.brand'),

            'roleKey' => $role,

            'roleConfig' => $roleConfig,

            'activePage' => $currentItem['key'],

            'pageTitle' => $currentItem['label'],

        ]);

    })->name('dashboard.preview');

}