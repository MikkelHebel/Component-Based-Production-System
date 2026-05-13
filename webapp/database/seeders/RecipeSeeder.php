<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Recipe;
use App\Models\RecipeStep;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse       = Asset::firstOrCreate(['name' => 'Warehouse'],       ['connection_status' => 'unknown']);
        $agv             = Asset::firstOrCreate(['name' => 'AGV'],             ['connection_status' => 'unknown']);
        $assemblyStation = Asset::firstOrCreate(['name' => 'AssemblyStation'], ['connection_status' => 'unknown']);

        $recipe = Recipe::firstOrCreate(['name' => 'Drone Assembly']);

        $steps = [
            ['asset' => $warehouse,       'command' => 'PickItem',                'parameters' => 'trayId=1'],
            ['asset' => $agv,             'command' => 'PickWarehouseOperation',  'parameters' => null],
            ['asset' => $agv,             'command' => 'MoveToAssemblyOperation', 'parameters' => null],
            ['asset' => $agv,             'command' => 'PutAssemblyOperation',    'parameters' => null],
            ['asset' => $assemblyStation, 'command' => 'StartProcess',            'parameters' => 'processId=drone_assembly'],
            ['asset' => $agv,             'command' => 'PickAssemblyOperation',   'parameters' => null],
            ['asset' => $agv,             'command' => 'MoveToStorageOperation',  'parameters' => null],
        ];

        foreach ($steps as $order => $step) {
            RecipeStep::firstOrCreate(
                ['recipe_id' => $recipe->id, 'step_order' => $order + 1],
                ['command' => $step['command'], 'parameters' => $step['parameters'], 'asset_id' => $step['asset']->id]
            );
        }
    }
}
