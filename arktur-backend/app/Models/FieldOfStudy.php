<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// aka Studiengang
class FieldOfStudy extends Model
{
    protected $fillable = [
        'active_from',
        'active_to',
        'po_version',
        'name',
        'name_short'
    ];

    protected $casts = [
        'active_from' => 'integer',
        'active_to' => 'integer',
        'po_version' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function degree() {
        return $this->belongsTo(Degree::class);
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }
}
