<?php

use App\Models\Batch;
use App\Models\BatchRecipeStep;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Services\BatchExecutionService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->seed();

    $recipe = Recipe::first();
    $this->batch = Batch::create([
        'recipe_id'  => $recipe->id,
        'status'     => 'In Progress',
        'quantity'   => 1,
        'priority'   => 1,
        'start_time' => now(),
    ]);

    $steps = RecipeStep::where('recipe_id', $recipe->id)->get();
    foreach ($steps as $step) {
        BatchRecipeStep::create([
            'batch_id'       => $this->batch->id,
            'recipe_step_id' => $step->id,
            'quantity'       => 1,
            'status'         => 'In Queue',
        ]);
    }

    $this->totalSteps = $steps->count();
});

test('all steps execute, batch is marked done, inventory is decremented for pick steps, and finished product is added to warehouse', function () {
    Http::fake(['http://orchestrator:5000/*' => Http::response([], 200)]);

    $inventoryBefore = Inventory::where('tray_number', 1)->value('quantity');

    app(BatchExecutionService::class)->execute($this->batch->id);

    expect($this->batch->fresh()->status)->toBe('Done');
    expect($this->batch->fresh()->end_time)->not->toBeNull();
    expect(BatchRecipeStep::where('batch_id', $this->batch->id)->where('status', 'Done')->count())->toBe($this->totalSteps);
    expect(Inventory::where('tray_number', 1)->value('quantity'))->toBe($inventoryBefore - 1);

    $product = Item::where('name', Recipe::first()->name)->where('type', 'product')->first();
    expect($product)->not->toBeNull();
    expect(Inventory::where('item_id', $product->id)->value('quantity'))->toBe(1);
});

test('each unit is added to warehouse individually as it completes', function () {
    Http::fake(['http://orchestrator:5000/*' => Http::response([], 200)]);

    $recipe = Recipe::first();
    $steps = RecipeStep::where('recipe_id', $recipe->id)->get();
    $quantity = 3;

    $batch = Batch::create([
        'recipe_id' => $recipe->id,
        'status' => 'In Progress',
        'quantity' => $quantity,
        'priority' => 2,
        'start_time' => now(),
    ]);

    for ($unit = 1; $unit <= $quantity; $unit++) {
        foreach ($steps as $step) {
            BatchRecipeStep::create([
                'batch_id' => $batch->id,
                'recipe_step_id' => $step->id,
                'quantity' => $unit,
                'status' => 'In Queue',
            ]);
        }
    }

    app(BatchExecutionService::class)->execute($batch->id);

    expect($batch->fresh()->status)->toBe('Done');

    $product = Item::where('name', $recipe->name)->where('type', 'product')->first();
    expect($product)->not->toBeNull();
    expect(Inventory::where('item_id', $product->id)->value('quantity'))->toBe($quantity);
});

test('failing step marks step and batch as error and stops remaining steps', function () {
    Http::fake(['http://orchestrator:5000/*' => Http::response([], 500)]);

    app(BatchExecutionService::class)->execute($this->batch->id);

    expect($this->batch->fresh()->status)->toBe('Error');
    expect(BatchRecipeStep::where('batch_id', $this->batch->id)->where('status', 'Error')->count())->toBe(1);
    expect(BatchRecipeStep::where('batch_id', $this->batch->id)->where('status', 'In Queue')->count())->toBe($this->totalSteps - 1);
});
