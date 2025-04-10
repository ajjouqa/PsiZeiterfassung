<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XmppDailyPresenceSummary extends Model
{
    protected $table = 'xmpp_daily_presence_summaries';
    public $timestamps = false; // Disable default timestamps
    protected $fillable = [
        'user_type',
        'user_id',
        'xmpp_username',
        'date',
        'total_seconds',
        'formatted_time',
        'session_count',
        'first_login',
        'last_logout'
    ];

    protected $casts = [
        'date' => 'date',
        'first_login' => 'datetime',
        'last_logout' => 'datetime'
    ];

    /**
     * Get the user that owns the presence summary
     */
    public function user()
    {
        // This relationship will need to be adjusted based on your actual user models
        if ($this->user_type === 'App\Models\User') {
            return $this->belongsTo(User::class, 'user_id');
        } elseif ($this->user_type === 'App\Models\Admin') {
            return $this->belongsTo(Admin::class, 'user_id');
        } elseif ($this->user_type === 'App\Models\Azubi') {
            return $this->belongsTo(Azubi::class, 'user_id');
        }

        // Add other user type relationships as needed
        return null;
    }

    public function status()
    {
        return $this->hasOne(DailyStatus::class, 'daily_summary_id');
    }
}