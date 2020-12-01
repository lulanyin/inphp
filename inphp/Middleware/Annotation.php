<?php
/**
 * Create By Hunter
 * 2020/12/1 4:14 下午
 *
 */
namespace Inphp\Middleware;

use Inphp\Service\Middleware\IServerOnResponseMiddleware;

class Annotation implements IServerOnResponseMiddleware{

    public function process($response, $controller = null, $method = null)
    {
        // TODO: Implement process() method.
        if(!is_null($controller)){
            \Inphp\Annotation\Annotation::process($controller, $method);
        }
    }
}