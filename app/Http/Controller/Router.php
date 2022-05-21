<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 23:03
 */

namespace App\Http\Controller;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->setGlobalMode(true);
        $this->setMethodNotAllowCallBack(function (Request $request, Response $response) {
            $response->write('405');
            return false;
        });
        $this->setRouterNotFoundCallBack(function (Request $request, Response $response) {
            $response->write('404');
            return false;
        });
        $routeCollector->get('/', '/Index');
    }
}