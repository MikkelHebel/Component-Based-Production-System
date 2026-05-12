<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class ConfigurationController extends Controller
{
    public function show(Request $request)
    {
        $recipes= Recipe::all();
        $selected = Recipe::find(request('recipe'));

        return view('dashboard.configuration', compact('recipes', 'selected'));
    }
}
