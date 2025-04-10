<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XmppUserMapping extends Model
{
    use HasFactory;

    protected $table = 'xmpp_user_mappings';

    protected $fillable = [
        'xmpp_username',
        'user_type',
        'user_id',
        'is_active',
        'current_presence',
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'last_login' => 'datetime',
    ];

    
    public function user()
{
    if ($this->user_type == 'admin') {
        return $this->belongsTo(Admin::class, 'user_id');
    } elseif ($this->user_type == 'azubi') {
        return $this->belongsTo(Azubi::class, 'user_id');
    } elseif ($this->user_type == 'mitarbeiter') {
        return $this->belongsTo(User::class, 'user_id');
    }
    return null;
}


    
}
