<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    use HasFactory;

    protected $fillable = [
        'vnr',
        'semester',
        'title',
        'active',
        'sws',
        'type',
        'targets',
        'rotation',
        'changed'
    ];

    protected $casts = [
        'vnr' => 'integer',
        'semester' => 'integer',
        'active' => 'boolean',
        'sws' => 'integer',
        'rotation' => 'integer',
        'changed' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function users() {
        return $this->belongsToMany(User::class)->using(EventUser::class)->withTimestamps();
    }

    public function modules() {
        return $this->belongsToMany(Module::class)->withPivot('pnr', 'description', 'title')->using(EventModule::class)->withTimestamps();
    }
}
