@extends('layouts.sistema')

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Primeiro Card -->
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="box-profile">
                    <form id="form-delete-picture" action="{{ route('users.picture', $user->id) }}" method="post">
                        @csrf
                        @method('delete')
                    </form>
                    <form id="form-update-picture" action="{{ route('users.picture', $user->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                        <input type="file" name="profile_path" id="profile" class="d-none">
                        <img src="{{ asset('storage/img/profile/' . $user->profile_path) }}"  id="previewProfile" alt="User profile picture" class="profile-user-img img-fluid img-circle">
                        <div class="overlay">
                            <div class="row">
                                <a href="#" id="btnEditProfile" class="" title="Editar">
                                    <i class="fas fa-pen text-dark fa-2x"></i>
                                </a>
                                @if ($user->profile_path != 'profile_default.png')
                                    <a href="#" id="btnDeleteProfile" class="ml-4" title="Excluir">
                                        <i class="fas fa-trash text-dark fa-2x"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <h3 class="profile-username text-center">{{ collect(explode(' ', Auth::user()->name))->slice(0, 2)->implode(' ') }}</h3>

                <p class="text-muted text-center">{{ $user->email }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>CPF</b> <p class="float-right mb-0">{{ $user->cpf }}</p>
                    </li>
                    <li class="list-group-item">
                        <b>Data de nascimento</b> <p class="float-right mb-0">{{ date('d/m/Y', strtotime($user->dateBirth)) }}</p>
                    </li>
                </ul>
                <a href="#" class="btn btn-primary btn-block"><b>Botão Qualquer</b></a>
            </div>
        </div>
        <!-- Fim Primeiro Card -->

        <!-- Segundo Card -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informações</h3>
            </div>
            <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Campo 1</strong>
                <p class="text-muted">value1</p>
                <hr>
                <strong><i class="fas fa-pencil-alt mr-1"></i> Campo 2</strong>
                <p class="text-muted">
                    <span class="badge badge-danger">UI Design</span>
                    <span class="badge badge-success">Coding</span>
                    <span class="badge badge-info">Javascript</span>
                    <span class="badge badge-warning">PHP</span>
                    <span class="badge badge-primary">Node.js</span>
                </p>
                <hr>
                <strong><i class="far fa-file-alt mr-1"></i> Campo 3</strong>
                <p class="text-muted">value3</p>
            </div>
        </div>
        <!-- Fim Segundo Card -->
    </div>

    <!-- Àrea Maior -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#menu1" data-toggle="tab">Alterar dados</a></li>
                    <li class="nav-item"><a class="nav-link" href="#menu2" data-toggle="tab">Menu 2</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="menu1">
                        <form id="form-adicionar" action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            @include('admin.users.form')
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-success">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="menu2">
                        Qualquer Informação
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fim Àrea Maior -->
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/components/sweetAlert.js') }}"></script>
    <script src="{{ asset('js/components/previewImage.js') }}"></script>
    <script>
        $("#type").attr("disabled", true);
        $("#btnEditProfile").click(function() {
            $("#profile").click();
        });
        $("#btnDeleteProfile").click(function() {
            $('#form-delete-picture').submit();
        });
        $("#profile").change(function() {
            filePreview(this, '#previewProfile');
            $('#form-update-picture').submit();
        });
    </script>
@endpush
