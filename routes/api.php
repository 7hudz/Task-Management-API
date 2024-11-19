<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


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
Route::get('/filter', [TaskController::class, 'filterTasks']);
Route::get('tasks/overdue', [TaskController::class, 'getTasks']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('tasks', TaskController::class);
Route::post('tasks/{task}/assign', [TaskController::class, 'assign']);
Route::post('/tasks', [TaskController::class, 'store']);
 



Route::post('register', [AuthController::class, 'register']);




