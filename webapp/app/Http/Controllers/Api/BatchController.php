<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Services\BatchExecutionService;

class BatchController extends Controller
{
    public function execute(Request $request, BatchExecutionService $executor)
    {
        $batch = Batch::findOrFail($request->batch_id);
        $batch->update(['status' => 'In Progress', 'start_time' => now()]);

        $executor->execute($batch->id);

        return response()->json(['status' => $batch->fresh()->status]);
    }
}
