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

use Inphp\DB\Swoole\Pool;
use Inphp\Service\Middleware\IServerOnWorkerStartMiddleware;

/**
 * 连接池
 * Class InitPool
 * @package Inphp\Middleware
 */
class InitPool implements IServerOnWorkerStartMiddleware
{
    public function process($server, $worker_id)
    {
        // TODO: Implement process() method.
        echo "worker[{$worker_id}] mysql pool init".PHP_EOL;
        Pool::init();
    }
}