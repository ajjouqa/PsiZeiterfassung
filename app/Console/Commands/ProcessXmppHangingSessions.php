<?php

    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use App\Services\XmppAuthService;

    class ProcessXmppHangingSessions extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'xmpp:process-hanging-sessions {--user_type= : Process only specific user type} {--user_id= : Process only specific user ID}';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Process hanging XMPP sessions and update daily presence summaries';

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Execute the console command.
         *
         * @param XmppAuthService $xmppAuthService
         * @return int
         */
        public function handle(XmppAuthService $xmppAuthService)
        {
            $userType = $this->option('user_type');
            $userId = $this->option('user_id');
            
            $this->info('Processing hanging XMPP sessions...');
            
            $processed = $xmppAuthService->processHangingSessions($userType, $userId);
            
            $this->info("Processed {$processed} hanging session(s)");
            
            
            return 0;
        }
    }