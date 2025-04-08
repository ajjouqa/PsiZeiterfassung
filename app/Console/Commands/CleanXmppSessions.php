<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XmppAuthService;
use Illuminate\Support\Facades\Log;

class CleanXmppSessions extends Command
{
    protected $signature = 'xmpp:cleanup-sessions';
    protected $description = 'Clean up hanging XMPP sessions';

    protected $xmppAuthService;

    public function __construct(XmppAuthService $xmppAuthService)
    {
        parent::__construct();
        $this->xmppAuthService = $xmppAuthService;
    }

    public function handle()
    {
        $this->info('Cleaning up hanging XMPP sessions...');
        
        $processed = $this->xmppAuthService->processHangingSessions();
        
        $this->info("Processed {$processed} hanging sessions.");
        Log::info("XMPP session cleanup: processed {$processed} hanging sessions");
        
        return 0;
    }
}