<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\BatchRecipeStep;
use App\Models\RecipeStep;

class BatchController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $batch = Batch::create([
            'recipe_id' => $request->recipe_id,
            'quantity' => $request->quantity,
            'status' => 'In Queue',
            'priority' => Batch::max('priority') + 1,
        ]);

        foreach (RecipeStep::where('recipe_id', $request->recipe_id)->get() as $step) {
            BatchRecipeStep::create([
                'batch_id'       => $batch->id,
                'recipe_step_id' => $step->id,
                'status'         => 'In Queue',
            ]);
        }

        return redirect()->route('dashboard');
    }

}
