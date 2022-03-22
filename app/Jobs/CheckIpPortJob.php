<?php

namespace App\Jobs;

use App\Services\Scanner;
use App\Actions\CheckIpsPorts;

class CheckIpPortJob extends Job
{
    public int $job_id;
    public array $ips;
    public array $ports;

    public function __construct(int $job_id, array $ips, array $ports)
    {
        $this->job_id = $job_id;
        $this->ips = $ips;
        $this->ports = $ports;
    }

    public function handle()
    {
        $results = CheckIpsPorts::run($this->ips, $this->ports);

        (new Scanner())->successJob([
            $this->job_id => $results
        ]);
    }
}
