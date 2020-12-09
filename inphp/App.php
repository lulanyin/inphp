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
     * @param bool $ws
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
     * 执行命令
     * @return ICommend
     */
    public static function cmd(){
        //
        echo "+---------------------+\r\n";
        echo "| ♪♪♪♪♪♪ INPHP ♪♪♪♪♪♪ |\r\n";
        echo "| Think you for using |\r\n";
        echo "|    commend part     |\r\n";
        echo "+---------------------+\r\n";
        $argv = $_SERVER['argv'] ?? [];
        if(count($argv)>=2){
            $prefix = "app\cmd\\";
            $commend = $prefix.str_replace(".", "\\", $argv[1]);
            if(class_exists($commend)){
                $params = count($argv)>2 ? array_slice($argv, 2) : [];
                if(!empty($params)){
                    $array = [];
                    foreach($params as $param){
                        $arr = explode("=", $param);
                        $array[$arr[0]] = $arr[1] ?? true;
                        $array[$arr[0]] = $array[$arr[0]]=="true" ? true : ($array[$arr[0]]=="false" ? false : $array[$arr[0]]);
                    }
                    $params = $array;
                }
                $cmd = new $commend($params);
                if($cmd instanceof ICommend){
                    echo date("Y/m/d H:i:s")." running ... ".PHP_EOL;
                    return $cmd;
                }else{
                    exit("not a commend".PHP_EOL);
                }
            }else{
                exit ("not commend found {$commend}".PHP_EOL);
            }
        }else{
            exit ("not commend".PHP_EOL);
        }
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