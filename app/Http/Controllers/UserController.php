<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        return view('admin.users.create',compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $data = $request->validated();
        $data = User::verifyUpdatePassword($data);
        User::create($data);
        return redirect()->route('users.index')->with('success',true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function profile(User $user)
    {
        $this->authorize('update', $user);
        return view('admin.users.profile', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();
        $data = User::verifyUpdatePassword($data);
        $user->update($data);
        return redirect()->back()->with('success',true);
    }

    public function updatePicture(UserProfileRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->validated();
        $data = User::saveImg($data, 'profile_path', 'public/img/profile/', $user->profile_path);

        $user->update($data);
        return redirect()->back()->with('success',true);
    }

    public function deletePicture(User $user)
    {
        $this->authorize('update', $user);
        User::deleteImg($user->profile_path, 'public/img/profile/');
        $user->profile_path = "profile_default.png";
        $user->save();
        return redirect()->back()->with('success',true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success',true);
    }
}
