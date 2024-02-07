<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable = [
        'value'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function field_of_studies() {
        return $this->hasMany(FieldOfStudy::class);
    }
}
