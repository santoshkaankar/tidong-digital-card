<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:run-missing-migrations')]
#[Description('Command description')]
class RunMissingMigrations extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
