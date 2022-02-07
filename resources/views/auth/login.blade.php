@extends('admin.layouts.auth')

@section('content')
    <p class="login-box-msg">Entre em sua conta</p>
    <a href="{{ route('github.redirect') }}" style="background-color: #2E64E0" class="btn btn-primary btn-block mt-3 mb-3">Entrar</a>
    <hr>
    <div class='text-center'>
        Ainda não tem uma conta? <a href="{{ route('register') }}"><b>Cadastre-se.</b></a>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/components/changeVisibilityPassword.js') }}"></script>
@endpush
