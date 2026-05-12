<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeStep;
use App\Models\Asset;

class RecipeStepController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'parameters' => 'nullable|string',
            'recipe_id' => 'required|exists:recipes,id',
            'component' => 'required|string',
        ]);

        $asset = Asset::where('name', $request->component)->firstOrFail();
        $stepOrder = RecipeStep::where('recipe_id', $request->recipe_id)->max('step_order') + 1;

        RecipeStep::create([
            'step_order' => $stepOrder,
            'command' => $request->command,
            'parameters' => $request->parameters,
            'asset_id' => $asset->id,
            'recipe_id' => $request->recipe_id,
        ]);

        return back()->with('success', 'Recipe Step created.');
    }

    public function destroy(int $id)
    {
        RecipeStep::findOrFail($id)->delete();
        return back();
    }
}
