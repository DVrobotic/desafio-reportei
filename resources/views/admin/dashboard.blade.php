@extends('admin.layouts.sistema')

@section('title', 'Dashboard')

@section('content')
    @foreach($files as $file)
        <h5>{{ $file->filename }}</h5>
        <p>{{ $file->patch }}</p>
    @endforeach
@endsection
