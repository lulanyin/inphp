<?php
/**
 * Create By Hunter
 * 2020/12/1 4:18 下午
 *
 */
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