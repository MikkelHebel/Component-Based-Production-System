<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\BatchRecipeStep;

class BatchController extends Controller
{

    public function execute(Request $request) {
        $batch = Batch::findOrFail($request->batch_id);

        $pendingSteps = BatchRecipeStep::where('batch_id', $batch->id)
            ->where('status', 'In Queue')
            ->with(['recipeStep' => fn($q) => $q->with('asset')->orderBy('step_order')])
            ->get()
            ->sortBy('recipeStep.step_order');

        foreach ($pendingSteps as $batchStep) {
            $recipeStep = $batchStep->recipeStep;

            $batchStep->update(['status' => 'In Progress']);

            $response = Http::post('http://orchestrator:5000/api/batch/execute', [
                'BatchId' => $batch->id,
                'RecipeStepId' => $recipeStep->id,
                'ComponentType' => $recipeStep->asset->name,
                'Command' => $recipeStep->command,
                'Parameters' => $recipeStep->parameters ? [$recipeStep->parameters] : [],
            ]);

            if ($response->successful()) {
                $batchStep->update(['status' => 'Done']);
            } else {
                $batchStep->update(['status' => 'Error']);
                return response()->json(['error' => 'orchestrator failed on step ' . $recipeStep->id], 500);
            }
        }

        $batch->update(['status' => 'Done']);
        return response()->json(['status' => 'Done']);
    }
}
