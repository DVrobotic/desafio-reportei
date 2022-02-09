@extends('admin.layouts.sistema')

@section('title', 'Dashboard')

@section('content')
    @if($data['user_json'] ?? false)
        <h1>user</h1>
        {{ json_encode($data['user_json']) ?? '' }}
    @endif
    @if($data['repo_json'] ?? false)
        <h1>repositorio</h1>
        {{ $data['repo_json'] ?? '' }}
    @endif
    @if($data['commits_json'] ?? false)
        <h1>commits</h1>
        {{ $data['commits_json'] ?? '' }}
    @endif
    @if($data['files'] ?? false)
        <h1>files</h1>
        @foreach($data['files'] as $file)
            <h5>{{ $file->filename ?? '' }}</h5>
            <p>{{ $file->patch ?? '' }}</p>
        @endforeach
    @endif
    @if($data['commit_json'] ?? false)
        <h1>Commit</h1>
        {{ $data['commit_json'] ?? '' }}
    @endif
    @if($data['organizations_json'] ?? false)
        <h1>organizations</h1>
        {{  $data['organizations_json'] ?? '' }}
    @endif
    @if($data['json_reportei'] ?? false)
        <h1>Reportei - public org</h1>
        {{ $data['json_reportei'] ?? '' }}
    @endif
    @if($data['reportei_repos_json'] ?? false)
        <h1>Reportei repos</h1>
        {{ $data['reportei_repos_json'] ?? '' }}
    @endif
@endsection
