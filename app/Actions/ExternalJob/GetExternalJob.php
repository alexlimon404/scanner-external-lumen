<?php

namespace App\Actions\ExternalJob;

use App\Actions\Action;
use App\Services\Scanner;
use App\Actions\CheckIpPorts;

class GetExternalJob extends Action
{
    private array $success_jobs;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $jobs = (new Scanner())->getJobs();

        foreach ($jobs['data'] as $job) {

            $payload = json_decode($job['payload'], true)['data'];

            $result = CheckIpPorts::run($job['task_id'], $payload['ips'], $payload['port']);

            $this->success_jobs[$job['id']] = $result;
        }

        return (new Scanner())->successJob($this->success_jobs);
    }
}
