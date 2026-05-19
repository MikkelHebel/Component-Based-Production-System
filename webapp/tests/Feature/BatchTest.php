<?php

use App\Jobs\RunBatchJob;
use App\Models\Batch;
use App\Models\BatchRecipeStep;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->seed();
    $this->user = User::factory()->create();
});

test('queuing a batch creates steps for each unit', function () {
    $recipe = Recipe::first();

    $this->actingAs($this->user)
        ->post(route('batches.store'), ['recipe_id' => $recipe->id, 'quantity' => 3]);

    $batch = Batch::first();
    expect($batch->status)->toBe('In Queue');
    expect(BatchRecipeStep::where('batch_id', $batch->id)->count())->toBe(3 * $recipe->recipeSteps->count());
});

test('start marks highest priority batch as in progress and leaves others in queue', function () {
    Queue::fake();

    $recipe = Recipe::first();
    $high = Batch::create(['recipe_id' => $recipe->id, 'status' => 'In Queue', 'quantity' => 1, 'priority' => 1]);
    $low  = Batch::create(['recipe_id' => $recipe->id, 'status' => 'In Queue', 'quantity' => 1, 'priority' => 2]);

    $this->actingAs($this->user)->post(route('batches.start'));

    expect($high->fresh()->status)->toBe('In Progress');
    expect($low->fresh()->status)->toBe('In Queue');
    Queue::assertPushed(RunBatchJob::class, fn($job) => $job->batchId === $high->id);
});

test('stop cancels only the top priority batch', function () {
    $recipe = Recipe::first();
    $top   = Batch::create(['recipe_id' => $recipe->id, 'status' => 'In Progress', 'quantity' => 1, 'priority' => 1]);
    $other = Batch::create(['recipe_id' => $recipe->id, 'status' => 'In Queue',    'quantity' => 1, 'priority' => 2]);

    $this->actingAs($this->user)->post(route('batches.stop'));

    expect($top->fresh()->status)->toBe('Cancelled');
    expect($other->fresh()->status)->toBe('In Queue');
});
