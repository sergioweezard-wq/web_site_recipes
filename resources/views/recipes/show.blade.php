@extends('layouts.app')

@php
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
    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
        <div>
            <h2 class="fw-bold">{{ $recipe->title }}</h2>
            <div class="text-muted">{{ $categoryLabels[$recipe->category] ?? $recipe->category }}</div>
        </div>
        <div>
            <a href="{{ route('recipes.index') }}" class="btn btn-outline-primary">Назад</a>
            @auth
                @if(in_array(auth()->user()->role, ['Admin', 'Redactor']))
                    <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-outline-warning ms-2">Редагувати</a>
                    <form method="post" action="{{ route('recipes.destroy', $recipe) }}" class="d-inline ms-2" onsubmit="return confirm('Видалити цей рецепт?')">
                        @csrf
                        @method('delete')
                        <button class="btn btn-outline-danger">Видалити</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>

    <div class="card border-0 shadow-sm bg-light mb-3">
        <div class="card-body">
            <h4 class="h6 fw-bold text-muted mb-3">Поживна цінність на 100 грам готової страви</h4>
            <div class="row text-center g-2">
                <div class="col-4">
                    <div class="nutrition-box">
                        <div class="nutrition-value">{{ number_format($recipe->fats ?? 0, 1) }}</div>
                        <div class="nutrition-label">Жири (г)</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="nutrition-box">
                        <div class="nutrition-value">{{ number_format($recipe->carbs ?? 0, 1) }}</div>
                        <div class="nutrition-label">Вуглеводи (г)</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="nutrition-box">
                        <div class="nutrition-value">{{ number_format($recipe->proteins ?? 0, 1) }}</div>
                        <div class="nutrition-label">Білки (г)</div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <span class="badge text-bg-primary fs-6">{{ number_format($recipe->calories, 1) }} ккал </span>
            </div>
        </div>
    </div>

    @if($recipe->photo_path)
        <img src="{{ $recipe->photo_path }}" class="img-fluid mb-3 rounded shadow-sm recipe-photo-large" alt="{{ $recipe->title }}">
    @endif

    <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
        @auth
            <form method="post" action="{{ route('recipes.like', $recipe) }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm {{ ($userReaction?->type ?? '') === 'like' ? 'btn-success' : 'btn-outline-success' }}">
                    👍 {{ $likesCount }}
                </button>
            </form>
            <form method="post" action="{{ route('recipes.dislike', $recipe) }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm {{ ($userReaction?->type ?? '') === 'dislike' ? 'btn-danger' : 'btn-outline-danger' }}">
                    👎 {{ $dislikesCount }}
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-success text-decoration-none">
                👍 {{ $likesCount }}
            </a>
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger text-decoration-none">
                👎 {{ $dislikesCount }}
            </a>
            <span class="text-muted small">(увійдіть, щоб оцінити)</span>
        @endauth
    </div>

    <div class="card border-0 shadow-sm bg-light mb-3">
        <div class="card-body">
            <h4 class="h5 fw-bold">Опис</h4>
            <p class="mb-0">{{ $recipe->description }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm bg-light h-100">
                <div class="card-body">
                    <h4 class="h5 fw-bold">Інгредієнти</h4>
                    <ul>
                        @foreach($recipe->ingredients->sortBy('id') as $ingredient)
                            <li>{{ $ingredient->text }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm bg-light h-100">
                <div class="card-body">
                    <h4 class="h5 fw-bold mb-3">Етапи приготування</h4>
                    <div class="recipe-steps-list">
                        @foreach($recipe->steps->sortBy('step_number') as $step)
                            <div class="mb-3">
                                <div class="mb-2"><span class="badge text-bg-secondary">Етап {{ $step->step_number }}</span></div>
                                <div>{{ $step->text }}</div>
                                @if($step->photo_path)
                                    <img src="{{ $step->photo_path }}" alt="Етап {{ $step->step_number }}" class="step-photo-preview rounded mt-2">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        <div class="mt-3">
            @if($isFavorite)
                <form method="post" action="{{ route('favorites.remove') }}">
                    @csrf
                    <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                    <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                    <button class="btn btn-outline-danger">Прибрати з обраного</button>
                </form>
            @else
                <form method="post" action="{{ route('favorites.add') }}">
                    @csrf
                    <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                    <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                    <button class="btn btn-outline-success">Додати в обране</button>
                </form>
            @endif
        </div>
    @endauth

    <div class="card border-0 shadow-sm bg-light mt-4">
        <div class="card-body">
            <h4 class="h5 fw-bold mb-3">Коментарі ({{ $recipe->comments->count() }})</h4>

            @auth
                <form method="post" action="{{ route('comments.store', $recipe) }}" class="mb-4">
                    @csrf
                    <label class="form-label">Ваш коментар</label>
                    <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="3" placeholder="Напишіть коментар...">{{ old('body') }}</textarea>
                    @include('partials.field-error', ['field' => 'body'])
                    <button type="submit" class="btn btn-primary btn-sm mt-2">Надіслати</button>
                </form>
            @else
                <p class="text-muted small mb-3"><a href="{{ route('login') }}">Увійдіть</a>, щоб залишити коментар.</p>
            @endauth

            @forelse($recipe->comments->sortByDesc('created_at') as $comment)
                <div class="border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <strong>{{ $comment->user->name }}</strong>
                            <span class="text-muted small ms-2">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                            <p class="mb-0 mt-1">{{ $comment->body }}</p>
                        </div>
                        @auth
                            @if(auth()->user()->role === 'Admin')
                                <form method="post" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Видалити коментар?')">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Видалити</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Коментарів ще немає.</p>
            @endforelse
        </div>
    </div>
    </div>
@endsection
