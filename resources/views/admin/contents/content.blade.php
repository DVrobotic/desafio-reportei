<div id="contents">
    <div class="card mb-0">
      <div class="card-header text-left" id="headingImage">
        <h5 class="mb-0">
          <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#imageCollapse" aria-expanded="false" aria-controls="imageCollapse">
                Imagens
          </button>
        </h5>
      </div>
      <div id="imageCollapse" class="collapse" aria-labelledby="headingOne" data-parent="#contents">
        <div class="card-body mb-5" id="img-body">
            @foreach($contents as $content)
                @if(strstr($content->type,'image/'))
                    <div class="mb-5 deletable img-card card card-dark card-outline border-left-0 border-right-0 border-bottom-0 col-md-5 col-12 d-inline-block mx-3 my-3 p-1 align-top" style="max-height:410px; max-width:410px">
                        <div class="card-body m-0 p-0">
                            <div class="text-center w-100 m-0 p-0">
                                <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!">
                            </div>
                        </div>
                        <div class="card-footer p-1">
                            <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-left btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
                            <button class="btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
                            <button class="btn btn-outline-warning float-right btn-sm border-0 rounded-circle"><i class="text-dark far fa-star"></i></button>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
      </div>
    </div>
    <div class="card mb-0">
        <div class="card-header text-left" id="headingVideo">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#videoCollapse" aria-expanded="false" aria-controls="videoCollapse">
                        Videos
                </button>
            </h5>
        </div>
        <div id="videoCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#contents">
            <div class="card-body" id="video-body">
                @foreach($contents as $content)
                    @if(strstr($content->type,'video/'))
                        <div class="mb-5 deletable card card-dark card-outline border-left-0 border-right-0 border-bottom-0 col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-width:810px; max-height:410px">
                            <div class="card-body m-0 p-0">
                                <div class="text-center w-100 m-0 p-0">
                                    <iframe sandbox allowfullscreen pause="true" load="lazy" class="img-fluid m-0 p-0" style="width:800px; height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></iframe>
                                </div>
                            </div>
                            <div class="card-footer p-1">
                                <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-left btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
                                <button class="btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
                                <button class="btn btn-outline-warning float-right btn-sm border-0 rounded-circle"><i class="text-dark far fa-star"></i></button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-header text-left" id="headingPdf">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#pdfCollapse" aria-expanded="false" aria-controls="pdfCollapse">
                    Pdfs
                </button>
            </h5>
        </div>
        <div id="pdfCollapse" class="collapse" aria-labelledby="headingPdf" data-parent="#contents">
            <div class="card-body" id="pdf-body">
                @foreach($contents as $content)
                    @if(strstr($content->type, 'pdf'))
                        <div class="mb-5 deletable card card-dark card-outline border-left-0 border-right-0 border-bottom-0 col-md-5 col-12 d-inline-block mx-3 my-3 p-0" style="max-width:800px; max-height:800px">
                            <div class="card-body m-0 p-0">
                                <div class="text-center w-100 m-0 p-0">
                                    <iframe load="lazy" class="img-fluid m-0 p-0" style="min-width:400px; min-height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></iframe>
                                </div>
                            </div>
                            <div class="card-footer p-1">
                                <button data-id="{{ $content->id }}" type="button" class="button-delete btn btn-outline-danger float-left btn-sm border-0 rounded-circle"><i class="fas fa-trash-alt"></i></button>
                                <button class="btn btn-outline-primary float-right btn-sm border-0 rounded-circle"><i class="fas fa-download"></i></button>
                                <button class="btn btn-outline-warning float-right btn-sm border-0 rounded-circle"><i class="text-dark far fa-star"></i></button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-header text-left" id="headingPdf">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#docCollapse" aria-expanded="false" aria-controls="docCollapse">
                    Documentos
                </button>
            </h5>
        </div>
        <div id="docCollapse" class="collapse" aria-labelledby="headingDocs" data-parent="#contents">
            <div class="card-body">
                <ul id="doc-ul-list" class="list-group list-group-unbordered mb-3 text-left">
                    @foreach($contents as $content)
                        @if(strstr($content->type,'text/rtf') || strstr($content->type,'application/msword') || strstr($content->type,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
                            <li class="list-group-item"><i class="text-primary fas fa-file-alt ml-3"></i> Documento </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-header text-left" id="headingPdf">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#sheetCollapse" aria-expanded="false" aria-controls="sheetCollapse">
                    Planilhas
                </button>
            </h5>
        </div>
        <div id="sheetCollapse" class="collapse" aria-labelledby="headingSheets" data-parent="#contents">
            <div class="card-body">
                <ul id="sheet-ul-list" class="list-group list-group-unbordered mb-3 text-left">
                    @foreach($contents as $content)
                        @if(strstr($content->type,'text/csv') || strstr($content->type,'application/vnd.ms-excel') || strstr($content->type,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
                            <li class="list-group-item"><i class="text-olive fas fa-file-excel ml-3"></i></i> Planilha </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
