<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'batch_id',
        'asset_id',
        'message',
        'timestamp',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset:class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
