<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillabe = [
        'modulcode'.
        'pnr',
        'description',
        'title',
        'changed'
    ];

    public function events() {
        
    }
}
