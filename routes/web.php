<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecipeController::class, 'home'])->name('home');

Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/add', [FavoriteController::class, 'add'])->name('favorites.add');
    Route::post('/favorites/remove', [FavoriteController::class, 'remove'])->name('favorites.remove');

    Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store'])->whereNumber('recipe')->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->whereNumber('comment')->name('comments.destroy');
    Route::post('/recipes/{recipe}/like', [ReactionController::class, 'like'])->whereNumber('recipe')->name('recipes.like');
    Route::post('/recipes/{recipe}/dislike', [ReactionController::class, 'dislike'])->whereNumber('recipe')->name('recipes.dislike');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/grant-redactor', [AdminController::class, 'grantRedactor'])->name('admin.grant-redactor');
    Route::post('/admin/revoke-redactor', [AdminController::class, 'revokeRedactor'])->name('admin.revoke-redactor');

    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->whereNumber('recipe')->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->whereNumber('recipe')->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->whereNumber('recipe')->name('recipes.destroy');
});

Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->whereNumber('recipe')->name('recipes.show');
