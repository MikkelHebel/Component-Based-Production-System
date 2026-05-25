<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Batch;
use App\Models\BatchRecipeStep;
use App\Models\Inventory;
use App\Models\Item;
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
            ->sortBy(fn($brs) => [$brs->quantity, $brs->recipeStep->step_order]);

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

                // When all steps for this unit are done, add one finished product to the warehouse
                $unitNumber = $batchStep->quantity;
                $totalForUnit = BatchRecipeStep::where('batch_id', $batch->id)->where('quantity', $unitNumber)->count();
                $doneForUnit = BatchRecipeStep::where('batch_id', $batch->id)->where('quantity', $unitNumber)->where('status', 'Done')->count();
                if ($doneForUnit === $totalForUnit) {
                    $this->storeFinishedProduct($batch);
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

    private function storeFinishedProduct(Batch $batch): void
    {
        $warehouse = Asset::where('name', 'Warehouse')->first();
        if (!$warehouse) return;

        $product = Item::firstOrCreate(
            ['name' => $batch->recipe->name],
            ['type' => 'product'],
        );

        $inventory = Inventory::where('item_id', $product->id)
            ->where('asset_id', $warehouse->id)
            ->first();

        if ($inventory) {
            $inventory->increment('quantity');
        } else {
            $nextTray = (Inventory::max('tray_number') ?? 0) + 1;
            Inventory::create([
                'item_id'     => $product->id,
                'asset_id'    => $warehouse->id,
                'tray_number' => $nextTray,
                'quantity'    => 1,
            ]);
        }
    }
}
