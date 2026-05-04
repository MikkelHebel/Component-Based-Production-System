<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeItem extends Pivot
{
    protected $fillable = [
        'quantity',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function recipeStep(): BelongsTo
    {
        return $this->belongsTo(RecipeStep::class);
    }
}
