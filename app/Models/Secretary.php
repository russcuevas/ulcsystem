<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Secretary extends Authenticatable
{
    use HasFactory;

    protected $table = 'secretaries';

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
