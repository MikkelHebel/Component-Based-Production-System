<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class BatchController extends Controller
{

    public function execute(Request $request) {
        $pendingSteps = $batch->batchRecipeSteps()
            ->where('status', 'pending')
            ->with('recipeStep.asset')
            ->join('recipe_steps', 'batch_recipe_steps.recipe_step_id', '=', 'recipe_steps.id')
            ->orderBy('recipe_steps.step_order')
            ->get();

        foreach ($pendingSteps as $batchStep) {
            $recipeStep = $batchStep->recipeStep;

            $batchStep->update(['status' => 'in_progress']);

            $response = Http::post('http://orchestrator:5001/api/batch/execute', [
                'batchId' => $batch->id,
                'recipeStepId' => $recipeStep->id,
                'componentType' => $recipeStep->asset->name,
                'command' => $recipeStep->command,
                'parameters' => $recipeStep->parameters ?? [],
            ]);

            if ($response->successful()) {
                $batchStep->update(['status' => 'completed']);
            } else {
                $batchStep->update(['status' => 'failed']);
                return response()->json(['error' => 'orchestrator failed on step ' . $recipeStep->id], 500);
            }
        }

        $batch->update(['status' => 'completed']);
        return response()->json(['status' => 'completed']);
    }
}
