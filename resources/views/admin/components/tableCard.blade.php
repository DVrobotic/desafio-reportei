@if ($card != '' || !($createFirst ?? true) || request('search') || request('year'))
<div class="card">
    <div class="card-header card-outline cor-backend">
        <h3 class="float-left m-0 table-title">{{ $title ?? null }}</h3>
        <div class="float-right mr-2">
            @if (isset($url))
                <div class="input-group input-group-sm">
                    <a href="{{ $url ?? null }}" >
                        <button type="button" class="btn btn-dark icone-add-table">
                            <b><i class="fas fa-plus-circle "></i> Adicionar</b>
                        </button>
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div class="card-body table-responsive ">
        <form id="research">
            <div class="row">
                <div class="col-sm-10 col-md-4 mt-1">
                    <input type="search" class="form-control d-inline-block" id="search" placeholder="Pesquisar" name="search" value="{{request('search')}}">
                </div>
                <button type="submit" form="research" class="btn btn-secondary d-inline-block"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <div class="row">
            {{ $card ?? null}}
        </div>
        {{ $footer ?? null }}
    </div>
</div>
@else
    <div class="text-center" style="color: #949699">
        <i class="fas fa-exclamation-circle" style="font-size: 10em"></i>
        <p class="mb-4 h2">Nenhum item encontrado!</p>
        <a href="{{ $url ?? '#' }}">
            <button type="button" class="btn btn-dark">
                <b><i class="fas fa-plus-circle"></i> Adicionar novo item</b>
            </button>
        </a>
    </div>
@endif

@push('scripts')
    <script>

        function research(){
            $("#research").submit();
        }

        $('select[value]').each(function () {
            $(this).val($(this).attr('value'));
        });

    </script>
@endpush