<?php

namespace App\Actions\ExternalJob;

use App\Actions\Action;
use App\Services\Scanner;
use App\Jobs\CheckIpPortJob;

class GetExternalJob extends Action
{
    public function __construct()
    {
        //
    }

    public function handle()
    {
        $jobs = (new Scanner())->getJobs();

        foreach ($jobs['data'] as $job) {

            $payload = json_decode($job['payload'], true)['data'];

            dispatch(new CheckIpPortJob($job['id'], $payload['ips'], $payload['ports']));
        }
    }
}
