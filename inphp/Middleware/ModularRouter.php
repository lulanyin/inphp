<?php
/**
 * Create By Hunter
 * 2020/12/1 7:02 下午
 *
 */
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