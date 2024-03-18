<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model {
    use HasFactory;

    protected $fillable = [
        'code',
        'active_from',
        'active_to',
        'active',
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

    protected $casts = [
        'active_from' => 'integer',
        'active_to' => 'integer',
        'active' => 'boolean',
        'ects' => 'integer',
        'presence_time' => 'integer',
        'workload' => 'integer',
    ];

    protected $hidden = [
        'remember_token',
        'created_at',
        'updated_at',
    ];

    public function events() {
        return $this->belongsToMany(Event::class)->withPivot('pnr', 'description', 'title')->using(EventModule::class)->withTimestamps();
    }

    public function users() {
        return $this->belongsToMany(User::class)->using(ModuleUser::class)->withTimestamps();
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }
}
