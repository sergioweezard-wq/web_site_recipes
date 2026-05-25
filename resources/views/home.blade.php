@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h1 class="h3 fw-bold m-0">Кулінарні рецепти</h1>
        </div>

        <h4 class="text-muted mb-3">Остані 6 нових рецептів</h4>

        <div class="row">
            @foreach($newRecipes as $recipe)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm border-0 bg-light">
                        @if($recipe->photo_path)
                            <img src="{{ $recipe->photo_path }}" class="card-img-top recipe-card-image" alt="{{ $recipe->title }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h5 class="card-title fw-bold m-0">{{ $recipe->title }}</h5>
                                <span class="badge text-bg-secondary mt-1">{{ $recipe->calories }} ккал</span>
                            </div>
                            <p class="card-text text-truncate mt-2 mb-3">{{ $recipe->description }}</p>
                            <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-sm btn-primary mt-auto">Переглянути</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
