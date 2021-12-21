@extends('admin.layouts.sistema')

@section('content')
    @component('admin.components.tableCard')
        @slot('title', 'Content Boxes')
        @can('create', App\Models\Box::class)
            @slot('url', route('boxes.create'))
        @endcan
        @slot('card')
            @foreach($boxes as $box)
                <div class="col-md-4 mt-2">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            
                            <h3 class="profile-username text-center">{{ $box->name }}</h3>
                            <ul class="list-group list-group-unbordered mb-3">
                            </ul>
                            <div class="options text-center">
                                @can('view', $box)
                                    <a href="{{ route('boxes.show', $box->id) }}" class="btn btn-dark"><i class="fas fa-search"></i></a>
                                @endcan
                                @can('update', $box)
                                    <a href="{{ route('boxes.edit', $box->id) }}" class="btn btn-primary"><i class="fas fa-pen"></i></a>
                                @endcan
                                @can('delete', $box)
                                    <form class="form-delete" action="{{ route('boxes.destroy', $box->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger "><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endslot
        @slot('footer')
            <span class="mt-4">Mostrando de {{ $boxes->firstItem() }} até {{ $boxes->lastItem() }} de {{ $boxes->total() }} registros</span>
            <span class="float-right mt-2">
                    {{ $boxes->links() }}
            </span>
        @endslot
    @endcomponent
@endsection
