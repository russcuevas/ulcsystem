<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Collector extends Authenticatable
{
    use HasFactory;

    protected $table = 'collectors';

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'phone',
        'gender',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
