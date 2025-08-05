<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $guard = 'admin';

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


    public function xmppUserMapping()
    {
        return $this->hasOne(XmppUserMapping::class, 'user_id', 'id')->where('user_type', 'admin');
    }

    public function notifications()
    {
        return $this->hasMany(StatusChangeRequest::class, 'id');
    }
    public function statusChangeRequests()
    {
        return $this->hasMany(StatusChangeRequest::class, 'requester_id', 'id')->where('requester_type', 'admin');
    }
}
