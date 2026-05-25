@php
    $categories = ['Salad', 'FirstDishes', 'Breakfast', 'Starters', 'Desserts'];
    $categoryLabels = [
        'Salad' => 'Салати',
        'FirstDishes' => 'Перші страви',
        'Breakfast' => 'Сніданки',
        'Starters' => 'Закуски',
        'Desserts' => 'Десерти',
    ];
    $ingredientsValue = old('ingredients_text');
    if ($recipe && !$ingredientsValue) {
        $ingredientsValue = $recipe->ingredients->sortBy('id')->pluck('text')->implode("\n");
    }

    $formSteps = old('steps');
    if ($formSteps === null && $recipe) {
        $formSteps = $recipe->steps->sortBy('step_number')->map(fn($step) => [
            'text' => $step->text,
            'keep_photo' => $step->photo_path,
        ])->values()->all();
    }
    if (empty($formSteps)) {
        $formSteps = [['text' => '', 'keep_photo' => null]];
    }
@endphp

<div class="mb-3">
    <label class="form-label">Назва</label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $recipe->title ?? '') }}">
    @include('partials.field-error', ['field' => 'title'])
</div>

<div class="mb-3">
    <label class="form-label">Опис</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $recipe->description ?? '') }}</textarea>
    @include('partials.field-error', ['field' => 'description'])
</div>

<div class="mb-3">
    <label class="form-label">Категорія</label>
    <select name="category" class="form-select @error('category') is-invalid @enderror">
        <option value="">— оберіть —</option>
        @foreach($categories as $category)
            <option value="{{ $category }}" @selected(old('category', $recipe->category ?? '') === $category)>{{ $categoryLabels[$category] }}</option>
        @endforeach
    </select>
    @include('partials.field-error', ['field' => 'category'])
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Поживна цінність (на порцію, 100г)</label>
    <div class="row g-2">
        <div class="col-md-4">
            <label class="form-label small text-muted">Жири</label>
            <input type="number" step="0.1" min="0" name="fats" class="form-control @error('fats') is-invalid @enderror" value="{{ old('fats', $recipe->fats ?? 0) }}">
            @include('partials.field-error', ['field' => 'fats'])
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted">Вуглеводи</label>
            <input type="number" step="0.1" min="0" name="carbs" class="form-control @error('carbs') is-invalid @enderror" value="{{ old('carbs', $recipe->carbs ?? 0) }}">
            @include('partials.field-error', ['field' => 'carbs'])
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted">Білки</label>
            <input type="number" step="0.1" min="0" name="proteins" class="form-control @error('proteins') is-invalid @enderror" value="{{ old('proteins', $recipe->proteins ?? 0) }}">
            @include('partials.field-error', ['field' => 'proteins'])
        </div>
    </div>
    <div class="form-text">Калорії розраховуються автоматично: білки×4 + вуглеводи×4 + жири×9</div>
</div>

<div class="mb-3">
    <label class="form-label">{{ $recipe ? 'Нове фото (необов\'язково)' : 'Фото рецепта' }}</label>
    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
    @include('partials.field-error', ['field' => 'photo'])
</div>

<div class="mb-3">
    <label class="form-label">Інгредієнти (кожен з нового рядка)</label>
    <textarea name="ingredients_text" class="form-control @error('ingredients_text') is-invalid @enderror" rows="6">{{ $ingredientsValue }}</textarea>
    @include('partials.field-error', ['field' => 'ingredients_text'])
</div>

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0">Етапи приготування</label>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-step-btn">+ Додати етап</button>
    </div>
    @error('steps')
        <div class="text-danger small mb-2">{{ $message }}</div>
    @enderror
    <div id="steps-container">
        @foreach($formSteps as $index => $step)
            <div class="card mb-2 step-row border">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge text-bg-secondary step-label">Етап {{ $index + 1 }}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-step-btn" @if(count($formSteps) <= 1) style="display:none" @endif>×</button>
                    </div>
                    <textarea name="steps[{{ $index }}][text]" class="form-control mb-2 @error('steps.'.$index.'.text') is-invalid @enderror" rows="2" placeholder="Опишіть етап...">{{ $step['text'] ?? '' }}</textarea>
                    @error('steps.'.$index.'.text')
                        <div class="text-danger small d-block mb-2">{{ $message }}</div>
                    @enderror
                    @if(!empty($step['keep_photo']))
                        <input type="hidden" name="steps[{{ $index }}][keep_photo]" value="{{ $step['keep_photo'] }}">
                        <div class="mb-2">
                            <img src="{{ $step['keep_photo'] }}" alt="Фото етапу" class="step-photo-preview rounded">
                        </div>
                    @endif
                    <label class="form-label small text-muted mb-1">Фото етапу (необов'язково)</label>
                    <input type="file" name="steps[{{ $index }}][photo]" class="form-control form-control-sm" accept="image/*">
                    @error('steps.'.$index.'.photo')
                        <div class="text-danger small d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endforeach
    </div>
</div>

<template id="step-row-template">
    <div class="card mb-2 step-row border">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge text-bg-secondary step-label">Етап</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-step-btn">×</button>
            </div>
            <textarea name="steps[__INDEX__][text]" class="form-control mb-2" rows="2" placeholder="Опишіть етап..."></textarea>
            <label class="form-label small text-muted mb-1">Фото етапу (необов'язково)</label>
            <input type="file" name="steps[__INDEX__][photo]" class="form-control form-control-sm" accept="image/*">
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('steps-container');
    const template = document.getElementById('step-row-template');
    const addBtn = document.getElementById('add-step-btn');

    function reindexSteps() {
        const rows = container.querySelectorAll('.step-row');
        rows.forEach((row, index) => {
            row.querySelector('.step-label').textContent = 'Етап ' + (index + 1);
            row.querySelectorAll('[name^="steps["]').forEach((el) => {
                el.name = el.name.replace(/steps\[\d+\]/, 'steps[' + index + ']');
            });
            const removeBtn = row.querySelector('.remove-step-btn');
            if (removeBtn) {
                removeBtn.style.display = rows.length > 1 ? '' : 'none';
            }
        });
    }

    addBtn.addEventListener('click', function () {
        const index = container.querySelectorAll('.step-row').length;
        const html = template.innerHTML.replace(/__INDEX__/g, index);
        container.insertAdjacentHTML('beforeend', html);
        reindexSteps();
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-step-btn')) {
            e.target.closest('.step-row').remove();
            reindexSteps();
        }
    });
});
</script>
