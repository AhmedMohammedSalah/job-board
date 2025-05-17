<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
// use ApplicationController
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\api\CandidateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/applications', [ApplicationController::class, 'index']);
Route::post('/applications', [ApplicationController::class, 'store']);
Route::get('/applications/{id}', [ApplicationController::class, 'show']);
Route::put('/applications/{id}', [ApplicationController::class, 'update']);
Route::delete('/applications/{id}', [ApplicationController::class, 'destroy']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
Route::post('/auth/password/reset', [AuthController::class, 'reset']);
    // [AMS] Applications Route
    Route::get('/recently-applied',[ApplicationController::class,'recentlyApplied']);
    // [AMS] Candidate ApiResource Route
    Route::ApiResource ('candidate',CandidateController::class);
    Route::put('/auth/user', [AuthController::class, 'update']);
});
Route::post('/auth/password/email', [AuthController::class, 'sendResetEmail']);
Route::post('/auth/password/email', [AuthController::class, 'sendResetEmail']);
Route::post('/auth/password/reset', [AuthController::class, 'reset']);

