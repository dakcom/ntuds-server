<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 21:28
 */

namespace App\Client;

use App\Pool\Redis\Pool;
use App\Pool\Redis\Redis;
use Swoole\Client;

class TcpClient
{
    protected $key;

    protected $host;

    protected $header = "\x44\x53\x2d\x4f\x4d\x50\x00\x01\x07\x00";

    /**
     * @var Client
     */
    protected $client;

    public function __construct($key, $host)
    {
        $this->host = $host;
        $this->key = $key;
    }

    public function checkLength($raw)
    {
        if (strlen($raw) < 12) {
            return 0;
        }
        $length = unpack('vLength', $raw, 10)['Length'] * 4;
        return $length + 12;
    }

    public function onConnect(\swoole_client $client)
    {

    }

    public function onClose(\swoole_client $client)
    {
        $this->reConnect();
    }

    protected function reConnect()
    {
        $client = $this->client;
        $this->client = null;
        unset($client);
        sleep(1);
        $this->run();
    }

    public function run()
    {
        $this->getClient();
        $this->client->on('connect', [$this, 'onConnect']);
        $this->client->on('receive', [$this, 'onReceive']);
        $this->client->on('close', [$this, 'onClose']);
        $this->client->on('error', [$this, 'onError']);
        $this->client->connect($this->host, '16999');
    }

    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Client(SWOOLE_TCP, SWOOLE_SOCK_ASYNC);
            $this->client->set([
                'open_length_check' => 1,
                'package_max_length' => 8 * 1024,
                'package_length_func' => [$this, 'checkLength']
            ]);
        }
    }

    public function onError(\swoole_client $client)
    {
        $this->reConnect();
    }

    public function onReceive(\swoole_client $client, string $raw)
    {
        $header = substr($raw, 0, 12);
        $body = substr($raw, 12);
        if(substr($header, 0, 10) === $this->header && strlen($body) != 0) {
            Pool::invoke(function (Redis $redis) use ($body) {
                $key = 'campus:' . $this->key;
                $redis->hMSet($key, [
                    'raw' => $body,
                    'timestamp' => time(),
                ]);
            });
        }
    }
}