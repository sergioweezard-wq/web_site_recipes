@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <h2 class="fw-bold mb-3">Створити рецепт</h2>

        <div class="row">
            <div class="col-lg-8">
                <form method="post" action="{{ route('recipes.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('recipes.form-fields', ['recipe' => null])
                    <button class="btn btn-primary">Зберегти</button>
                    <a href="{{ route('recipes.index') }}" class="btn btn-secondary ms-2">Скасувати</a>
                </form>
            </div>
        </div>
    </div>
@endsection
