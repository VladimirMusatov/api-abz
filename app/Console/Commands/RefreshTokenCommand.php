<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\TokenRefreshed;

class RefreshTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-token-command';

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
        
        event(new TokenRefreshed());

    }
}
