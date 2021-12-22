<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="required">Nome da Content Box </label>
            <input type="text" name="name" id="name" autofocus required class="form-control" value="{{ old('name', $box->name) }}">
        </div>
    </div>
    @if(Route::is('boxes.edit'))
        <div class="col-12 text-center">
            <hr>
            <h3 class="text-center mb-3">Conteúdos</h3>
            @include('admin.contents.content', ['contents' => $box->contents ?? ''])
        </div>
        <div class="form-group col-12 mt-4 d-block">  
            <label for="content_list[]">Enviar</label>
            <input id="content" type="file" class="form-control-file" name="content_list[]" multiple>
        </div>
    @endif
</div>

@push('scripts')
    <script src="{{ asset('js/components/previewImage.js') }}"></script>
    <script src="{{ asset('js/components/ajaxFile.js') }}"></script>
    @if(Route::is('boxes.edit'))
        <script>
            $(document).ajaxFile('#form-adicionar', true, null, true, true);
        </script>
    @endif
@endpush

