<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 19:35
 */

namespace App\Server;

use App\Process\TCP;
use App\Process\UDP;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\SysConst;


class Server
{
    protected $register;
    protected $hosts = [];


    public function __construct(EventRegister $register)
    {
        $this->register = $register;
    }

    public static function initEnv()
    {
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE, 'App\\Http\\Controller\\');
    }

    public function init()
    {
        $this->addUdpProcess();
        $this->addTcpProcess();
    }

    protected function addUdpProcess()
    {
        $process = new UDP('udp');
        ServerManager::getInstance()->getSwooleServer()->addProcess($process->getProcess());
    }

    protected function addTcpProcess()
    {
        $process = new TCP('tcp');
        ServerManager::getInstance()->getSwooleServer()->addProcess($process->getProcess());
    }
}