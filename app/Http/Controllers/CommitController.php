<?php

namespace App\Http\Controllers;

use App\Models\Commit;
use App\Http\Requests\StoreCommitRequest;
use App\Http\Requests\UpdateCommitRequest;

class CommitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommitRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Commit  $commit
     * @return \Illuminate\Http\Response
     */
    public function show(Commit $commit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Commit  $commit
     * @return \Illuminate\Http\Response
     */
    public function edit(Commit $commit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommitRequest  $request
     * @param  \App\Models\Commit  $commit
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommitRequest $request, Commit $commit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Commit  $commit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commit $commit)
    {
        //
    }
}
