<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'fats' => 'float',
            'carbs' => 'float',
            'proteins' => 'float',
            'calories' => 'float',
        ];
    }

    protected $fillable = [
        'title',
        'description',
        'category',
        'photo_path',
        'calories',
        'fats',
        'carbs',
        'proteins',
    ];

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function steps()
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteRecipe::class);
    }

    public function comments()
    {
        return $this->hasMany(RecipeComment::class);
    }

    public function reactions()
    {
        return $this->hasMany(RecipeReaction::class);
    }

    public static function calculateCalories(float $fats, float $carbs, float $proteins): float
    {
        return round($proteins * 4 + $carbs * 4 + $fats * 9, 1);
    }

    public function likesCount(): int
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function dislikesCount(): int
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }
}
