<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model {
    protected $fillable = [
        'name',
        'name_medium',
        'name_short'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function field_of_studies() {
        return $this->hasMany(FieldOfStudy::class);
    }
}
