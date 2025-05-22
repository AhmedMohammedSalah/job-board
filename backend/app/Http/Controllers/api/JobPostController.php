<?php
namespace App\Http\Controllers\api;
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

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
// use ApplicationStatus;
use App\Enums\ApplicationStatus;

// use Illuminate\Support\Facades\Auth;


class JobPostController extends Controller
{
    /**
     * Display all jobs for the employer
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role!="employer") {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $jobs = Job::where('employer_id', $user->id)
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
                    'created_at' => $job->created_at,
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
        if ($user->role!="employer") {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'work_type' => 'required|in:remote,onsite,hybrid',
            'location' => 'required|string',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric',
            'deadline' => 'required|date',
            'status' => 'nullable|in:draft,pending,published,expired',
        ]);

        $job = Job::create([
            'employer_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'],
            'requirements' => $validated['requirements'],
            'benefits' => $validated['benefits'] ?? null,
            'work_type' => $validated['work_type'],
            'location' => $validated['location'],
            'min_salary' => $validated['min_salary'] ?? null,
            'max_salary' => $validated['max_salary'] ?? null,
            'deadline' => $validated['deadline'],
            'status' => $validated['status'] ?? 'draft',
        ]);

        return response()->json(['message' => 'Job created successfully', 'job' => $job], 201);
    }

    /**
     * Display the specified job
     */
    public function show(string $id)
    {
        $user = Auth::user();

        if ($user->role!="employer") {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $id)
            ->where('employer_id', $user->id)
            ->with(['applications', 'category'])
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
    public function update(Request $request, $id)
    {
        $job = Job::where('id', $id)->where('employer_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'responsibilities' => 'sometimes|string',
            'requirements' => 'sometimes|string',
            'benefits' => 'nullable|string',
            'work_type' => 'sometimes|in:remote,onsite,hybrid',
            'location' => 'sometimes|string',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric',
            'deadline' => 'sometimes|date',
            'status' => 'sometimes|in:draft,pending,published,expired',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        }

        $job->update($validated);

        return response()->json(['message' => 'Job updated successfully', 'job' => $job]);
    }

    /**
     * Delete the specified job
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if ($user->role!="employer") {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        $job = Job::where('id', $id)
            ->where('employer_id', $user->id)
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

        if ($user->role!="employer") {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        // Find the job with different conditions based on user role
        if ($user->role=="admin") {
            $job = Job::find($id);
        } else {
            $job = Job::where('id', $id)
                ->where('employer_id', $user->id)
                ->first();
        }

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $job->applications,
            'message' => 'Applications retrieved successfully'
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

        if ($user->role!="employer") {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Not an employer account.'
            ], 403);
        }

        return DB::transaction(function () use ($jobId, $applicationId, $user) {
            $job = Job::where('id', $jobId)
                ->where('employer_id', $user->id)
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

            $application->status = ApplicationStatus::ACCEPTED->value;
            // $application->employer_notes = $application->employer_notes . "\n[" . now()->format('Y-m-d H:i:s') . "] Application accepted.";
            $application->save();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'application' => $application
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

            if ($user->role != "employer") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Not an employer account.'
                ], 403);
            }

            return DB::transaction(function () use ($jobId, $applicationId, $user) {
                $job = Job::where('id', $jobId)
                    ->where('employer_id', $user->id)
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

                $application->status = ApplicationStatus::REJECTED->value;
                $application->save();

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'application' => $application
                    ],
                    'message' => 'Application rejected successfully'
                ]);
            });
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
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            if ($user->role != "employer") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Not an employer account.'
                ], 403);
            }

            return DB::transaction(function () use ($request, $jobId, $applicationId, $user) {
                $job = Job::where('id', $jobId)
                    ->where('employer_id', $user->id)
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

                $application->status = $request->status;

                if ($request->filled('employer_notes')) {
                    $application->employer_notes = $request->employer_notes;
                }

                $application->save();

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'application' => $application
                    ],
                    'message' => 'Application status updated successfully'
                ]);
            });
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
