<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Content;
use Illuminate\Http\Request;
use App\Http\Requests\BoxSearchRequest;
use App\Http\Requests\BoxRequest;

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BoxSearchRequest $request)
    {
        if($request->search != null){
            $boxes = Box::where('name', $request->search)
            ->orWhere('name', 'like', '%' . $request->search . '%')->paginate(20);
        } else{
            $boxes = Box::paginate(20);
        }

        return view('admin.boxes.index', compact('boxes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $box = new Box();
        return view('admin.boxes.create', compact('box'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BoxRequest $request)
    {
        $data = $request->validated();
        Box::create($data);
        return redirect()->route('boxes.index')->with('success',true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function show(Box $box)
    {
        return view('admin.boxes.show', compact('box'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function edit(Box $box)
    {
        return view('admin.boxes.edit', compact('box'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function update(BoxRequest $request, Box $box)
    {
        $data = $request->validated();
        $files = $request->file('content_list');

        if($request->hasFile('content_list'))
        {
            foreach ($files as $key => $file) {
                Content::savefile($data, $key, 'content_list', 'public/boxes/files/', $file);
                unset($data['content_list'][$key]);
            }
        }

        $box->update($data);

        return redirect()->back()->with('success',true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function destroy(Box $box)
    {
        $box->delete();
        return redirect()->route('boxes.index')->with('success',true);
    }
}
