<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use model candidateSkill
use App\Models\candidateSkill;
class CandidateSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $id = Auth()-> id();
        $candidateSkill = candidateSkill::where('candidate_id', $id)->get();
        return response()->json($candidateSkill);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $id = Auth()->id();
        request->validate ([
            'skill_id' => 'required|integer|exists:skills,id',
            'level' => 'required|integer|in:1,2,3,4,5'
        ]);
        $candidateSkill = new candidateSkill();
        $candidateSkill-> candidate_id = $id;
        $candidateSkill-> skill_id = $request->skill_id ;
        $candidateSkill-> level = $request-> level ;
        $candidateSkill-> save();
        return response()-> json($candidateSkill )-> status(201);
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

    }
}
