<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavoriteJobRequest;
use App\Http\Requests\UpdateFavoriteJobRequest;
use App\Models\FavoriteJob;

class FavoriteJobController extends Controller
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
    public function store(StoreFavoriteJobRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FavoriteJob $favoriteJob)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFavoriteJobRequest $request, FavoriteJob $favoriteJob)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FavoriteJob $favoriteJob)
    {
        //
    }
}
