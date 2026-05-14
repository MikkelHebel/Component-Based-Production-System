<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    protected $fillable = [
        'recipe_id',
        'status',
        'quantity',
        'priority',
        'start_time',
        'end_time',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function batchRecipeSteps(): HasMany
    {
        return $this->hasMany(BatchRecipeStep::class);
    }
}
