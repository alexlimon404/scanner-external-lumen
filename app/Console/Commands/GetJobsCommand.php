<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\ExternalJob\GetExternalJob;

class GetJobsCommand extends Command
{
    protected $signature = 'get:jobs';

    public function handle()
    {
        GetExternalJob::run();
    }
}
