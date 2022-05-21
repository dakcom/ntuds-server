<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 21:07
 */

namespace App\Process;

use App\Client\TcpClient;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Config;

class TCP extends AbstractProcess
{
    protected $hosts = [];

    public function run($arg)
    {
        $this->loadHosts();
        foreach ($this->hosts as $key => $host) {
            (new TcpClient($key, $host))->run();
        }
    }

    public function onShutDown()
    {

    }

    public function onReceive(string $str)
    {

    }

    protected function loadHosts()
    {
        $hosts = Config::getInstance()->getConf('CLASSROOM');
        if ($hosts) {
            $hosts = array_map(function ($host) {
                return $host['tcp'];
            }, array_filter($hosts, function ($host) {
                return (isset($host['tcp']) && $host['tcp']);
            }));
        } else {
            $hosts = [];
        }
        $this->hosts = $hosts;
    }
}