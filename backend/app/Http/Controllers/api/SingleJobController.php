<?php

namespace App\Http\Controllers\aPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class SingleJobController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->role=="employer") {
            return response()->json(['message' => 'User is not an employer'], 403);
        }

        $job = Job::findOrFail($id);
        

        return response()->json([
            'job' => $job,
        ]);
    }
}