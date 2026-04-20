<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BatchRecipeStep extends Pivot
{
    protected $fillable = [
        'status',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function recipeStep()
    {
        return $this->belongsTo(RecipeStep::class);
    }
}
