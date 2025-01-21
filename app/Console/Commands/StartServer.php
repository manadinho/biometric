<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartServer extends Command
{
    protected $signature = 'nativephp:start-server';

    protected $description = 'Start the NativePHP server on port 80';

    public function handle()
    {
        $host = '0.0.0.0';
        $port = 2000;

        // Run PHP's built-in server on port 80
        $this->info("Starting server on http://$host:$port");
        exec("php -S $host:$port -t public");
    }
}
