@extends('admin.layouts.sistema')

@section('title', 'Dashboard')

@section('content')
    @if($user_json ?? false)
        <h1>user</h1>
        {{ json_encode($user_json) ?? '' }}
    @endif
    @if($repo_json ?? false)
        <h1>repositorio</h1>
        {{ $repo_json ?? '' }}
    @endif
    @if($commits_json ?? false)
        <h1>commits</h1>
        {{ $commits_json ?? '' }}
    @endif
    @if($files ?? false)
        <h1>files</h1>
        @foreach($files as $file)
            <h5>{{ $file->filename ?? '' }}</h5>
            <p>{{ $file->patch ?? '' }}</p>
        @endforeach
    @endif
    @if($commit_json ?? false)
        <h1>Commit</h1>
        {{ $commit_json ?? '' }}
    @endif
    @if($organizations_json ?? false)
        <h1>organizations</h1>
        {{  $organizations_json ?? '' }}
    @endif
    @if($json_reportei ?? false)
        <h1>Reportei - public org</h1>
        {{ $json_reportei ?? '' }}
    @endif
    @if($reportei_repos_json ?? false)
        <h1>Reportei repos</h1>
        {{ $reportei_repos_json ?? '' }}
    @endif
@endsection
