<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RecipeItem extends Pivot
{
    protected $fillable = [
        'quantity',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function recipeStep()
    {
        return $this->belongsTo(RecipeStep::class);
    }
}
