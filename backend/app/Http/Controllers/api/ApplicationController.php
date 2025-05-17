<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
}