<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Auth;


class JobController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employer = $user->employer;

        $jobs = $employer->jobs()
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'jobs' => $jobs,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'technologies' => 'nullable|array',
            'work_type' => 'required|in:remote,onsite,hybrid',
            'employment_type' => 'required|string|max:100',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $employer = $user->employer;

        // Calculate expiration date (default to 30 days if not specified)
        $expiresAt = $request->deadline 
            ? Carbon::parse($request->deadline) 
            : Carbon::now()->addDays(30);

        $job = new Job([
            'title' => $request->title,
            'description' => $request->description,
            'responsibilities' => $request->responsibilities,
            'requirements' => $request->requirements,
            'category' => $request->category,
            'location' => $request->location,
            'technologies' => $request->technologies,
            'work_type' => $request->work_type,
            'employment_type' => $request->employment_type,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'benefits' => $request->benefits,
            'deadline' => $request->deadline,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        $employer->jobs()->save($job);

        return response()->json([
            'message' => 'Job posted successfully',
            'job' => $job,
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $job = Job::where('id', $id)
            ->where('employer_id', $user->employer->id)
            ->with(['applications.user', 'comments.user'])
            ->withCount('applications')
            ->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json([
            'job' => $job,
        ]);
    }

    // public function update(Request $request, $id)
    // {
        
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'sometimes|string|max:255',
    //         'description' => 'sometimes|string',
    //         'responsibilities' => 'nullable|string',
    //         'requirements' => 'nullable|string',
    //         'category' => 'sometimes|string|max:100',
    //         'location' => 'sometimes|string|max:255',
    //         'technologies' => 'nullable|array',
    //         'work_type' => 'sometimes|in:remote,onsite,hybrid',
    //         'employment_type' => 'sometimes|string|max:100',
    //         'salary_min' => 'nullable|numeric',
    //         'salary_max' => 'nullable|numeric',
    //         'benefits' => 'nullable|string',
    //         'deadline' => 'nullable|date',
    //         'is_active' => 'sometimes|boolean',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $user = Auth::user();
        
    //     $job = Job::where('id', $id)
    //         ->where('employer_id', $user->employer->id)
    //         ->first();

    //     if (!$job) {
    //         return response()->json(['message' => 'Job not found'], 404);
    //     }

    //     // Update expiration date if deadline is updated
    //     if ($request->has('deadline')) {
    //         $job->expires_at = Carbon::parse($request->deadline);
    //     }

    //     $job->fill($request->all());
    //     $job->save();

    //     return response()->json([
    //         'message' => 'Job updated successfully',
    //         'job' => $job,
    //     ]);
    // }
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // Common validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'category' => 'sometimes|string|max:100',
            'location' => 'sometimes|string|max:255',
            'technologies' => 'nullable|array',
            'work_type' => 'sometimes|in:remote,onsite,hybrid',
            'employment_type' => 'sometimes|string|max:100',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
            'status' => 'sometimes|in:pending,approved,rejected', // Added for admin
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500' // Added for admin
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the job with different conditions based on user role
        if ($user->isAdmin()) {
            $job = Job::find($id);
        } else {
            $job = Job::where('id', $id)
                ->where('employer_id', $user->employer->id)
                ->first();
        }

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        // Admin-specific updates
        if ($user->isAdmin() && $request->has('status')) {
            $updateData = ['status' => $request->status];
            
            if ($request->status === 'approved') {
                $updateData['approved_at'] = now();
                $updateData['is_active'] = true;
            } elseif ($request->status === 'rejected') {
                $updateData['rejection_reason'] = $request->rejection_reason;
                $updateData['is_active'] = false;
            }
            
            $job->update($updateData);
            
            // Here you would add notification logic
            return response()->json([
                'message' => 'Job status updated successfully',
                'job' => $job,
            ]);
        }

        // Original employer update logic
        if ($request->has('deadline')) {
            $job->expires_at = Carbon::parse($request->deadline);
        }

        // When employer updates, set status back to pending if it was rejected
        if (!$user->isAdmin() && $job->status === 'rejected') {
            $job->status = 'pending';
            $job->rejection_reason = null;
        }

        $job->fill($request->except(['status', 'rejection_reason'])); // Prevent employers from changing these
        $job->save();

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job,
        ]);
    }
    public function pendingJobs()
    {
        $this->authorize('admin', User::class);
        
        $jobs = Job::where('status', 'pending')
            ->with('employer.user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($jobs);
         return response()->json("jobs");
    }

    public function approveJob($id)
    {
        $this->authorize('admin', User::class);
        
        return $this->update(new Request([
            'status' => 'approved'
        ]), $id);
    }

    public function rejectJob(Request $request, $id)
    {
        $this->authorize('admin', User::class);
        
        return $this->update(new Request([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]), $id);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        
        $job = Job::where('id', $id)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully',
        ]);
    }

    public function toggleActive($id)
    {
        $user = Auth::user();
        
        $job = Job::where('id', $id)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $job->is_active = !$job->is_active;
        $job->save();

        return response()->json([
            'message' => 'Job status updated successfully',
            'is_active' => $job->is_active,
        ]);
    }
}