<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employer',
        ]);

        $employer = Employer::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'industry' => $request->industry,
            'website' => $request->website,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Employer registered successfully',
            'user' => $user,
            'employer' => $employer,
            'access_token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        if ($user->role !== 'employer') {
            return response()->json(['message' => 'Unauthorized. Not an employer account.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'employer' => $user->employer,
            'access_token' => $token,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $employer = $user->employer;

        return response()->json([
            'user' => $user,
            'employer' => $employer,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'company_name' => 'sometimes|string|max:255',
            'company_description' => 'sometimes|string',
            'industry' => 'sometimes|string|max:255',
            'website' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'zip_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update user info
        if ($request->has('name')) {
            $user->name = $request->name;
            $user->save();
        }

        // Update employer info
        $employer->fill($request->only([
            'company_name',
            'company_description',
            'industry',
            'website',
            'phone',
            'address',
            'city',
            'state',
            'zip_code',
            'country',
        ]));
        
        $employer->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'employer' => $employer,
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $employer = $user->employer;

        $logoPath = $request->file('logo')->store('company_logos', 'public');
        $employer->company_logo = $logoPath;
        $employer->save();

        return response()->json([
            'message' => 'Company logo uploaded successfully',
            'logo_path' => $logoPath,
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $openJobsCount = $employer->jobs()->where('is_active', true)->count();
        $savedCandidatesCount = $employer->savedCandidates()->count();
        
        $recentJobs = $employer->jobs()
            ->with('applications')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'employment_type' => $job->employment_type,
                    'status' => $job->is_active ? 'Active' : 'Expired',
                    'applications_count' => $job->applications->count(),
                    'remaining_days' => $job->expires_at ? now()->diffInDays($job->expires_at, false) : null,
                    'created_at' => $job->created_at->format('M d, Y'),
                ];
            });

        return response()->json([
            'open_jobs_count' => $openJobsCount,
            'saved_candidates_count' => $savedCandidatesCount,
            'recent_jobs' => $recentJobs,
        ]);
    }
}