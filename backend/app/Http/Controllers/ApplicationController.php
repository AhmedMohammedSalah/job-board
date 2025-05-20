<?php

namespace App\Http\Controllers;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use Illuminate\Http\JsonResponse;

class ApplicationController extends Controller
{
    public function jobApplications($jobId)

    {
        $user = Auth::user();
        
        $job = Job::where('id', $jobId)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found'
            ], 404);
        }

        $applications = $job->applications()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($application) {
                // Format application data to match frontend expectations
                return [
                    'id' => $application->id,
                    'job_id' => $application->job_id,
                    'user_id' => $application->user_id,
                    'applicant_name' => $application->user->name,
                    'applicant_email' => $application->user->email,
                    'resume_url' => $application->resume_path ? url('storage/' . $application->resume_path) : null,
                    'cover_letter' => $application->cover_letter,
                    'status' => $application->status,
                    'applied_date' => $application->created_at->format('M d, Y'),
                    'employer_notes' => $application->employer_notes,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'job' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'applications_count' => $applications->count()
                ],
                'applications' => $applications
            ],
            'message' => 'Applications retrieved successfully'
        ]);
    }

    public function updateApplicationStatus(Request $request, $applicationId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewed,shortlisted,rejected,hired',
            'employer_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        $application = Application::with('job')
            ->where('id', $applicationId)
            ->whereHas('job', function ($query) use ($user) {
                $query->where('employer_id', $user->employer->id);
            })
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->status = $request->status;
        
        if ($request->has('employer_notes')) {
            $application->employer_notes = $request->employer_notes;
        }
        
        $application->save();

        return response()->json([
            'message' => 'Application status updated successfully',
            'application' => $application,
        ]);
    }

    /**
     * Accept a job application
     * 
     * @param int $applicationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptApplication($applicationId)
    {
        $user = Auth::user();
        
        // Start a database transaction for data integrity
        return DB::transaction(function () use ($applicationId, $user) {
            $application = Application::with(['job', 'user'])
                ->where('id', $applicationId)
                ->whereHas('job', function ($query) use ($user) {
                    $query->where('employer_id', $user->employer->id);
                })
                ->first();
            
            if (!$application) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Application not found'
                ], 404);
            }
            
            // Update the application status to hired
            $application->status = 'hired';
            $application->employer_notes = $application->employer_notes . "\n[" . now()->format('Y-m-d H:i:s') . "] Application accepted.";
            $application->save();
            
            // Update the job if needed (e.g., mark as filled or reduce openings)
            $job = $application->job;
            
            // Optional: You might want to mark other applications as rejected
            // Uncomment if this is desired behavior
            /*
            $job->applications()
                ->where('id', '!=', $application->id)
                ->where('status', '!=', 'rejected')
                ->update([
                    'status' => 'rejected',
                    'employer_notes' => DB::raw("CONCAT(IFNULL(employer_notes, ''), '\n[" . now()->format('Y-m-d H:i:s') . "] Application rejected as another candidate was hired.')")
                ]);
            */
            
            // Return the updated application with related user info
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
     * Display a listing of the resource.
     */

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validated();

        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $data['resume_path'] = $resumePath;
        }

        $application = Application::create($data);

        return response()->json($application, 201);
    }



    /**
     * Reject a job application
     * 
     * @param int $applicationId
     * @return \Illuminate\Http\JsonResponse
     */

    public function rejectApplication($applicationId)
    {
        $user = Auth::user();
        
        $application = Application::with(['job', 'user'])
            ->where('id', $applicationId)
            ->whereHas('job', function ($query) use ($user) {
                $query->where('employer_id', $user->employer->id);
            })
            ->first();
        
        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }
        
        // Update the application status to rejected
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
                    'job_title' => $application->job->title
                ]
            ],
            'message' => 'Application rejected successfully'
        ]);
    }

    public function show($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        return response()->json($application, 200);
    }
    
    /**
     * Get all applications for the authenticated user
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function employerApplications()
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

    public function update(UpdateApplicationRequest $request, $id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        $application->update($request->validated());

        return response()->json([
            'message' => 'Application updated successfully.',
            'data' => $application
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        $application->delete();

        return response()->json(['message' => 'Application deleted successfully.'], 200);
    }

    public function recentlyApplied(Request $request)
    {
        $id = Auth()->id();
        $applications = Application::where('candidate_id', $id)->orderBy('created_at', 'desc')->get();
        $jobs = [];

        foreach ($applications as $application) {
            $jobs[] = Job::where('id', $application->job_id)
                ->with('category')
                ->first();
        }
        // return $applications ;
        return response()->json($jobs);
    }

    //Marwa Nasser
    public function checkIfExists(Request $request)
    {
        $exists = Application::where('job_id', $request->job_id)
            ->where('candidate_id', $request->candidate_id)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}

