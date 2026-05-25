<?php

namespace App\Http\Controllers;

use App\Models\FavoriteRecipe;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $recipes = Recipe::query()
            ->whereIn('id', Auth::user()->favoriteRecipes()->pluck('recipe_id'))
            ->latest()
            ->get();

        return view('favorites.index', compact('recipes'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => ['required', 'integer', 'exists:recipes,id'],
            'return_url' => ['nullable', 'string'],
        ]);

        FavoriteRecipe::query()->firstOrCreate([
            'user_id' => Auth::id(),
            'recipe_id' => $data['recipe_id'],
        ]);

        return redirect($data['return_url'] ?? route('favorites.index'));
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => ['required', 'integer'],
            'return_url' => ['nullable', 'string'],
        ]);

        FavoriteRecipe::query()
            ->where('user_id', Auth::id())
            ->where('recipe_id', $data['recipe_id'])
            ->delete();

        return redirect($data['return_url'] ?? route('favorites.index'));
    }
}
