@foreach($contents as $content)
    @if(strstr($content->type,'image/'))
        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-3 my-3 p-0 align-top" style="max-height:410px; max-width:410px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <img class="img-fluid m-0 p-0" style="max-height:400px; max-width:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!">
                </div>
            </div>
        </div>
    @endif
    @if(strstr($content->type,'video/') || strstr($content->type,'gif/'))
        <div class="card card-dark card-outline border-dark col-md-5 col-12 d-inline-block mx-auto my-3 p-0" style="max-width:800px; max-height:400px">
            <div class="card-body m-0 p-0">
                <div class="text-center w-100 m-0 p-0">
                    <iframe class="img-fluid m-0 p-0" style="width:800px; height:400px" src="{{ asset($content->file_path) }}" alt="Imagem não encontrada!">
                </div>
            </div>
        </div>
    @endif
@endforeach