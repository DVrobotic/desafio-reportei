@foreach($contents as $content)
    @if(strstr($content->type,'image/'))
        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!">
                </div>
            </div>
        </div>
    @endif
    @if(strstr($content->type,'video/'))
        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-width:810px; max-height:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <video controls pause="true" load="lazy" class="img-fluid m-0 p-0" style="width:800px; height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></video>
                </div>
            </div>
        </div>
    @endif
    @if(strstr($content->type,'pdf'))
        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0" style="max-width:800px; max-height:800px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <iframe load="lazy" class="img-fluid m-0 p-0" style="min-width:400px; min-height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></iframe>
                </div>
            </div>
        </div>
    @endif
    @if(strstr($content->type,'text/rtf') || strstr($content->type,'application/msword') || strstr($content->type,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset('img/doc-file.png') }}" alt="documento não encontrada!">
                </div>
            </div>
        </div>
    @endif
    @if(strstr($content->type,'text/csv') || strstr($content->type,'application/vnd.ms-excel') || strstr($content->type,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset('img/excel-file.png') }}" alt="documento não encontrada!">
                </div>
            </div>
        </div>
    @endif
@endforeach

<div id="contents">
    <div class="card">
      <div class="card-header" id="headingImage">
        <h5 class="mb-0">
          <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#imageCollapse" aria-expanded="true" aria-controls="imageCollapse">
                Imagens
          </button>
        </h5>
      </div>
      <div id="imageCollapse" class="collapse show" aria-labelledby="headingOne" data-parent="#contents">
        <div class="card-body">
            @foreach($contents as $content)
                @if(strstr($content->type,'image/'))
                    <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
                        <div class="card-body m-0 p-0">
                            <div class="text-center w-100 m-0 p-0">
                                <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!">
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
      </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingVideo">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#videoCollapse" aria-expanded="false" aria-controls="videoCollapse">
                        Videos
                </button>
            </h5>
        </div>
        <div id="videoCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#contents">
            <div class="card-body">
                @foreach($contents as $content)
                    @if(strstr($content->type,'video/'))
                        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-width:810px; max-height:410px">
                            <div class="card-body m-0 p-0">
                                <div class="text-center w-100 m-0 p-0">
                                    <video controls pause="true" load="lazy" class="img-fluid m-0 p-0" style="width:800px; height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></video>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingPdf">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#pdfCollapse" aria-expanded="false" aria-controls="pdfCollapse">
                    Pdfs
                </button>
            </h5>
        </div>
        <div id="pdfCollapse" class="collapse" aria-labelledby="headingPdf" data-parent="#contents">
            <div class="card-body">
                @foreach($contents as $content)
                    @if(strstr($content->type,'pdf'))
                        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0" style="max-width:800px; max-height:800px">
                            <div class="card-body m-0 p-0">
                                <div class="text-center w-100 m-0 p-0">
                                    <iframe load="lazy" class="img-fluid m-0 p-0" style="min-width:400px; min-height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!"></iframe>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingPdf">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#docCollapse" aria-expanded="false" aria-controls="docCollapse">
                    Documentos
                </button>
            </h5>
        </div>
        <div id="docCollapse" class="collapse" aria-labelledby="headingDocs" data-parent="#contents">
            <div class="card-body">
                @foreach($contents as $content)
                    @if(strstr($content->type,'text/rtf') || strstr($content->type,'application/msword') || strstr($content->type,'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
                        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
                            <div class="card-body m-0 p-0">
                                <div class="text-center w-100 m-0 p-0">
                                    <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset('img/doc-file.png') }}" alt="documento não encontrada!">
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingPdf">
            <h5 class="mb-0">
                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#sheetCollapse" aria-expanded="false" aria-controls="sheetCollapse">
                    Planilhas
                </button>
            </h5>
        </div>
        <div id="sheetCollapse" class="collapse" aria-labelledby="headingSheets" data-parent="#contents">
            <div class="card-body">
                @foreach($contents as $content)
                    @if(strstr($content->type,'text/csv') || strstr($content->type,'application/vnd.ms-excel') || strstr($content->type,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))
                        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
                            <div class="card-body m-0 p-0">
                                <div class="text-center w-100 m-0 p-0">
                                    <img load="lazy" class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset('img/excel-file.png') }}" alt="documento não encontrada!">
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>