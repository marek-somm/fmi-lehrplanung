<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model {
    use HasFactory;

    protected $fillable = [
        'modulecode',
        'aktiv_from',
        'aktiv_to',
        'ects',
        'presence_time',
        'workload',
        'pnr',
        'rotation',
        'title_de',
        'title_en',
        'composition',
        'prior_knowledge',
        'type',
        'content',
        'requirement_creditpoints',
        'requirement_exam',
        'requirement_admission',
        'additional_info',
        'literature',
    ];

    public function events() {
        return $this->belongsToMany(Event::class)->withPivot('pnr', 'description', 'title', 'changed')->as('exams')->withTimestamps();
    }

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
