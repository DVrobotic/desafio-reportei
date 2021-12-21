@push('name')

@endpush
<div class="row">
    <div class="form-group col-sm-6">
        <label for="name" class="required">Nome </label>
        <input type="text" name="name" id="name" class="form-control" required autofocus value="{{ old('name',$user->name) }}">
    </div>
    <div class="form-group col-sm-6">
        <label for="email" class="required">E-mail </label>
        <input type="email" name="email" id="email" class="form-control" required value="{{ old('email',$user->email) }}">
    </div>
    <div class="form-group col-sm-6">
        <label for="cpf" class="required">CPF </label>
        <input type="text" name="cpf" id="cpf" class="form-control" required value="{{ old('cpf',$user->cpf) }}">
    </div>
    <div class="form-group col-sm-6">
        <label for="dateBirth" class="required">Data de nascimento </label>
        <input type="date" name="dateBirth" id="dateBirth" class="form-control" required value="{{ old('dateBirth',$user->dateBirth) }}">
    </div>
    @can('is_admin', App\Models\User::class)
        <div class="form-group col-12">
            <label for="type" class="required">Tipo </label>
            <select name="type" class="form-control select2" id="type" value="{{ old('type',$user->type) }}" required>
                <option></option>
                <option value="0">Usuário Comum</option>
                <option value="1">Administrador</option>
            </select>
        </div>
    @endcan
    @if (!Route::is('users.show'))
        <div class="form-group col-sm-12">
            <hr>
        </div>
        <div class="form-group col-sm-6">
            <label for="password" class="{{ Route::is('users.create') ? 'required' : '' }}">Senha </label>
            <i  class="far fa-question-circle"
                data-toggle="tooltip"
                data-placement="right"
                title="Mínimo 8 caracteres.">
            </i>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="clickEye">
                        <i class="fas fa-eye-slash" id="eye"></i>
                    </span>
                </div>
                <input type="password" name="password" id="password" class="form-control" minlength="8" {{ Route::is('users.create') ? 'required' : '' }}>
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label for="password_confirmation" class="{{ Route::is('users.create') ? 'required' : '' }}">Confirmação de senha </label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" {{ Route::is('users.create') ? 'required' : '' }}>
        </div>
    @endif
</div>

@push('scripts')
    <script src="{{ asset('js/components/changeVisibilityPassword.js') }}"></script>
@endpush

{{-- <div class="form-group col-12">
    <label for="description" class="required">Descrição </label>
    <textarea class="summernote" name="description" id="description" required>{{ old('description', $user->description )}}</textarea>
</div>
<script>
    $('.summernote').summernote({
        placeholder: 'Descrição do curso',
        tabsize: 2,
        height: 150
    });
</script> --}}
