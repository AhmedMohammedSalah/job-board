<?php

use App\Http\Controllers\api\SingleJobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
// use App\Http\Controllers\Api\EmployerController;
use App\Http\Controllers\api\JobCommentController;
use App\Http\Controllers\api\JobPostController;
use App\Http\Controllers\JobController;
// use ApplicationController
use App\Http\Controllers\api\CandidateController;

use App\Http\Controllers\api\SkillController;
// use CandidateSkillController
use App\Http\Controllers\api\CandidateSkillController;
use App\Http\Controllers\api\CategoryController;

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
    // Favorite
    Route::get('/favorite-jobs', [JobController::class, 'favoriteJobs']);
    Route::post('/favorite-jobs', [JobController::class, 'addFavorite']);
    Route::delete('/favorite-jobs/{job_id}', [JobController::class, 'removeFavorite']);
    // check if job is favorite
    Route::get('/favorite-jobs/check/{job_id}', [JobController::class, 'isFavorite']);
});


Route::post('/auth/password/email', [AuthController::class, 'sendResetEmail']);
Route::post('/auth/password/email', [AuthController::class, 'sendResetEmail']);
Route::post('/auth/password/reset', [AuthController::class, 'reset']);
// Admin routes
    Route::get('/jobs/pending', [JobController::class, 'pendingJobs'])->name('jobs.pending')->middleware('auth:sanctum'); // List pending jobs
    Route::post('/jobs/{id}/approve', [JobController::class, 'approveJob'])->name('jobs.approve'); // Approve a job
    Route::post('/jobs/{id}/reject', [JobController::class, 'rejectJob'])->name('jobs.reject'); // Reject a job

//filter
Route::get('/jobs/filter', [JobController::class, 'filterJobs']);
Route::get('/filter-options', [JobController::class, 'getFilterOptions']);

        Route::get('/jobs', [JobPostController::class, 'index'])->middleware('auth:sanctum');
        Route::post('/addjobs', [JobPostController::class, 'store'])->middleware('auth:sanctum');
        Route::get('/jobs/{id}', [JobPostController::class, 'show'])->middleware('auth:sanctum');
        Route::put('/jobs/{id}', [JobPostController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/jobs/{id}', [JobPostController::class, 'destroy'])->middleware('auth:sanctum');

        // Applications for a specific job
        Route::get('/jobs/{id}/applications', [JobPostController::class, 'applications'])->middleware('auth:sanctum');;

        // All applications across all employer jobs
        Route::get('/applications', [JobPostController::class, 'employerApplications'])->middleware('auth:sanctum');;

        // Accept/reject/update a specific application
        Route::post('/jobs/{jobId}/applications/{applicationId}/accept', [JobPostController::class, 'acceptApplication'])->middleware('auth:sanctum');
        Route::post('/jobs/{jobId}/applications/{applicationId}/reject', [JobPostController::class, 'rejectApplication'])->middleware('auth:sanctum');
        Route::put('/jobs/{jobId}/applications/{applicationId}/status', [JobPostController::class, 'updateApplicationStatus'])->middleware('auth:sanctum');


// Route::prefix('jobs')->group(function () {
//     Route::get('/', [JobController::class, 'index']);
//     Route::post('/', [JobController::class, 'store']);
//     Route::get('/{id}', [JobController::class, 'show']);
//     Route::put('/{id}', [JobController::class, 'update']);
//     Route::delete('/{id}', [JobController::class, 'destroy']);
//     Route::patch('/{id}/toggle-active', [JobController::class, 'toggleActive']);
//     // Route::get('/{jobId}/comments', [JobCommentController::class, 'index']);
//     // Route::post('/{jobId}/comments', [JobCommentController::class, 'store']);
//     // Route::put('/{jobId}/comments/{commentId}', [JobCommentController::class, 'update']);
//     // Route::delete('/{jobId}/comments/{commentId}', [JobCommentController::class, 'destroy']);
//     Route::get('/{jobId}/applications', [ApplicationController::class, 'jobApplications']);
// });

Route::prefix('applications')->middleware('auth:sanctum')->group(function () {
    Route::patch('/{applicationId}/status', [ApplicationController::class, 'updateApplicationStatus']);
});
// get_current_user
// http://localhost:8000/api/auth/get_current_user
Route::get('/auth/get_current_user', [AuthController::class, 'get_current_user'])->middleware('auth:sanctum');
// checkemail
// http://localhost:8000/api/auth/checkemail
Route::post('/auth/checkemail', [AuthController::class, 'checkemail']);

//Marwa Nasser
Route::middleware('auth:sanctum')->group(function () {
    Route::get('jobs/{job}/comments', [CommentController::class, 'index']);
    Route::post('comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
    Route::get('commentss', [CommentController::class, 'getAllComments']);
});
Route::get('/categories', [CategoryController::class, 'index']);
