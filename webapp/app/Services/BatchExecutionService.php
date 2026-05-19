<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchRecipeStep;
use App\Models\Inventory;
use Illuminate\Support\Facades\Http;

class BatchExecutionService
{
    public function execute(int $batchId): void
    {
        $batch = Batch::findOrFail($batchId);

        $pendingSteps = BatchRecipeStep::where('batch_id', $batch->id)
            ->where('status', 'In Queue')
            ->with(['recipeStep' => fn($q) => $q->with('asset')->orderBy('step_order')])
            ->get()
            ->sortBy('recipeStep.step_order');

        foreach ($pendingSteps as $batchStep) {
            if ($batch->fresh()->status === 'Cancelled') break;

            $recipeStep = $batchStep->recipeStep;
            $batchStep->update(['status' => 'In Progress']);

            $response = Http::post('http://orchestrator:5000/api/batch/execute', [
                'BatchId'       => $batch->id,
                'RecipeStepId'  => $recipeStep->id,
                'ComponentType' => $recipeStep->asset->name,
                'Command'       => $recipeStep->command,
                'Parameters'    => $recipeStep->parameters ? [$recipeStep->parameters] : [],
            ]);

            if ($response->successful()) {
                $batchStep->update(['status' => 'Done']);

                if ($recipeStep->parameters && preg_match('/trayId=(\d+)/i', $recipeStep->parameters, $m)) {
                    $inventory = Inventory::where('tray_number', (int) $m[1])->first();
                    if ($inventory) {
                        stripos($recipeStep->command, 'pick') !== false
                            ? $inventory->decrement('quantity')
                            : $inventory->increment('quantity');
                    }
                }
            } else {
                $batchStep->update(['status' => 'Error']);
                $batch->update(['status' => 'Error']);
                return;
            }
        }

        if ($batch->fresh()->status !== 'Cancelled') {
            $batch->update(['status' => 'Done', 'end_time' => now()]);
        }
    }
}
