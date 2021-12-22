@extends('admin.layouts.sistema')

@section('content')
    @component('admin.components.edit')
        @slot('title', 'Editar Content Box')
        @slot('url', route('boxes.update', $box->id))
        @slot('form')
            @include('admin.boxes.form')
        @endslot
        @slot('body')
            <form class="form-delete" action="{{ route('contents.destroy') }}" method="post">
                @csrf
                @method('delete')
                <input hidden value="" name="content_id" id="content-input">
            </form>
        @endslot
    @endcomponent
@endsection

@push('scripts')
    <script src="{{ asset('js/components/ajaxWatch.js') }}"></script>
    <script>
        $(document).ajaxWatch('.form-delete', true);
        $(document).on('click', '.button-delete', function(){
            console.log( $(this).attr('data-id'));
            $('#content-input').attr('value', $(this).attr('data-id'));
            $('.form-delete').submit();

            $(this).closest('.deletable').remove();
        });
    </script>
@endpush

