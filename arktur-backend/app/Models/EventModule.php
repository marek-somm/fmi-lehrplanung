<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventModule extends Pivot {
    protected $fillable = [
        'pnr',
        'description',
        'title',
        'changed'
    ];

    protected $casts = [
        'changed' => 'boolean',
        'pnr' => 'integer',
        'event_id' => 'integer',
        'module_id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
