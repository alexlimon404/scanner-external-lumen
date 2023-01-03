<?php

namespace App\Actions;

use CurlHandle;
use CurlMultiHandle;

class CheckIpsPorts extends Action
{
    private array $result = [];

    public function __construct(private array $ips, private array $ports, private $timeout = 5)
    {
    }

    public function handle(): array
    {
        foreach ($this->ports as $port) {
            $this->eachPort($port);
        }

        return $this->result;
    }

    public function eachPort(int $port)
    {
        $mc = curl_multi_init();

        foreach ($this->ips as $ip) {
            $ip_port = "{$ip}:{$port}";

            $url = "http://{$ip_port}";

            $c[$ip_port] = $this->create_curl($url, $ip_port);

            curl_multi_add_handle($mc, $c[$ip_port]);
        }

        $this->process($mc);
    }

    private function process(CurlMultiHandle $mc)
    {
        while (($execrun = curl_multi_exec($mc, $running)) === CURLM_CALL_MULTI_PERFORM) ;

        while ($running && $execrun === CURLM_OK) {
            // https://bugs.php.net/bug.php?id=63411
            // curl_multi_select($mc) !== -1 с багом
            if ($running) {
                do {
                    $execrun = curl_multi_exec($mc, $running);
                    // если поток завершился
                    if ($info = curl_multi_info_read($mc) and $info['msg'] === CURLMSG_DONE) {
                        $ch = $info['handle'];
                        //ключ
                        $key = curl_getinfo($ch, CURLINFO_PRIVATE);
                        // смотрим http код который он вернул
                        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        $data = curl_multi_getcontent($ch);
                        curl_multi_remove_handle($mc, $ch);
                        curl_close($ch);

                        if ($status !== 0) {
                            $this->create($key, $status, $data);
                        }
                    }
                } while ($execrun === CURLM_CALL_MULTI_PERFORM);
            }
            usleep(100);
        }
        curl_multi_close($mc);
    }

    private function create_curl($url, $identifier, $proxy = null, $proxy_type = null): CurlHandle
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

        curl_setopt($ch, CURLOPT_PRIVATE, $identifier);
        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  // If url has redirects then go to the final redirected URL.
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5); // If expected to call with specific PROXY type
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        if (!is_null($proxy_type)) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type);
        }

        return $ch;
    }

    private function create($key, $status, $data): void
    {
        [$ip, $port] = explode(':', $key);

        $data = mb_convert_encoding(mb_substr($data, 0, 3000), 'UTF-8', 'UTF-8');

        $this->result[] = compact('ip', 'port', 'status', 'data');
    }
}
