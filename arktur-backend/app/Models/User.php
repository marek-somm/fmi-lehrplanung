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

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uid',
        'email',
        'forename',
        'surname',
        'salutaion',
        'displayname'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public function organizations() {
        return $this->belongsToMany(Organization::class)->withTimestamps();
    }

    public function affiliations() {
        return $this->belongsToMany(Affiliation::class)->withTimestamps();
    }

    public function events() {
        return $this->belongsToMany(Event::class)->withTimestamps();
    }

    public function modules() {
        return $this->belongsToMany(Module::class)->withTimestamps();
    }
}
