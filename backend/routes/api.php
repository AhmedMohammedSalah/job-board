<?php

use App\Http\Controllers\api\SingleJobController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\JobCommentController;
use App\Http\Controllers\JobController;

// use ApplicationController
use App\Http\Controllers\api\CandidateController;

use App\Http\Controllers\api\SkillController;
// use CandidateSkillController
use App\Http\Controllers\api\CandidateSkillController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Marwa Nasser
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::post('/applications', [ApplicationController::class, 'store']);
    Route::get('/applications/{id}', [ApplicationController::class, 'show']);
    Route::put('/applications/{id}', [ApplicationController::class, 'update']);
    Route::delete('/applications/{id}', [ApplicationController::class, 'destroy']);
    Route::get('/recently-applied', [ApplicationController::class, 'recentlyApplied']);
    Route::get('/checkApplications', [ApplicationController::class, 'checkIfExists']);
});

Route::middleware('auth:sanctum')->get('/singleJob/{id}', [SingleJobController::class, 'show']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/password/reset', [AuthController::class, 'reset']);
    // [AMS] Applications Route
    Route::get('/recently-applied', [ApplicationController::class, 'recentlyApplied']);
    // [AMS] Candidate ApiResource Route
    Route::ApiResource('candidate', CandidateController::class);
    Route::put('/auth/user', [AuthController::class, 'update']);
    // [AMS] Skills ApiRoute
    Route::ApiResource('skills', SkillController::class);
    // [AMS] Candidate Skills ApiRoute
    Route::ApiResource('candidate-skills', CandidateSkillController::class);
});
Route::post('/employers/register', [EmployerController::class, 'register']);
Route::post('/employers/login', [EmployerController::class, 'login']);

Route::post('/auth/password/email', [AuthController::class, 'sendResetEmail']);
Route::post('/auth/password/email', [AuthController::class, 'sendResetEmail']);
Route::post('/auth/password/reset', [AuthController::class, 'reset']);
// Admin routes
    Route::get('/jobs/pending', [JobController::class, 'pendingJobs'])->name('jobs.pending')->middleware('auth:sanctum'); // List pending jobs
    Route::post('/jobs/{id}/approve', [JobController::class, 'approveJob'])->name('jobs.approve'); // Approve a job
    Route::post('/jobs/{id}/reject', [JobController::class, 'rejectJob'])->name('jobs.reject'); // Reject a job

Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::post('/', [JobController::class, 'store']);
    Route::get('/{id}', [JobController::class, 'show']);
    Route::put('/{id}', [JobController::class, 'update']);
    Route::delete('/{id}', [JobController::class, 'destroy']);
    Route::patch('/{id}/toggle-active', [JobController::class, 'toggleActive']);
    // Route::get('/{jobId}/comments', [JobCommentController::class, 'index']);
    // Route::post('/{jobId}/comments', [JobCommentController::class, 'store']);
    // Route::put('/{jobId}/comments/{commentId}', [JobCommentController::class, 'update']);
    // Route::delete('/{jobId}/comments/{commentId}', [JobCommentController::class, 'destroy']);
    Route::get('/{jobId}/applications', [ApplicationController::class, 'jobApplications']);
});

Route::prefix('applications')->middleware('auth:sanctum')->group(function () {
    Route::patch('/{applicationId}/status', [ApplicationController::class, 'updateApplicationStatus']);
});