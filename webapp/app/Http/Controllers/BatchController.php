<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\BatchRecipeStep;
use App\Models\RecipeStep;
use App\Services\BatchExecutionService;

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

    public function start(BatchExecutionService $executor)
    {
        $batch = Batch::where('status', 'In Queue')
            ->orderBy('priority')
            ->first();

        if (!$batch) {
            return redirect()->route('dashboard');
        }

        $batch->update(['status' => 'In Progress', 'start_time' => now()]);

        $executor->execute($batch->id);

        return redirect()->route('dashboard');
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
