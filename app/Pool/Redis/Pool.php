<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 21:49
 */

namespace App\Pool\Redis;

use EasySwoole\Component\Pool\AbstractPool;
use EasySwoole\EasySwoole\Config;

class Pool extends AbstractPool
{
    protected $host;

    protected $port;

    protected $auth;

    protected $db = 0;

    protected function createObject()
    {
        $this->loadConfig();
        $redis = new Redis();
        if ($redis->connect($this->host, $this->port)) {
            if ($this->auth) {
                $redis->auth($this->auth);
            }
            if ($this->db !== 0) {
                $redis->select($this->db);
            }

            return $redis;
        }

        return null;
    }

    protected function loadConfig()
    {
        $config = Config::getInstance()->getConf('REDIS');
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->auth = $config['auth'];
        $this->port = $config['port'];
    }
}