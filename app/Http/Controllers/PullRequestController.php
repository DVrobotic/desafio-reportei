<?php

namespace App\Http\Controllers;

use App\Models\PullRequest;
use App\Http\Requests\StorePullRequestRequest;
use App\Http\Requests\UpdatePullRequestRequest;

class PullRequestController extends Controller
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
     * @param  \App\Http\Requests\StorePullRequestRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePullRequestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PullRequest  $pullRequest
     * @return \Illuminate\Http\Response
     */
    public function show(PullRequest $pullRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PullRequest  $pullRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(PullRequest $pullRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePullRequestRequest  $request
     * @param  \App\Models\PullRequest  $pullRequest
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePullRequestRequest $request, PullRequest $pullRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PullRequest  $pullRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(PullRequest $pullRequest)
    {
        //
    }
}
