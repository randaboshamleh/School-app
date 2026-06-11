<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PurgeOldFcmTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-old-fcm-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Models\FcmToken::where('last_used_at', '<', now()->subMonths(2))->delete();
        $this->info('Expired FCM tokens purged.');
    }
}
