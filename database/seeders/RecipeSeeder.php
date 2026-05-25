<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipesData = [
            [

            ]
        ];

        foreach ($recipesData as $data) {
            $recipe = Recipe::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'calories' => $data['calories'],
                'fats' => $data['fats'],
                'carbs' => $data['carbs'],
                'proteins' => $data['proteins'],
                // 'photo_path' => null // за замовчуванням
            ]);

            foreach ($data['ingredients'] as $ingredientText) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'text' => $ingredientText
                ]);
            }

            foreach ($data['steps'] as $index => $stepText) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $index + 1,
                    'text' => $stepText
                ]);
            }
        }
    }
}
