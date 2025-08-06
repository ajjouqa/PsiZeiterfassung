<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusChangeRequest extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'requester_id',
        'requester_type',
        'status',
        'date',
        'requested_status',
        'reason',
        'admin_note',
        'admin_id',
        'summarie_id',
    ];

    public function mitarbeiter()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'requester_id');
    }
    
    public function azubi()
    {
        return $this->belongsTo(Azubi::class, 'requester_id');
    }

    
    public function getRequesterAttribute()
    {
        switch ($this->requester_type) {
            case 'mitarbeiter':
                return $this->mitarbeiter;
            case 'admin':
                return $this->admin;
            case 'azubi':
                return $this->azubi;
            default:
                return null;
        }
    }

}
