<?php
namespace Inphp;

use Inphp\Annotation\Annotation;

class App
{
    /**
     * 应用初始化
     * @param bool $swoole
     * @return Service\Http\Service
     */
    public static function init(bool $swoole = false){
        //定义服务类型常量
        !defined("SWOOLE") && define("SWOOLE", "swoole");
        !defined("FPM") && define("FPM", "php-fpm");
        //定义服务类型
        !defined("SERVICE_PROVIDER") && define("SERVICE_PROVIDER", $swoole ? SWOOLE : FPM);
        //实现注解
        Annotation::start();
        return new Service\Http\Service();
    }


    /**
     *
     */
    public static function cmd(){


    }
}