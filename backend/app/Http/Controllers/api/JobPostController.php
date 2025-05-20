<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


// use Illuminate\Support\Facades\Auth;


class JobPostController extends Controller
{
    /**
     * Display all jobs for the employer
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $jobs = Job::where('employer_id', $user->employer->id)
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'location' => $job->location,
                    'type' => $job->type,
                    'salary' => $job->salary,
                    'status' => $job->status ?? ($job->is_active ? 'active' : 'inactive'),
                    'closing_date' => $job->closing_date ? date('Y-m-d', strtotime($job->closing_date)) : null,
                    'applications_count' => $job->applications_count,
                    'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                    'is_remote' => $job->is_remote,
                    'experience_level' => $job->experience_level,
                    'work_type' => $job->work_type,
                    'employment_type' => $job->employment_type,
                    'salary_min' => $job->salary_min,
                    'salary_max' => $job->salary_max,
                    'benefits' => $job->benefits,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $jobs,
            'message' => 'Jobs retrieved successfully'
        ]);
    }

    /**
     * Store a newly created job
     */
    // Duplicate index() method removed to fix redeclaration error.

    // /**
    //  * Store a newly created job
    //  */
    // public function index()
    // {
    //     $user = Auth::user();
    //     $employer = $user->employer;

    //     $jobs = $employer->jobs()
    //         ->withCount('applications')
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return response()->json([
    //         'jobs' => $jobs,
    //     ]);
    // }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only employers can post jobs'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|array',
            'location' => 'required|string|max:255',
            'requirements' => 'nullable|string',
            'location' => 'required|string',
            'salary' => 'nullable|numeric',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'type' => 'required|in:full-time,part-time,contract,freelance',
            'employment_type' => 'required|string|max:100',
            'work_type' => 'required|in:remote,onsite,hybrid',
            'closing_date' => 'nullable|date',
            'is_remote' => 'boolean',
            'experience_level' => 'nullable|in:entry,mid,senior',
            'benefits' => 'nullable|string',
            'technologies' => 'nullable|array',
            'category' => 'required|string|max:100',
            'deadline' => 'nullable|date',
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

        // Calculate expiration date
        $expiresAt = $request->closing_date
            ? Carbon::parse($request->closing_date)
            : Carbon::now()->addDays(30);

        $job = new Job($validator->validated());
        $job->employer_id = $user->employer->id;
        $job->status = 'published';
        $job->is_active = true;
        $job->expires_at = $expiresAt;
        $job->save();

        return response()->json([
            'status' => 'success',
            'data' => $job,
            'message' => 'Job posted successfully'
        ], 201);
    }

    /**
     * Display the specified job
     */
    public function show(string $id)
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $id)
            ->where('employer_id', $user->employer->id)
            ->with(['applications.user', 'comments.user'])
            ->withCount('applications')
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $job,
            'message' => 'Job retrieved successfully'
        ]);
    }

    /**
     * Update the specified job
     */

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
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'sometimes|array',
            'location' => 'sometimes|string|max:255',
            'requirements' => 'nullable|string',
            'location' => 'sometimes|string',
            'salary' => 'nullable|numeric',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'type' => 'sometimes|in:full-time,part-time,contract,freelance',
            'employment_type' => 'sometimes|string|max:100',
            'work_type' => 'sometimes|in:remote,onsite,hybrid',
            'closing_date' => 'nullable|date',
            'status' => 'sometimes|in:draft,published,closed,expired,pending,approved,rejected',
            'is_remote' => 'sometimes|boolean',
            'experience_level' => 'sometimes|in:entry,mid,senior',
            'benefits' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'category' => 'sometimes|string|max:100',
            'technologies' => 'nullable|array',
            'deadline' => 'nullable|date',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $id)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        // Update expiration date if closing_date is updated
        if ($request->has('closing_date')) {
            $job->expires_at = Carbon::parse($request->closing_date);
        }

        $job->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'data' => $job,
            'message' => 'Job updated successfully'
        ]);
    }

    /**
     * Delete the specified job
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $id)
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
     * Get all applications for a specific job
     */
    public function applications(string $id)
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
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

    /**
     * Get all applications for the employer
     */
    public function allApplications()
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $applications = Application::with(['job', 'user'])
            ->whereHas('job', function ($query) use ($user) {
                $query->where('employer_id', $user->employer->id);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'job_id' => $application->job_id,
                    'job_title' => $application->job->title,
                    'applicant_name' => $application->user->name,
                    'applicant_email' => $application->user->email,
                    'status' => $application->status,
                    'applied_date' => $application->created_at->format('M d, Y'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $applications,
            'message' => 'All applications retrieved successfully'
        ]);
    }

    /**
     * Accept a job application
     */
    public function acceptApplication(string $jobId, string $applicationId)
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        return DB::transaction(function () use ($jobId, $applicationId, $user) {
            $job = Job::where('id', $jobId)
                ->where('employer_id', $user->employer->id)
                ->first();

            if (!$job) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Job not found or not owned by employer'
                ], 404);
            }

            $application = Application::where('id', $applicationId)
                ->where('job_id', $jobId)
                ->with('user')
                ->first();

            if (!$application) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Application not found'
                ], 404);
            }

            $application->status = 'hired';
            $application->employer_notes = $application->employer_notes . "\n[" . now()->format('Y-m-d H:i:s') . "] Application accepted.";
            $application->save();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'application' => [
                        'id' => $application->id,
                        'status' => $application->status,
                        'applicant_name' => $application->user->name,
                        'applicant_email' => $application->user->email,
                        'job_title' => $job->title
                    ]
                ],
                'message' => 'Application accepted successfully'
            ]);
        });
    }

    /**
     * Reject a job application
     */
    public function rejectApplication(string $jobId, string $applicationId)
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $jobId)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        $application = Application::where('id', $applicationId)
            ->where('job_id', $jobId)
            ->with('user')
            ->first();

        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        $application->status = 'rejected';
        $application->employer_notes = $application->employer_notes . "\n[" . now()->format('Y-m-d H:i:s') . "] Application rejected.";
        $application->save();

        return response()->json([
            'status' => 'success',
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'applicant_name' => $application->user->name,
                    'job_title' => $job->title
                ]
            ],
            'message' => 'Application rejected successfully'
        ]);
    }

    /**
     * Update application status
     */
    public function updateApplicationStatus(Request $request, string $jobId, string $applicationId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewed,shortlisted,rejected,hired',
            'employer_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $jobId)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
        }

        $application = Application::where('id', $applicationId)
            ->where('job_id', $jobId)
            ->first();

        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        $application->status = $request->status;

        if ($request->has('employer_notes')) {
            $application->employer_notes = $request->employer_notes;
        }

        $application->save();

        return response()->json([
            'status' => 'success',
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'status' => $application->status,
                ]
            ],
            'message' => 'Application status updated successfully'
        ]);
    }

    /**
     * Toggle job active status
     */

    
        
    public function toggleActive(string $id)
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $id)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found or not owned by employer'
            ], 404);
            return response()->json(['message' => 'Job not found'], 404);
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
    public function markAsExpired(string $id)
    {
        $user = Auth::user();

        if (!$user->employer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $id)
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
            'message' => 'Job marked as expired',
            'is_active' => $job->is_active,
            'message' => 'Job marked as expired'
        ]);
    }
}
