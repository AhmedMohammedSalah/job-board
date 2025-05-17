<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use model job and application
use App\Models\Job;


class ApplicationController extends Controller
{
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
        //
        }

    public function recentlyApplied(Request $request){
        $user_id = Auth()->id();
        $candidate_id = $user_id ;
        $applications = Application::where('candidate_id', $candidate_id)->
        orderBy('created_at', 'desc')->get();
        $jobs =[];
        foreach ($applications as $application) {
            $jobs[] = $application-> job;
            }
        // return $applications ;
        return response ()-> json ($jobs);
    }
    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        //
    }
}
