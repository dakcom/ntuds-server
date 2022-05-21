<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 21:45
 */

namespace App\Pool\Redis;

use EasySwoole\Component\Pool\PoolObjectInterface;

class Redis extends \Redis implements PoolObjectInterface
{
    function gc()
    {
        $this->close();
    }

    function objectRestore()
    {

    }

    function beforeUse(): bool
    {
        return true;
    }
}
