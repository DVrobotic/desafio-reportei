<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="required">Box </label>
            <input type="text" name="name" id="name" autofocus required class="form-control" value="{{ old('name', $box->name) }}">
        </div>
    </div>
    <div class="form-group col-sm-6 col-12 my-auto">  
        <label for="content_list[]">Enviar</label>
        <input id="content" type="file" class="form-control-file" name="content_list[]" multiple>
        <img src=""  id="previewProfile" alt="User profile picture" class="profile-user-img img-fluid img-circle">
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/components/previewImage.js') }}"></script>
    <script>
        $("#content").change(function() {
                filePreview(this, '#previewProfile');
            });
    </script>
@endpush

