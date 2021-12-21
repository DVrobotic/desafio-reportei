@extends('admin.layouts.sistema')

@section('content')

@component('admin.components.edit')
    @slot('title', 'Editar Content Box')
    @slot('url', route('boxes.update', $box->id))
    @slot('form')
        @include('admin.boxes.form')
    @endslot
@endcomponent

@endsection
