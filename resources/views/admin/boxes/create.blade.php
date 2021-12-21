@extends('admin.layouts.sistema')

@section('content')

    @component('admin.components.create')
        @slot('title', 'Criar Meta')
        @slot('url', route('marks.store'))
        @slot('form')
            @include('admin.marks.form')
        @endslot
    @endcomponent

@endsection