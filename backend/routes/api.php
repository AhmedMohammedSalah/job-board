<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\JobCommentController;
use App\Http\Controllers\JobController;
// use ApplicationController
use App\Http\Controllers\api\CandidateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/user', [AuthController::class, 'update']);
    // [AMS] Applications Route
    Route::get('/recently-applied',[ApplicationController::class,'recentlyApplied']);
    // [AMS] Candidate ApiResource Route
    Route::ApiResource ('candidate',CandidateController::class);


});
Route::post('/employers/register', [EmployerController::class, 'register']);
Route::post('/employers/login', [EmployerController::class, 'login']);

Route::post('/auth/password/email', [AuthController::class, 'sendResetEmail']);
Route::post('/auth/password/reset', [AuthController::class, 'reset']);


Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::post('/', [JobController::class, 'store']);
    Route::get('/{id}', [JobController::class, 'show']);
    Route::put('/{id}', [JobController::class, 'update']);
    Route::delete('/{id}', [JobController::class, 'destroy']);
    Route::patch('/{id}/toggle-active', [JobController::class, 'toggleActive']);
    Route::get('/{jobId}/comments', [JobCommentController::class, 'index']);
    Route::post('/{jobId}/comments', [JobCommentController::class, 'store']);
    Route::put('/{jobId}/comments/{commentId}', [JobCommentController::class, 'update']);
    Route::delete('/{jobId}/comments/{commentId}', [JobCommentController::class, 'destroy']);
    Route::get('/{jobId}/applications', [ApplicationController::class, 'jobApplications']);

});

Route::prefix('applications')->group(function () {
    Route::patch('/{applicationId}/status', [ApplicationController::class, 'updateApplicationStatus']);
});






