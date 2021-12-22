@foreach($contents as $content)
@if(strstr($content->type,'image/'))
    <div class="img-card card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
        <div class="card-body m-0 p-0">
            <div class="text-center w-100 m-0 p-0">
                <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!">
            </div>
        </div>
    </div>
@endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type,'video/'))
        <div class="video-card card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-width:810px; max-height:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <video controls pause="true" load="lazy" class="img-fluid m-0 p-0" style="width:800px; height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></video>
                </div>
            </div>
        </div>
    @endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type, 'pdf'))
        <div class="pdf-card card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0" style="max-width:800px; max-height:800px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <iframe load="lazy" class="img-fluid m-0 p-0" style="min-width:400px; min-height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></iframe>
                </div>
            </div>
        </div>
    @endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type,'text/rtf') || strstr($content->type,'application/msword') || strstr($content->type,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
        <li class="doc-list list-group-item"><i class="text-primary fas fa-file-alt ml-3"></i> Documento </li>
    @endif
@endforeach
@foreach($contents as $content)
    @if(strstr($content->type,'text/csv') || strstr($content->type,'application/vnd.ms-excel') || strstr($content->type,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        <li class="sheet-list list-group-item"><i class="text-olive fas fa-file-excel ml-3"></i></i> Planilha </li>
    @endif
@endforeach
      