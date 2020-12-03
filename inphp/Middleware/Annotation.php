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

use Inphp\Service\Middleware\IServerOnResponseMiddleware;

/**
 * 处理控制器的注解
 * Class Annotation
 * @package Inphp\Middleware
 */
class Annotation implements IServerOnResponseMiddleware{

    public function process($response, $controller = null, $method = null)
    {
        // TODO: Implement process() method.
        if(!is_null($controller)){
            \Inphp\Annotation\Annotation::process($controller, $method);
        }
    }
}