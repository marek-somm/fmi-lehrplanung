<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uid',
        'email',
        'forename',
        'surname',
        'salutaion',
        'displayname'
    ];

    protected $casts = [

    ];

    protected $hidden = [
        'remember_token',
        'created_at',
        'updated_at'
    ];

    public function organizations() {
        return $this->belongsToMany(Organization::class)->using(OrganizationUser::class)->withTimestamps();
    }

    public function affiliations() {
        return $this->belongsToMany(Affiliation::class)->using(AffiliationUser::class)->withTimestamps();
    }

    public function events() {
        return $this->belongsToMany(Event::class)->using(EventUser::class)->withTimestamps();
    }

    public function modules() {
        return $this->belongsToMany(Module::class)->using(ModuleUser::class)->withTimestamps();
    }
}
