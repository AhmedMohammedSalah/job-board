<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Candidate
use App\Models\Candidate;
// User
use App\Models\User;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $id = Auth()->id();
        $candidate = Candidate::with('user')->find($id);
        // // Get user with candidate data
        // $user = User::with('candidate')->find($id);

        // // Get candidate with user data
        // $candidate = Candidate::with('user')->find($id);

        return response()-> json($candidate);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    // Verify the authenticated user owns this candidate profile
    $userId = Auth()->id();
    $candidate = Candidate::with('user')->findOrFail($id);

    if ($candidate->id !== $userId) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validatedCandidateData = $request->validate([
        'headline' => 'sometimes|string|max:255',
        'skills' => 'sometimes|string',
        'experience_years' => 'sometimes|integer|min:0',
        'linkedin_url' => 'sometimes|nullable|url'
    ]);

    // Validate user-specific fields if provided
    if ($request->has('user')) {
        $validatedUserData = $request->validate([
            'user.name' => 'sometimes|string|max:255',
            'user.email' => 'sometimes|email|unique:users,email,'.$userId
        ]);

        // Update user data
        $candidate->user->update($validatedUserData['user'] ?? []);
    }

    // Update candidate data
    $candidate->update($validatedCandidateData);

    // Return updated candidate with user data
    return response()->json([$candidate]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
