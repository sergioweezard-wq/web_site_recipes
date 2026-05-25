@extends('layouts.app')

@php
    $categories = ['Salad', 'FirstDishes', 'Breakfast', 'Starters', 'Desserts'];
    $categoryLabels = [
        'Salad' => 'Салати',
        'FirstDishes' => 'Перші страви',
        'Breakfast' => 'Сніданки',
        'Starters' => 'Закуски',
        'Desserts' => 'Десерти',
    ];
@endphp

@section('content')
    <div class="container mt-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h2 class="fw-bold m-0">Всі рецепти</h2>
        @auth
            @if(in_array(auth()->user()->role, ['Admin', 'Redactor']))
                <a href="{{ route('recipes.create') }}" class="btn btn-success btn-sm">+ Додати рецепт</a>
            @endif
        @endauth
    </div>

    <form method="get" action="{{ route('recipes.index') }}" class="row g-2 align-items-end mb-4">
        <div class="col-md-6">
            <label class="form-label">Пошук</label>
            <input class="form-control" type="text" name="q" value="{{ $query }}" placeholder="Введіть слово...">
        </div>
        <div class="col-md-4">
            <label class="form-label">Категорія</label>
            <select class="form-select" name="category">
                <option value="">Усі категорії</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected($currentCategory == $category)>{{ $categoryLabels[$category] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Застосувати</button>
        </div>
    </form>

    <div class="row">
        @foreach($recipes as $recipe)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0 bg-light">
                    @if($recipe->photo_path)
                        <img src="{{ $recipe->photo_path }}" class="card-img-top recipe-card-image" alt="{{ $recipe->title }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <h5 class="card-title fw-bold m-0">{{ $recipe->title }}</h5>
                                <div class="text-muted small">{{ $categoryLabels[$recipe->category] ?? $recipe->category }}</div>
                            </div>
                            <span class="badge text-bg-secondary mt-1">{{ $recipe->calories }} ккал</span>
                        </div>

                        <p class="card-text text-truncate mt-2 mb-3">{{ $recipe->description }}</p>

                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-primary btn-sm flex-grow-1">Переглянути</a>
                            @auth
                                @if(in_array($recipe->id, $favoriteIds))
                                    <form method="post" action="{{ route('favorites.remove') }}">
                                        @csrf
                                        <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                                        <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                        <button class="btn btn-outline-danger btn-sm">Прибрати</button>
                                    </form>
                                @else
                                    <form method="post" action="{{ route('favorites.add') }}">
                                        @csrf
                                        <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                                        <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                        <button class="btn btn-outline-success btn-sm">В обране</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    </div>
@endsection
