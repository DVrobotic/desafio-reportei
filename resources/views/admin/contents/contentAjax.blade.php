@foreach($contents as $content)
    @if(strstr($content->type,'image/'))
        <div class="mb-5 deletable img-card card card-dark card-outline border-left-0 border-right-0 border-bottom-0 col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!">
                </div>
            </div>
            <div class="card-footer p-1">
                <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-left btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
                <button class="btn btn-outline-warning float-right btn-sm border-0 rounded-circle"><i class="text-dark far fa-star"></i></button>
                <button type="button" data-id="{{ $content->id }}" class="button-download btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
            </div>
        </div>
    @endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type,'video/'))
        <div class="mb-5 deletable video-card card card-dark card-outline border-left-0 border-right-0 border-bottom-0 col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-width:810px; max-height:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <iframe sandbox allowfullscreen pause="true" load="lazy" class="img-fluid m-0 p-0" style="width:800px; height:400px" src="{{ asset($content->file_path) }}" alt="Vídeo não encontrada!"></iframe>
                </div>
            </div>
            <div class="card-footer p-1">
                <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-left btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
                <button class="btn btn-outline-warning float-right btn-sm border-0 rounded-circle"><i class="text-dark far fa-star"></i></button>
                <button type="button" data-id="{{ $content->id }}" class="button-download btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
            </div>
        </div>
    @endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type, 'pdf'))
        <div class="mb-5 deletable pdf-card card card-dark card-outline border-left-0 border-right-0 border-bottom-0 col-md-5 col-12 d-inline-block mx-3 my-3 p-0" style="max-width:800px; max-height:800px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <iframe load="lazy" class="img-fluid m-0 p-0" style="min-width:400px; min-height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></iframe>
                </div>
            </div>
            <div class="card-footer p-1">
                <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-left btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
                <button class="btn btn-outline-warning float-right btn-sm border-0 rounded-circle"><i class="text-dark far fa-star"></i></button>
                <button type="button" data-id="{{ $content->id }}" class="button-download btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
            </div>
        </div>
    @endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type,'text/rtf') || strstr($content->type,'application/msword') || strstr($content->type,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
        <li class="deletable doc-list list-group-item">
            <i class="text-primary fas fa-file-alt ml-3"></i>  
            Documento 
            <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-right btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
            <button type="button" data-id="{{ $content->id }}" class="button-download btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
        </li>
    @endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type,'text/csv') || strstr($content->type,'application/vnd.ms-excel') || strstr($content->type,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        <li class="deletable sheet-list list-group-item"><i class="text-olive fas fa-file-excel ml-3"></i> 
            Planilha 
            <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-right btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
            <button type="button" data-id="{{ $content->id }}" class="button-download btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
        </li>
    @endif
@endforeach
      