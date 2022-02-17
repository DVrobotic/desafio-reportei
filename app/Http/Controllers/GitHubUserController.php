<?php

namespace App\Http\Controllers;

use App\Models\GitHubUser;
use App\Http\Requests\StoreGitHubUserRequest;
use App\Http\Requests\UpdateGitHubUserRequest;

class GitHubUserController extends Controller
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
     * @param  \App\Http\Requests\StoreGitHubUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGitHubUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GitHubUser  $gitHubUser
     * @return \Illuminate\Http\Response
     */
    public function show(GitHubUser $gitHubUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GitHubUser  $gitHubUser
     * @return \Illuminate\Http\Response
     */
    public function edit(GitHubUser $gitHubUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateGitHubUserRequest  $request
     * @param  \App\Models\GitHubUser  $gitHubUser
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGitHubUserRequest $request, GitHubUser $gitHubUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GitHubUser  $gitHubUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(GitHubUser $gitHubUser)
    {
        //
    }
}
