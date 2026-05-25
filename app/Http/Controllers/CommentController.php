<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:1000'],
        ], $this->messages());

        RecipeComment::create([
            'recipe_id' => $recipe->id,
            'user_id' => Auth::id(),
            'body' => trim($data['body']),
        ]);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('ok', 'Коментар додано.');
    }

    public function destroy(RecipeComment $comment)
    {
        abort_unless(Auth::user() && Auth::user()->role === 'Admin', 403);

        $recipe = $comment->recipe;
        $comment->delete();

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('ok', 'Коментар видалено.');
    }

    private function messages(): array
    {
        return [
            'body.required' => 'Введіть текст коментаря.',
            'body.min' => 'Коментар має містити щонайменше 2 символи.',
            'body.max' => 'Коментар не може перевищувати 1000 символів.',
        ];
    }
}
