<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Support\ValidationMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RecipeController extends Controller
{
    private const CATEGORIES = ['Salad', 'FirstDishes', 'Breakfast', 'Starters', 'Desserts'];

    public function home()
    {
        $newRecipes = Recipe::query()->latest()->take(6)->get();
        return view('home', compact('newRecipes'));
    }

    public function index(Request $request)
    {
        $query = Recipe::query();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('q')) {
            $q = '%' . trim((string) $request->string('q')) . '%';
            $query->where(function ($inner) use ($q) {
                $inner->where('title', 'ilike', $q)
                    ->orWhere('description', 'ilike', $q)
                    ->orWhereHas('ingredients', function ($ingredientsQuery) use ($q) {
                        $ingredientsQuery->where('text', 'ilike', $q);
                    })
                    ->orWhereHas('steps', function ($stepsQuery) use ($q) {
                        $stepsQuery->where('text', 'ilike', $q);
                    });
            });
        }

        $recipes = $query->latest()->get();
        $favoriteIds = [];

        if (Auth::check()) {
            $favoriteIds = Auth::user()
                ->favoriteRecipes()
                ->pluck('recipe_id')
                ->all();
        }

        return view('recipes.index', [
            'recipes' => $recipes,
            'favoriteIds' => $favoriteIds,
            'query' => $request->string('q'),
            'currentCategory' => $request->string('category'),
        ]);
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['ingredients', 'steps', 'comments.user', 'reactions']);
        $isFavorite = false;
        $userReaction = null;

        if (Auth::check()) {
            $isFavorite = Auth::user()
                ->favoriteRecipes()
                ->where('recipe_id', $recipe->id)
                ->exists();

            $userReaction = $recipe->reactions
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('recipes.show', [
            'recipe' => $recipe,
            'isFavorite' => $isFavorite,
            'userReaction' => $userReaction,
            'likesCount' => $recipe->likesCount(),
            'dislikesCount' => $recipe->dislikesCount(),
        ]);
    }

    public function create()
    {
        $this->authorizeAdminOrRedactor();
        return view('recipes.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdminOrRedactor();

        $data = $request->validate($this->recipeRules(requirePhoto: true), ValidationMessages::recipe());
        $this->validateIngredientsAndSteps($request, $data);

        DB::transaction(function () use ($data, $request) {
            $photoPath = $request->file('photo')->store('recipes', 'public');
            $macros = $this->extractMacros($data);

            $recipe = Recipe::create([
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'category' => $data['category'],
                'fats' => $macros['fats'],
                'carbs' => $macros['carbs'],
                'proteins' => $macros['proteins'],
                'calories' => $macros['calories'],
                'photo_path' => '/storage/' . $photoPath,
            ]);

            foreach ($this->splitLines($data['ingredients_text']) as $line) {
                $recipe->ingredients()->create(['text' => $line]);
            }

            $this->syncSteps($recipe, $request->input('steps', []), $request);
        });

        return redirect()->route('recipes.index')->with('ok', 'Рецепт створено.');
    }

    public function edit(Recipe $recipe)
    {
        $this->authorizeAdminOrRedactor();
        $recipe->load(['ingredients', 'steps']);
        return view('recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $this->authorizeAdminOrRedactor();

        $data = $request->validate($this->recipeRules(requirePhoto: false), ValidationMessages::recipe());
        $this->validateIngredientsAndSteps($request, $data);

        DB::transaction(function () use ($data, $request, $recipe) {
            $macros = $this->extractMacros($data);
            $payload = [
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'category' => $data['category'],
                'fats' => $macros['fats'],
                'carbs' => $macros['carbs'],
                'proteins' => $macros['proteins'],
                'calories' => $macros['calories'],
            ];

            if ($request->hasFile('photo')) {
                $this->deleteStorageFile($recipe->photo_path);
                $payload['photo_path'] = '/storage/' . $request->file('photo')->store('recipes', 'public');
            }

            $recipe->update($payload);
            $recipe->ingredients()->delete();

            foreach ($this->splitLines($data['ingredients_text']) as $line) {
                $recipe->ingredients()->create(['text' => $line]);
            }

            $this->replaceSteps($recipe, $request->input('steps', []), $request);
        });

        return redirect()->route('recipes.show', $recipe)->with('ok', 'Рецепт оновлено.');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorizeAdminOrRedactor();

        $recipe->load('steps');
        $this->deleteStorageFile($recipe->photo_path);
        foreach ($recipe->steps as $step) {
            $this->deleteStorageFile($step->photo_path);
        }

        $recipe->delete();
        return redirect()->route('recipes.index')->with('ok', 'Рецепт видалено.');
    }

    private function recipeRules(bool $requirePhoto): array
    {
        $photoRule = $requirePhoto
            ? ['required', 'image', 'max:4096']
            : ['nullable', 'image', 'max:4096'];

        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'category' => ['required', 'string', Rule::in(self::CATEGORIES)],
            'fats' => ['required', 'numeric', 'min:0', 'max:10000'],
            'carbs' => ['required', 'numeric', 'min:0', 'max:10000'],
            'proteins' => ['required', 'numeric', 'min:0', 'max:10000'],
            'photo' => $photoRule,
            'ingredients_text' => ['required', 'string'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.text' => ['required', 'string', 'max:2000'],
            'steps.*.photo' => ['nullable', 'image', 'max:4096'],
            'steps.*.keep_photo' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function validateIngredientsAndSteps(Request $request, array $data): void
    {
        $ingredients = $this->splitLines($data['ingredients_text']);
        if (count($ingredients) === 0) {
            $request->validate(['ingredients_text' => ['required']], [
                'ingredients_text.required' => 'Додайте хоча б один інгредієнт (кожен з нового рядка).',
            ]);
        }

        $steps = collect($data['steps'] ?? [])->filter(fn($step) => trim((string) ($step['text'] ?? '')) !== '');
        if ($steps->isEmpty()) {
            $request->validate(['steps' => ['required', 'array', 'min:1']], ValidationMessages::recipe());
        }
    }

    private function extractMacros(array $data): array
    {
        $fats = (float) $data['fats'];
        $carbs = (float) $data['carbs'];
        $proteins = (float) $data['proteins'];

        return [
            'fats' => $fats,
            'carbs' => $carbs,
            'proteins' => $proteins,
            'calories' => Recipe::calculateCalories($fats, $carbs, $proteins),
        ];
    }

    private function replaceSteps(Recipe $recipe, array $steps, Request $request): void
    {
        $oldPaths = $recipe->steps()->whereNotNull('photo_path')->pluck('photo_path')->all();
        $recipe->steps()->delete();
        $this->syncSteps($recipe, $steps, $request);

        $keptPaths = $recipe->steps()->whereNotNull('photo_path')->pluck('photo_path')->all();
        foreach ($oldPaths as $oldPath) {
            if (!in_array($oldPath, $keptPaths, true)) {
                $this->deleteStorageFile($oldPath);
            }
        }
    }

    private function syncSteps(Recipe $recipe, array $steps, Request $request): void
    {
        $stepNumber = 1;
        foreach ($steps as $index => $stepData) {
            $text = trim((string) ($stepData['text'] ?? ''));
            if ($text === '') {
                continue;
            }

            $photoPath = null;
            $uploadedFile = $this->stepPhotoFile($request, $index);
            if ($uploadedFile) {
                $stored = $uploadedFile->store('recipe-steps', 'public');
                $photoPath = '/storage/' . $stored;
            } elseif (!empty($stepData['keep_photo'])) {
                $photoPath = $stepData['keep_photo'];
            }

            $recipe->steps()->create([
                'step_number' => $stepNumber,
                'text' => $text,
                'photo_path' => $photoPath,
            ]);
            $stepNumber++;
        }
    }

    private function stepPhotoFile(Request $request, int|string $index): ?\Illuminate\Http\UploadedFile
    {
        $steps = $request->file('steps');
        if (!is_array($steps) || !isset($steps[$index])) {
            return null;
        }

        $file = $steps[$index]['photo'] ?? null;

        return $file instanceof \Illuminate\Http\UploadedFile && $file->isValid() ? $file : null;
    }

    private function splitLines(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_map(fn($line) => trim((string) $line), $lines);
        return array_values(array_filter($lines, fn($line) => $line !== ''));
    }

    private function deleteStorageFile(?string $path): void
    {
        if (!$path) {
            return;
        }
        $relative = str_replace('/storage/', '', $path);
        Storage::disk('public')->delete($relative);
    }

    private function authorizeAdminOrRedactor(): void
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['Admin', 'Redactor'], true), 403);
    }
}
