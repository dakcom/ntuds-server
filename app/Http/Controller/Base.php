<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 23:12
 */

namespace App\Http\Controller;


use EasySwoole\Http\AbstractInterface\Controller;

abstract class Base extends Controller
{
    protected function toJson($data)
    {
        $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
        return;
    }
}