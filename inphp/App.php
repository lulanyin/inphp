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
namespace Inphp;

use Inphp\Annotation\Annotation;
use Inphp\Object\ModuleObject;
use Inphp\Service\Context;
use Inphp\Service\Service;
use Swoole\Coroutine;

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
        !defined("INPHP_SERVICE_PROVIDER") && define("INPHP_SERVICE_PROVIDER", $swoole ? SWOOLE : FPM);
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

    /**
     * 临时上下文数据
     * @var array
     */
    private static $context = [];

    /**
     * 保存数据到上下文
     * @param ModuleObject $module
     */
    public static function setModule(ModuleObject $module){
        if(Context::isSwoole()){
            Coroutine::getContext()["app_module"] = $module;
        }else{
            self::$context["app_module"] = $module;
        }
    }

    /**
     * 获取当前所用模块对象
     * @return ModuleObject
     */
    public static function getModule(){
        if(Context::isSwoole()){
            return Coroutine::getContext()["app_module"] ?? null;
        }else{
            return self::$context["app_module"] ?? null;
        }
    }
}