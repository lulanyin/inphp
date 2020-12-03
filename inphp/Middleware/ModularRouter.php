<?php
// +----------------------------------------------------------------------
// | INPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2020 https://inphp.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://opensource.org/licenses/MIT )
// +----------------------------------------------------------------------
// | Author: lulanyin <me@lanyin.lu>
// +----------------------------------------------------------------------
namespace Inphp\Middleware;

use Inphp\Modular;
use Inphp\Service\Middleware\IRouterMiddleware;

/**
 * 模块化路由中间键
 * 与框架本身不同的是，代码可模块化，路由基于现有的，会加一层模块
 * Class ModularRouter
 * @package Inphp\Middleware
 */
class ModularRouter implements IRouterMiddleware
{

    public function process(string $host, string $uri = '', string $method = null, $group = 'http')
    {
        // TODO: Implement process() method.
        return Modular::process($host, $uri, $method, $group);
    }
}