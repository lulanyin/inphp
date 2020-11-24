<?php
/**
 * 此文件为最重要的文件，系统环境就靠这文件配置
 */
define('MAGIC_QUOTES_GPC', ini_set("magic_quotes_runtime",0) ? true : false);
//时区
date_default_timezone_set("PRC");
//文件夹分隔符
!defined("DS") && define("DS", DIRECTORY_SEPARATOR);
//根目录
define("ROOT",              dirname(__DIR__));
define("BASE_PATH",         ROOT);
//核心方法、类等文件存放的文件夹名
define("RESOURCE",          ROOT."/resources");
//runtime目录
define("RUNTIME",           ROOT."/runtime");
//cache 目录
define("CACHE",             RUNTIME."/cache");
define("CACHE_PATH",        CACHE);
//app根目录
define("APP_PATH",               ROOT."/app");
//添加支持自定义Smarty标签
define('SMARTY_TAGS_PARSER', APP_PATH."/smarty_tags_parser");
//站点配置
define("SERVICE_CONFIG", ROOT."/config/service.php");
//数据库配置文件
define("DB_CONFIG", ROOT."/config/private/db.php");
//常量数组
$define = [
    "root"          => ROOT,
    "app"           => "{root}/app",
    "resources"     => "{root}/resources",
    "runtime"       => "{root}/runtime",
    "configs"       => "{root}/config",
    "views"         => "{resources}/views",
    "cache"         => "{runtime}/cache",
    "logs"          => "{runtime}/logs",
    "public"        => "{root}/public",
    "attachment"    => "{public}/attachment"
];
\Inphp\Config::set("define", \Inphp\Util\Arr::parseValue($define));