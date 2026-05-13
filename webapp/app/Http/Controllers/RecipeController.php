<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'recipe_name' => 'required|string'
        ]);

        if (Recipe::where('name', $request->recipe_name)->exists()) {
            return back()->withErrors(['recipe_name' => 'Recipe with that name already exists.']);
        }

        Recipe::create([
            'name' => $request->recipe_name,
        ]);

        return back()->with('success', 'Recipe Added.');
    }
}
