<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Azubi extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $guard = 'azubi';
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'profile_picture',
        'status',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'password' => 'hashed',
    ];
}
