<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model {
    protected $fillable = [
        'name',
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
