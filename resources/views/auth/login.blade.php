@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <h2 class="fw-bold mb-3">Вхід</h2>
        <div class="row">
            <div class="col-lg-6">
                <form method="post" action="{{ route('login.attempt') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @include('partials.field-error', ['field' => 'email'])
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @include('partials.field-error', ['field' => 'password'])
                    </div>
                    <button class="btn btn-primary" type="submit">Увійти</button>
                    <a class="btn btn-outline-primary ms-2" href="{{ route('register') }}">Реєстрація</a>
                </form>
            </div>
        </div>
    </div>
@endsection
