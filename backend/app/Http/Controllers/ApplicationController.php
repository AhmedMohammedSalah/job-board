<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use model job and application
use App\Models\Application;
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
        $id = Auth()->id();
        $applications = Application::where('candidate_id', $id)->
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
