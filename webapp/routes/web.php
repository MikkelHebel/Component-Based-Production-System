<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeStepController;
use App\Http\Controllers\BatchController;

Route::get('/', [AuthController::class, 'show'])->name('login');
Route::post('/', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
    Route::post('/batches/start', [BatchController::class, 'start'])->name('batches.start');

    Route::get('/config', [ConfigurationController::class, 'show'])->name('config');
    Route::post('/recipe', [RecipeController::class, 'store'])->name('recipe.store');
    Route::post('/recipeSteps', [RecipeStepController::class, 'store'])->name('recipesteps.store');
    Route::delete('/recipeSteps/{id}', [RecipeStepController::class, 'destroy'])->name('recipesteps.destroy');
});
