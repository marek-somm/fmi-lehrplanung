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
}
