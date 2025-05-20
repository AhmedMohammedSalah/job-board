<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function update(Request $request, $id)
    {
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        $job = Job::where('id', $id)
            ->where('employer_id', $user->employer->id)
            ->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        // Update expiration date if deadline is updated
        if ($request->has('deadline')) {
            $job->expires_at = Carbon::parse($request->deadline);
        }

        $job->fill($request->all());
        $job->save();

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job,
        ]);
    }
    public function pendingJobs()
    {
        // $this->authorize('admin', User::class);
        
        $jobs = Job::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($jobs);
        //  return response()->json("jobs");
    }

    public function approveJob($id)
    {
        // $this->authorize('admin', User::class);
        
        // return $this->update(new Request([
        //     'status' => 'approved'
        // ]), $id);
        $job = Job::find($id);
        $job-> status = 'published';
        $job-> save();
        return response()-> json(['message' => 'Job approved successfully',$job]);
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
    ///filter
//     public function filterJobs(Request $request)
// {
//     $query = Job::with('category') // Eager load the category relationship
//               ->where('status', 'published');

//     // Search filter
//     if ($request->has('search')) {
//         $searchTerm = $request->input('search');
//         $query->where(function($q) use ($searchTerm) {
//             $q->where('title', 'like', '%'.$searchTerm.'%')
//               ->orWhere('description', 'like', '%'.$searchTerm.'%')
//               ->orWhere('location', 'like', '%'.$searchTerm.'%');
//         });
//     }

//     // Category filter - now using category_id
//     if ($request->has('category') && $request->input('category') != 'All Category') {
//         $query->whereHas('category', function($q) use ($request) {
//             $q->where('name', $request->input('category'));
//         });
//     }

//     // Job Type filter
//     if ($request->has('work_type')) {
//         $query->where('work_type', $request->input('work_type'));
//     }

//     // Salary range filter - adjusted for min_salary/max_salary columns
//     if ($request->has('salary_min') || $request->has('salary_max')) {
//         $min = $request->input('salary_min', 0);
//         $max = $request->input('salary_max', PHP_INT_MAX);
        
//         $query->where(function($q) use ($min, $max) {
//             $q->where(function($q) use ($min, $max) {
//                 $q->where('min_salary', '>=', $min)
//                   ->where('min_salary', '<=', $max);
//             })->orWhere(function($q) use ($min, $max) {
//                 $q->where('max_salary', '>=', $min)
//                   ->where('max_salary', '<=', $max);
//             });
//         });
//     }

//     // Remote job filter
//     if ($request->has('remote_job')) {
//         $query->where('work_type', 'remote');
//     }

//     $jobs = $query->orderBy('created_at', 'desc')->paginate(10);

//     return response()->json([
//         'jobs' => $jobs,
//     ]);
// }


//filter 2
// app/Http/Controllers/JobController.php
// public function index()
// {
//     $jobs = Job::with(['employer', 'category'])
//               ->where('status', 'active')
//               ->latest()
//               ->get();

//     return response()->json($jobs);
// }

// 

public function getFilterOptions()
    {
        return response()->json([
            'categories' => Category::select('id', 'name')->get(),
            'job_types' => Job::select('work_type')
                            ->distinct()
                            ->whereNotNull('work_type')
                            ->get()
                            ->pluck('work_type'),
            'salary_ranges' => [
                'min' => Job::min('min_salary') ?? 0,
                'max' => Job::max('max_salary') ?? 100000
            ]
        ]);
    }

    public function filterJobs(Request $request)
    {
        $query = Job::with(['category', 'employer'])
                  ->where('status', 'published');

        // Search filter
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%')
                  ->orWhereHas('employer', function($q) use ($request) {
                      $q->where('name', 'like', '%'.$request->search.'%');
                  });
            });
        }

        // Category filter
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Job Type filter
        if ($request->has('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        // Salary range filter
        if ($request->has('min_salary') || $request->has('max_salary')) {
            $min = $request->min_salary ?? 0;
            $max = $request->max_salary ?? PHP_INT_MAX;
            
            $query->where(function($q) use ($min, $max) {
                $q->where(function($q) use ($min, $max) {
                    $q->where('min_salary', '<=', $max)
                      ->where('max_salary', '>=', $min);
                });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }
}