<?php
namespace Inphp;

use Inphp\Annotation\Annotation;
use Inphp\Service\Service;

class App
{
    /**
     * @var \Swoole\Http\Server|\Swoole\WebSocket\Server|null;
     */
    public static $server = null;

    /**
     * 应用初始化
     * @param bool $swoole
     * @return Service
     */
    public static function init(bool $swoole = false, $ws = false){
        //定义服务类型常量
        !defined("SWOOLE") && define("SWOOLE", "swoole");
        !defined("FPM") && define("FPM", "php-fpm");
        //定义服务类型
        !defined("SERVICE_PROVIDER") && define("SERVICE_PROVIDER", $swoole ? SWOOLE : FPM);
        //ws
        $ws = $swoole && $ws;
        //swoole 数据库连接池
        if($swoole){
            $pools = Config::get("private.db.swoole.pools");
            $pools = is_numeric($pools) ? $pools : 5;
            $pools = $pools >=5 && $pools <= 20 ? ceil($pools) : 5;
            define("INPHP_DB_SWOOLE_POOLS", $pools);
        }
        //实现注解
        Annotation::start();
        //返回服务对象
        $service = new Service($swoole, $ws);
        self::$server = $service->server;
        return $service;
    }
}