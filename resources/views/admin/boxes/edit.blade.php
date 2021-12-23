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
                <form id="form-banner" action="{{ route('boxes.banner') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <input hidden value="" name="content_id" id="banner-content-input">
                    <input hidden value="" name="box_id" id="banner-box-input">
                </form>
            @endcan
        @endslot
    @endcomponent
@endsection

@push('scripts')
    <script src="{{ asset('js/components/ajaxWatch.js') }}"></script>
    <script>
        $(document).ajaxWatch('#form-delete', true);
        $(document).ajaxWatch('#form-banner');

        $(document).on('click', '.button-delete', function(){
            $('#content-input').attr('value', $(this).attr('data-id'));
            $('#form-delete').submit();
            $(this).closest('.deletable').remove();
        });

        $(document).on('click', '.button-download', function(){
            $('#download-input').attr('value', $(this).attr('data-id'));
            $('#form-download').submit();
        });

        $(document).on('click', '.button-banner', function(){
            $('#banner-content-input').attr('value', $(this).attr('data-content-id'));
            $('#banner-box-input').attr('value', $(this).attr('data-box-id'));
            
            if($(this).hasClass('bg-transparent')){
                emptyStar('.button-banner');
            } else{
                emptyStar('.button-banner');
                fullStar(this);
            }
            
            $('#form-banner').submit();
        });


        function fullStar(element){
            $(element).find('.icon-banner').removeClass('far fa-star');
            $(element).find('.icon-banner').removeClass('text-dark');

            $(element).find('.icon-banner').addClass('fas fa-star');
            $(element).find('.icon-banner').addClass('text-warning');

            $(element).addClass('bg-transparent');
        }

        function emptyStar(element){
            $(element).find('.icon-banner').removeClass('fas fa-star');
            $(element).find('.icon-banner').removeClass('text-warning');
            
            $(element).find('.icon-banner').addClass('far fa-star');
            $(element).find('.icon-banner').addClass('text-dark');

            $(element).removeClass('bg-transparent');
        }

    </script>
@endpush

