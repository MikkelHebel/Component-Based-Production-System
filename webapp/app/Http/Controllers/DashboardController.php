<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Batch;
use App\Models\Inventory;
use App\Models\Recipe;

class DashboardController extends Controller
{
    public function show()
    {
        $activeBatches = Batch::with(['recipe', 'batchRecipeSteps'])
            ->whereIn('status', ['In Progress', 'In Queue'])
            // Put batches In Progress to the front
            ->orderByRaw("CASE status WHEN 'In Progress' THEN 0 ELSE 1 END")
            ->orderBy('priority')
            ->get()
            // Calculate progress
            ->map(function (Batch $batch) {
                $total = $batch->batchRecipeSteps->count();
                $done = $batch->batchRecipeSteps->where('status', 'Done')->count();
                $batch->progress = $total > 0 ? (int) round($done / $total * 100) : 0;
                return $batch;
            });

        $completedBatches = Batch::with('recipe')
            ->whereIn('status', ['Done', 'Error', 'Cancelled'])
            ->orderByDesc('end_time')
            ->limit(10)
            ->get();

        $inventory = Inventory::with(['item', 'asset'])
            ->orderBy('tray_number')
            ->get()
            ->groupBy('asset_id');

        $assets = Asset::all();
        // $onlineCount = $assets->where('connection_status', 'connected')->count();
        // $systemStatus = match(true) {
        //     $assets->isEmpty() => 'unknown',
        //     $onlineCount == $assets->count() => 'online',
        //     $onlineCount > 0 => 'partial',
        //     default => 'offline',
        // };
        $systemStatus = 'online';
        $recipes = Recipe::all();

        return view('dashboard.index', compact('activeBatches', 'completedBatches', 'inventory', 'assets', 'systemStatus', 'recipes'));
    }
}
