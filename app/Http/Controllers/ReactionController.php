<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function like(Request $request, Recipe $recipe)
    {
        return $this->toggle($recipe, 'like');
    }

    public function dislike(Request $request, Recipe $recipe)
    {
        return $this->toggle($recipe, 'dislike');
    }

    private function toggle(Recipe $recipe, string $type): \Illuminate\Http\RedirectResponse
    {
        $userId = Auth::id();
        $existing = RecipeReaction::query()
            ->where('recipe_id', $recipe->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing && $existing->type === $type) {
            $existing->delete();
        } elseif ($existing) {
            $existing->update(['type' => $type]);
        } else {
            RecipeReaction::create([
                'recipe_id' => $recipe->id,
                'user_id' => $userId,
                'type' => $type,
            ]);
        }

        return redirect()->route('recipes.show', $recipe);
    }
}
