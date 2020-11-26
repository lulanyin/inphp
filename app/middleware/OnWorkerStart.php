<?php
/**
 * Create By Hunter
 * 2020/11/25 12:18 下午
 *
 */
namespace app\middleware;

use Inphp\DB\Swoole\Pool;
use Inphp\Service\IWorkerStartMiddleWare;
use Swoole\Http\Server;

class OnWorkerStart implements IWorkerStartMiddleWare
{
    public function process(Server $server, int $worker_id)
    {
        // TODO: Implement process() method.
        //初始化连接池
        echo "worker[{$worker_id}] mysql pool start".PHP_EOL;
        Pool::init();
    }
}