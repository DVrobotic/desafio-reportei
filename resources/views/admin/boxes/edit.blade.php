@extends('admin.layouts.sistema')

@section('content')
    @component('admin.components.edit')
        @slot('title', 'Editar Content Box')
        @slot('url', route('boxes.update', $box->id))
        @slot('form')
            @include('admin.boxes.form')
        @endslot
        @slot('body')
            @can('delete', $box)
                <form id="form-delete" action="{{ route('contents.destroy') }}" method="post">
                    @csrf
                    @method('delete')
                    <input hidden value="" name="content_id" id="content-input">
                </form>
                <form id="form-download" action="{{ route('contents.download') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <input hidden value="" name="content_id" id="download-input">
                </form>
            @endcan
        @endslot
    @endcomponent
@endsection

@push('scripts')
    <script src="{{ asset('js/components/ajaxWatch.js') }}"></script>
    <script>
        $(document).ajaxWatch('#form-delete', true);

        $(document).on('click', '.button-delete', function(){
            $('#content-input').attr('value', $(this).attr('data-id'));
            $('#form-delete').submit();
            $(this).closest('.deletable').remove();
        });

        $(document).on('click', '.button-download', function(){
            console.log( $(this).attr('data-id'));
            $('#download-input').attr('value', $(this).attr('data-id'));
            $('#form-download').submit();
        });
    </script>
@endpush

