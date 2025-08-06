<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStatus extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'daily_summary_id',
        'status',
        'notes',
    ];

    public function summary()
    {
        return $this->belongsTo(XmppDailyPresenceSummary::class, 'daily_summary_id');
    }

    
}
