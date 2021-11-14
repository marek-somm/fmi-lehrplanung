<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AffiliationUser extends Pivot {
    protected $fillable = [
    ];

    protected $casts = [
        'affiliation_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
