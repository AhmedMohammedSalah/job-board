<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\JobCommentController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\EmployerJobController;

// use ApplicationController
use App\Http\Controllers\api\CandidateController;

use App\Http\Controllers\api\SkillController;
// use CandidateSkillController
use App\Http\Controllers\api\CandidateSkillController;

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
    // [AMS] Skills ApiRoute
    Route::ApiResource('skills', SkillController::class);
    // [AMS] Candidate Skills ApiRoute
    Route::ApiResource('candidate-skills', CandidateSkillController::class);
});

// Employer Public Routes (unchanged)
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

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('employers')->group(function () {
        Route::get('/profile', [EmployerController::class, 'profile']);
        Route::put('/profile', [EmployerController::class, 'updateProfile']);
        Route::post('/logo', [EmployerController::class, 'uploadLogo']);
        Route::get('/dashboard', [EmployerController::class, 'dashboard']);
    });
    
    // Employer Job Management Routes
    Route::prefix('employer/jobs')->group(function () {
        Route::post('/', [EmployerJobController::class, 'store']);
        Route::put('/{jobId}', [EmployerJobController::class, 'update']);
        Route::delete('/{jobId}', [EmployerJobController::class, 'destroy']);
        Route::patch('/{jobId}/toggle-active', [EmployerJobController::class, 'toggleActive']);
        Route::patch('/{jobId}/mark-expired', [EmployerJobController::class, 'markAsExpired']);
    });
    
    Route::prefix('applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'employerApplications'])->name('employer.applications');
        Route::patch('/{applicationId}/status', [ApplicationController::class, 'updateApplicationStatus'])
            ->name('applications.update-status');
        Route::put('/{applicationId}/accept', [ApplicationController::class, 'acceptApplication'])
            ->name('applications.accept');
        Route::put('/{applicationId}/reject', [ApplicationController::class, 'rejectApplication'])
            ->name('applications.reject');
    });
});