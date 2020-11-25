<?php
namespace app\middleware;

use Inphp\Service\Http\Response;
use Inphp\Service\IMiddleWare;

class Annotation implements IMiddleWare
{

    public function __construct()
    {

    }

    public function process(Response $response, $controller = null, string $method = null)
    {
        // TODO: Implement process() method.
        if($controller){
            //处理控制器的注解
            \Inphp\Annotation\Annotation::process($controller, $method);
        }
    }
}