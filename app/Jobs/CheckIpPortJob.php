<?php

namespace App\Jobs;

use App\Services\Scanner;
use App\Actions\CheckIpsPorts;

class CheckIpPortJob extends Job
{
    public function __construct(public int $job_id, public array $payload)
    {
    }

    public function handle()
    {
        $results = CheckIpsPorts::run(
            $this->payload['ips'],
            $this->payload['ports'],
            $this->payload['timeout'] ?? 5,
            $this->payload['length'] ?? 3000,
        );

        (new Scanner())->successJob([
            $this->job_id => $results
        ]);
    }
}
