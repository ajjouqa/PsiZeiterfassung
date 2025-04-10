<?php

namespace App\Console\Commands;

use App\Models\DailyStatus;
use Illuminate\Console\Command;
use App\Models\XmppUserMapping;
use App\Models\XmppDailyPresenceSummary;
use Carbon\Carbon;

class GenerateDailyPresenceSummaries extends Command
{
    protected $signature = 'presence:generate-daily';
    protected $description = 'Generates daily presence summary rows for all users';

    public function handle()
    {
        $date = now()->toDateString();
    
        $users = XmppUserMapping::all();
    
        foreach ($users as $mapping) {
            $exists = XmppDailyPresenceSummary::where('user_id', $mapping->user_id)
                ->where('user_type', $mapping->user_type)
                ->where('date', $date)
                ->exists();
    
            if (! $exists) {
                $xmpp_daily = XmppDailyPresenceSummary::create([
                    'user_type'      => $mapping->user_type,
                    'user_id'        => $mapping->user_id,
                    'xmpp_username'  => $mapping->xmpp_username,
                    'date'           => $date,
                    'total_seconds'  => 0,
                    'formatted_time' => '00:00:00',
                    'session_count'  => 0,
                    'first_login'    => null,
                    'last_logout'    => null,
                ]);

                DailyStatus::create([
                    'daily_summary_id' => $xmpp_daily->id,
                    'status'           => 'working',
                    'notes'            => 'No presence data available for today.',
                ]);
            }
        }
    
        $this->info('Daily presence summaries generated.');
    }
}
