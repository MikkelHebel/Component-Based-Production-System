<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Asset::firstOrCreate(
            ['name' => 'Warehouse'],
            ['connection_status' => 'unknown']
        );

        $parts = [
            ['name' => 'Motor',                      'type' => 'part', 'quantity' => 120, 'tray' => 1],
            ['name' => 'Frame',                      'type' => 'part', 'quantity' => 45,  'tray' => 2],
            ['name' => 'Propeller',                  'type' => 'part', 'quantity' => 200, 'tray' => 3],
            ['name' => 'ESC (Speed Controller)',     'type' => 'part', 'quantity' => 30,  'tray' => 4],
            ['name' => 'Flight Controller',          'type' => 'part', 'quantity' => 18,  'tray' => 5],
            ['name' => 'LiPo Battery',               'type' => 'part', 'quantity' => 8,   'tray' => 6],
            ['name' => 'FPV Camera',                 'type' => 'part', 'quantity' => 0,   'tray' => 7],
            ['name' => 'GPS Module',                 'type' => 'part', 'quantity' => 55,  'tray' => 8],
        ];

        foreach ($parts as $part) {
            $item = Item::firstOrCreate(
                ['name' => $part['name']],
                ['type' => $part['type']]
            );

            Inventory::firstOrCreate(
                ['tray_number' => $part['tray']],
                ['item_id' => $item->id, 'asset_id' => $warehouse->id, 'quantity' => $part['quantity']]
            );
        }
    }
}
