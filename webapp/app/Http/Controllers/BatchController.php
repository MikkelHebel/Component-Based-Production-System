<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\BatchRecipeStep;
use App\Models\RecipeStep;
use App\Jobs\RunBatchJob;

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

        $steps = RecipeStep::where('recipe_id', $request->recipe_id)->get();

        for ($unit = 1; $unit <= $batch->quantity; $unit++) {
            foreach ($steps as $step) {
                BatchRecipeStep::create([
                    'batch_id'       => $batch->id,
                    'recipe_step_id' => $step->id,
                    'quantity'       => $unit,
                    'status'         => 'In Queue',
                ]);
            }
        }

        return redirect()->route('dashboard');
    }

    public function start()
    {
        $batch = Batch::where('status', 'In Queue')
            ->orderBy('priority')
            ->first();

        if (!$batch) {
            return redirect()->route('dashboard');
        }

        $batch->update(['status' => 'In Progress', 'start_time' => now()]);
        RunBatchJob::dispatch($batch->id);

        return redirect()->route('dashboard');
    }

    public function progress()
    {
        return response()->json(
            Batch::with('batchRecipeSteps')
                ->whereIn('status', ['In Progress', 'In Queue'])
                ->get()
                ->map(fn($b) => [
                    'id'       => $b->id,
                    'status'   => $b->status,
                    'progress' => $b->batchRecipeSteps->count() > 0
                        ? (int) round($b->batchRecipeSteps->where('status', 'Done')->count() / $b->batchRecipeSteps->count() * 100)
                        : 0,
                ])
        );
    }

    public function stop()
    {
        $batch = Batch::whereIn('status', ['In Progress', 'In Queue'])
            ->orderBy('priority')
            ->first();

        if ($batch) {
            $batch->update(['status' => 'Cancelled']);
            BatchRecipeStep::where('batch_id', $batch->id)
                ->whereIn('status', ['In Queue', 'In Progress'])
                ->update(['status' => 'Cancelled']);
        }

        return redirect()->route('dashboard');
    }

}
