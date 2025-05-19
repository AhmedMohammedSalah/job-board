<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
            return response()->json(['message' => 'Job not found'], 404);
        }

        $applications = $job->applications()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'applications' => $applications,
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
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Application::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicationRequest $request)
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
     * Display the specified resource.
     */
    public function show($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        return response()->json($application, 200);
    }

    /**
     * Update the specified resource in storage.
     */
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


}
