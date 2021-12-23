<div class="mb-5 deletable pdf-card card card-dark card-outline border-left-0 border-right-0 border-bottom-0 col-md-5 col-12 d-inline-block mx-3 my-3 p-0" style="max-width:800px; max-height:800px">
    <div class="card-body m-0 p-0">
        <div class="text-center w-100 m-0 p-0">
            <iframe load="lazy" class="img-fluid m-0 p-0" style="min-width:400px; min-height:400px" src="{{ asset($content->file_path) }}" alt="Pdf não encontrada!"></iframe>
        </div>
    </div>
    <div class="card-footer p-1">
        @can('delete', $content)
            @if(Route::is('boxes.edit') || isset($ajax))
                <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-left btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
            @endif
        @endcan
        <button type="button" data-id="{{ $content->id }}" class="button-download btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
    </div>
</div>