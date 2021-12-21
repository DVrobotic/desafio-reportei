@extends('admin.layouts.sistema')

@section('content')

@component('admin.components.edit')
    @slot('title', 'Editar Meta')
    @slot('url', route('marks.update', $mark->id))
    @slot('form')
        @include('admin.marks.form')
    @endslot
@endcomponent

@endsection
