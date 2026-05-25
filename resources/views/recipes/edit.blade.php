@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <h2 class="fw-bold mb-3">Редагувати рецепт</h2>

        @if($recipe->photo_path)
            <div class="mb-3">
                <div class="text-muted mb-1">Поточне фото:</div>
                <img class="recipe-photo-edit" src="{{ $recipe->photo_path }}" alt="Фото рецепта">
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <form method="post" action="{{ route('recipes.update', $recipe) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    @include('recipes.form-fields', ['recipe' => $recipe])
                    <button class="btn btn-primary">Зберегти</button>
                    <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-secondary ms-2">Скасувати</a>
                </form>
            </div>
        </div>
    </div>
@endsection
