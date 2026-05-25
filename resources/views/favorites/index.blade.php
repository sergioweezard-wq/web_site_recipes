@extends('layouts.app')

@section('content')
    <div class="container mt-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h2 class="fw-bold m-0">Ваше обране</h2>
        <a class="btn btn-outline-primary" href="{{ route('recipes.index') }}">Рецепти</a>
    </div>

    @if($recipes->isEmpty())
        <div class="alert alert-info">У вас поки що немає рецептів в обраному.</div>
    @else
        <div class="row">
            @foreach($recipes as $recipe)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm border-0 bg-light">
                        @if($recipe->photo_path)
                            <img src="{{ $recipe->photo_path }}" class="card-img-top recipe-card-image" alt="{{ $recipe->title }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $recipe->title }}</h5>
                            <p class="card-text text-truncate mt-2 mb-3">{{ $recipe->description }}</p>

                            <div class="d-flex justify-content-between align-items-center gap-2 mt-auto">
                                <span class="badge text-bg-secondary">{{ $recipe->calories }} ккал</span>
                                <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-sm btn-primary">Переглянути</a>
                            </div>

                            <div class="mt-2">
                                <form method="post" action="{{ route('favorites.remove') }}">
                                    @csrf
                                    <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                                    <input type="hidden" name="return_url" value="{{ route('favorites.index') }}">
                                    <button class="btn btn-sm btn-outline-danger w-100">Прибрати</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    </div>
@endsection
