<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 20:55
 */

namespace App\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Config;

class UDP extends AbstractProcess
{
    protected $hosts = [];
    protected $checkInternal = 300000;
    protected $command = "\x44\x53\x2d\x4f\x4d\x50\x00\x01\x00\x00\x01\x00\x03";

    public function run($arg)
    {
        $this->init();
      var_dump($this->hosts);
      var_dump('udp');
        foreach ($this->hosts as $host) {
            $cli = new \Swoole\Client(SWOOLE_UDP);
            $cli->sendto($host, 17001, $this->command);
            $cli->close();
            unset($cli);
        }

        $this->addTick($this->checkInternal, function () {
            foreach ($this->hosts as $host) {
                $cli = new \Swoole\Client(SWOOLE_UDP);
                $cli->sendto($host, 17001, $this->command);
                $cli->close();
                unset($cli);
            }
        });
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
                return $host['udp'];
            }, array_filter($hosts, function ($host) {
                return (isset($host['udp']) && $host['udp']);
            }));
        } else {
            $hosts = [];
        }
        $this->hosts = $hosts;
    }

    protected function loadCheckInternal()
    {
        $internal = Config::getInstance()->getConf('CHECK_INTERNAL');
        if(is_numeric($internal) && $internal > 1) {
            $internal *= 1000;
        } else {
            $internal = 300 * 1000;
        }

        $this->checkInternal = $internal;
    }

    protected function init()
    {
        $this->loadHosts();
        $this->loadCheckInternal();
    }
}