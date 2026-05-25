@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <h2 class="fw-bold mb-3">Реєстрація</h2>
        <div class="row">
            <div class="col-lg-6">
                <form method="post" action="{{ route('register.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ім'я</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                        @include('partials.field-error', ['field' => 'name'])
                    </div>
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
                    <div class="mb-3">
                        <label class="form-label">Підтвердити пароль</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <button class="btn btn-primary" type="submit">Зареєструватися</button>
                    <a class="btn btn-outline-primary ms-2" href="{{ route('login') }}">Вхід</a>
                </form>
            </div>
        </div>
    </div>
@endsection
