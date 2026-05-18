<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchRecipeStep extends Pivot
{
    protected $table = 'batch_recipe_steps';
    public $timestamps = false;

    protected $fillable = [
        'batch_id',
        'recipe_step_id',
        'quantity',
        'status',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function recipeStep(): BelongsTo
    {
        return $this->belongsTo(RecipeStep::class);
    }
}
