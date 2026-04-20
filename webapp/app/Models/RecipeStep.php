<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeStep extends Model
{
    protected $fillable = [
        'step_order',
        'command',
        'parameters',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function batchRecipeStep()
    {
        return $this->hasMany(BatchRecipeStep::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function recipeItem()
    {
        return $this->hasMany(RecipeItem::class);
    }
}
