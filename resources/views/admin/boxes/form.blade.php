<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="required">Box </label>
            <input type="text" name="name" id="name" autofocus required class="form-control" value="{{ old('name', $box->name) }}">
        </div>
    </div>
    <div id="test" class="col-12 text-center">
        <hr>
        <h3 class="text-sm-left text-center">Conteúdos</h3>
        @include('admin.contents.content', ['contents' => $box->contents ?? ''])
    </div>
    <div class="form-group col-12 my-auto d-block">  
        <label for="content_list[]">Enviar</label>
        <input id="content" type="file" class="form-control-file" name="content_list[]" multiple>
        {{-- <img src=""  id="previewProfile" alt="User profile picture" class="profile-user-img img-fluid img-circle"> --}}
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/components/previewImage.js') }}"></script>
    <script src="{{ asset('js/components/ajaxFile.js') }}"></script>
    <script>
        $(document).ajaxFile('#form-adicionar', true, null, true, $('#test'));
        $("#content").change(function() {
                filePreview(this, '#previewProfile');
            });
    </script>
@endpush

