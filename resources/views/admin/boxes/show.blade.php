@extends('admin.layouts.sistema')
@section('content')

    @component('admin.components.show')
        @slot('title', 'Detalhes da Meta')
        @slot('content')
            @include('admin.marks.form', ['show' => true])
        @endslot
        @slot('back')
            @can('update',$mark)
                <form id="form-download" action="{{ route('marks.download.file', $mark->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <button type="submit" form="form-download" class="btn btn-warning float-right mx-1"><i class="fas fa-file-alt"></i> Download</button>
                </form>
                <a href="{{ route('marks.edit', $mark->id) }}" class="btn btn-primary float-right ml-1"><i class="fas fa-pen"></i> Editar</a>
                <a href="{{ route('marks.index') }}" class="btn btn-dark float-right"><i class="fas fa-undo-alt"></i> Voltar</a>
            @endcan
        @endslot
    @endcomponent
@endsection

@push('scripts')
    <script>
        $('.form-control').attr('disabled', true);
    </script>
@endpush