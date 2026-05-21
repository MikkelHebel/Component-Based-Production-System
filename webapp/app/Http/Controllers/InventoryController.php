<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:part,product',
            'tray_number' => 'required|integer|min:1|unique:inventories,tray_number',
            'quantity' => 'required|integer|min:0',
        ]);

        $warehouse = Asset::where('name', 'Warehouse')->firstOrFail();

        $item = Item::firstOrCreate(
            ['name' => $request->name],
            ['type' => $request->type],
        );

        Inventory::create([
            'item_id' => $item->id,
            'asset_id' => $warehouse->id,
            'tray_number' => $request->tray_number,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Item added to warehouse.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return back()->with('success', 'Item removed from warehouse.');
    }
}
