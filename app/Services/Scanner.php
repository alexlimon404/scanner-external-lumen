<?php

namespace App\Services;

use App\Version;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class Scanner
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge(
            config('scanner', []),
            $config
        );
    }

    public function config(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    public function client(): PendingRequest
    {
        return Http::timeout(15)->withHeaders([
            'accept' => 'application/json',
            'unique' => $this->config('unique_id'),
            'token' => $this->config('auth_token'),
            'app-version' => Version::$version,
            'app-type' => 'lumen',
        ])->baseUrl("{$this->config('api_url')}/api");
    }

    public function getJobs()
    {
        $response = $this->client()->get('external-jobs', [
            'limit' => $this->config('limit'),
        ]);

        $response->throw();
        return $response->json();
    }

    public function successJob(array $data)
    {
        $response = $this->client()->post('external-jobs', [
            'data' => $data,
        ]);

        $response->throw();
        return $response->json();
    }
}
