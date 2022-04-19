<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// aka "Konto"
class Category extends Model
{
    protected $fillable = [
        'name',
        'obligational',
        'nr'
    ];

    protected $casts = [
        'obligational' => 'boolean',
        'nr' => 'integer'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function parent() {
        return $this->belongsTo(Category::class);
    }

    public function children() {
        return $this->hasMany(Category::class);
    }

    public function field_of_study() {
        return $this->belongsTo(FieldOfStudy::class);
    }

    public function modules() {
        return $this->belongsToMany(Module::class);
    }
}
