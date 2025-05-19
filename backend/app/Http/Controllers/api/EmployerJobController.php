<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployerJobController extends Controller
{
    /**
     * Create a new job posting
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'salary' => 'nullable|numeric',
            'type' => 'required|in:full-time,part-time,contract,freelance',
            'closing_date' => 'nullable|date',
            'requirements' => 'nullable|array',
            'is_remote' => 'boolean',
            'experience_level' => 'nullable|in:entry,mid,senior'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only employers can post jobs'
            ], 403);
        }

        $job = new Job($validator->validated());
        $job->employer_id = $user->employer->id;
        $job->save();

        return response()->json([
            'status' => 'success',
            'data' => $job,
            'message' => 'Job posted successfully'
        ], 201);
    }

    /**
     * Update a job posting
     */
    public function update(Request $request, $jobId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string',
            'salary' => 'nullable|numeric',
            'type' => 'sometimes|in:full-time,part-time,contract,freelance',
            'closing_date' => 'nullable|date',
            'status' => 'sometimes|in:draft,published,closed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $job = Job::where('id', $jobId)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        $job->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'data' => $job,
            'message' => 'Job updated successfully'
        ]);
    }

    /**
     * Delete a job posting
     */
    public function destroy($jobId)
    {
        $user = Auth::user();
        $job = Job::where('id', $jobId)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        $job->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Job deleted successfully'
        ]);
    }

    /**
     * Toggle job active status
     */
    public function toggleActive($jobId)
    {
        $user = Auth::user();
        $job = Job::where('id', $jobId)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        $job->is_active = !$job->is_active;
        $job->save();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $job->id,
                'is_active' => $job->is_active,
                'title' => $job->title
            ],
            'message' => 'Job status updated'
        ]);
    }

    /**
     * Mark job as expired
     */
    public function markAsExpired($jobId)
    {
        $user = Auth::user();
        $job = Job::where('id', $jobId)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        $job->status = 'expired';
        $job->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Job marked as expired'
        ]);
    }
}