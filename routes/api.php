<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('/sign-up', 'signUp');
        Route::post('/sign-in', 'signIn');
        Route::middleware('auth:sanctum')
            ->post('/sign-out', 'signOut');
    });

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('profile')
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('/', 'getProfile');
            Route::put('/', 'updateProfile');
        });

    Route::get('task-lists/{task_list}/tasks', [TaskListController::class, 'tasks']);
    Route::post('task-lists/{task_list}/tasks', [TaskListController::class, 'addTask']);

    Route::post('tasks/{task}/status', [TaskController::class, 'updateStatus']);

    Route::apiResource('task-lists', TaskListController::class);
    Route::apiResource('tasks', TaskController::class);
});
