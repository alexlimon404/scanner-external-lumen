<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use App\Actions\ExternalJob\GetExternalJob;

class GetJobsCommand extends Command
{
    protected $signature = 'GetJobsCommand';

    public function handle()
    {
        if (Queue::size() < 10) {
            GetExternalJob::run();
        };
    }
}
