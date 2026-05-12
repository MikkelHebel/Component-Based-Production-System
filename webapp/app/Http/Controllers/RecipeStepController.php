<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeSteps;

class RecipeStepController extends Controller
{
    public function destroy()
    {
        RecipeSteps::deleteOrFail($request->recipe_step->id);

        return back();
    }
}
