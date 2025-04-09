<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XmppPresenceLog extends Model
{
    protected $table = 'xmpp_presence_logs';
    
    protected $fillable = [
        'user_type',
        'user_id',
        'xmpp_username',
        'event_type',     // login, logout, status_change
        'presence',       // available, away, chat, dnd, xa, unavailable
        'status',         // Custom status message
        'timestamp',
        'resource',
        'ip_address'
    ];
    
    protected $casts = [
        'timestamp' => 'datetime',
    ];
    
    // Disable default timestamps
    public $timestamps = false;
    
    /**
     * Get the user that owns the presence log.
     */
    public function user()
    {
        return $this->morphTo(__FUNCTION__, 'user_type', 'user_id');
    }
    
    /**
     * Get the user mapping record.
     */
    public function xmppMapping()
    {
        return $this->belongsTo(XmppUserMapping::class, 'xmpp_username', 'xmpp_username');
    }

}