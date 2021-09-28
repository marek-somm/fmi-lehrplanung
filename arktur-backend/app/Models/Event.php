<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'vnr',
        'semester',
        'title',
        'aktiv',
        'sws',
        'type',
        'targets',
        'rythm',
        'changed'
    ];

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function modules() {
        return $this->belongsToMany(Module::class)->withPivot('pnr', 'description', 'title', 'changed')->as('exams')->withTimestamps();
    }
}
