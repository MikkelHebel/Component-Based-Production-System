<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Http;

class ConfigurationController extends Controller
{
    public function show(Request $request)
    {
        $recipes = Recipe::all();
        $selected = Recipe::find($request->query('recipe'));

        $components = Http::get('http://orchestrator:5000/api/components/commands')->json() ?? [];

        $selectedComponentType = $request->query('component');
        $commands = collect($components)->firstWhere('componentType', $selectedComponentType)['supportedCommands'] ?? [];

        return view('dashboard.configuration', compact('recipes', 'selected', 'components', 'selectedComponentType', 'commands'));
    }
}
