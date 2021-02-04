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

use Inphp\DB\Cache;
use Inphp\Object\ModuleObject;
use Inphp\Service\Context;
use Inphp\Service\Object\Status;
use Inphp\Service\Router;
use Inphp\Service\Service;
use Inphp\Util\Arr;

/**
 * 模块化处理
 * Class Modular
 * @package Inphp
 */
class Modular
{
    /**
     *
     * 模块化配置
     * @var array
     */
    public static $config = [];

    /**
     * 模块化缓存
     * @var array
     */
    public static $modules_cache = [];

    /**
     * 获取模块配置
     * @param $name
     * @return ModuleObject|null
     */
    public static function get($name){
        $config = self::$modules_cache[$name] ?? null;
        if(is_null($config)){
            $home = self::getConfig("home");
            $dir = str_replace("\\", DS, $home.$name);
            $file = ROOT."/".$dir."/config.json";
            if(is_file($file)){
                $content = file_get_contents($file);
                $config = json_decode($content, true);
                if(null !== $config){
                    $config = new ModuleObject($config);
                    self::$modules_cache[$name] = $config;
                }
            }
        }
        return $config;
    }

    /**
     * 模块化处理，如果已有路由缓存，不会执行
     * @param string $host                      域名
     * @param string $uri                       路径
     * @param string|null $request_method       请求类型  GET,POST
     * @param string $group                     服务类型
     * @return Status
     */
    public static function process(string $host, string $uri = '', string $request_method = null, $group = 'http'){
        //模块化配置
        $modular_router = self::getConfig();
        //模块化路由入口（存放模块的文件夹位置）
        $home = Arr::get($modular_router, "home", "app\modules\\");
        //必须是在app文件夹下
        if(stripos($home, "app") !== 0){
            $home = "app\{$home}";
        }
        //根据 host 识别模块
        $domains = Arr::get($modular_router, "domains", []);
        if(!empty($domains) && ($module = array_search($host, $domains))){
            //从列表中匹配，找到入口
            $pathArray = !empty($uri) ? explode("/", $uri) : ["index", "index"];
            //记录长度，以便于后续的智能判断使用
            $pathLen = count($pathArray) + 1;
            //至少长度为2
            if($pathLen < 3){
                $pathArray = array_pad($pathArray, 2, "index");
            }
            //将module名称组合到第一截
            $pathArray = array_merge([$module], $pathArray);
        }else{
            //常规的入口
            $listPath = array_keys($modular_router['list']);
            //拆分请求路径
            $pathArray = !empty($uri) ? explode("/", $uri) : [
                $modular_router['default'], "index", "index"
            ];
            //判断第一截是否在入口列表中
            if(!in_array($pathArray[0], $listPath)){
                //不在，则添加到默认入口
                $pathArray = array_merge([$modular_router['default']], $pathArray);
            }
            //替换为实际的命名空间名称
            $pathArray[0] = $modular_router["list"][$pathArray[0]];
            //记录长度，以便后续的智能匹配使用
            $pathLen = count($pathArray);
            //如果长度少于3
            if($pathLen < 3){
                $pathArray = array_pad($pathArray, 3, "index");
            }
            //记录模块名
            $module = $pathArray[0];
        }
        //模块化入口
        //获取模块化配置
        $moduleObject = self::get($module);
        if(is_null($moduleObject)){
            return new Status([
                "status"    => 404,
                "message"   => 'undefined module '.$module,
                "state"     => 'modular'
            ]);
        }else{
            //将模块保存到上下文，后续可以使用
            App::setModule($moduleObject);
            //使用模块继续处理...
            //获取入口
            $module_router_home = $moduleObject->getHome($group);
            //截掉第一个，第一个是模块名称
            $pathArray = array_slice($pathArray, 1);
            //当前请求版块
            $part = $pathArray[0];
            //模块的所有可用路由列表
            $router_list = $moduleObject->getRouterList($group);
            //默认版块，
            $default_router = $moduleObject->getDefaultRouter($group);
            //填的是 "/" 或者 ""，会自动处理成 "/"
            if($default_router == "/" || empty($router_list)){
                //则说明直接默认使用入口，不区分版块
                $home_val = $moduleObject->getHomeValue($group);
                $pathArray = array_merge([$home_val], $pathArray);
                $module_router_home = $moduleObject->namespace;
            }else{
                //由于多了一层模块，需要减1
                $pathLen -= 1;
                //区分版块
                if(isset($router_list[$part])){
                    $pathArray[0] = $router_list[$part];
                }else{
                    //添加默认
                    $part = $router_list[$default_router];
                    $pathArray = array_merge([$router_list[$default_router]], $pathArray);
                }
                //长度少于3
                if(count($pathArray) < 3){
                    $pathArray = array_pad($pathArray, 3, "index");
                }

            }
            //视图
            $view_dir = $view_suffix = '';
            if($group == Service::HTTP){
                $view_dir = $moduleObject->getViewDir();
                $view_dir = strrchr($view_dir, "/") == "/" ? $view_dir : "{$view_dir}/";
                $view_suffix = $moduleObject->getViewSuffix();
            }
            //返回匹配的数据
            return Router::match($module_router_home, $pathArray, $moduleObject->getResponseContentType($part), $view_dir, $view_suffix, $group);
        }
    }

    /**
     * 获取配置
     * @param null $key
     * @param null $default
     * @return array|bool|mixed|string|null
     */
    public static function getConfig($key = null, $default = null){
        if(empty(self::$config)){
            $file = defined('INPHP_MODULAR_CONFIG') ? INPHP_MODULAR_CONFIG : null;
            if(!is_null($file) && is_file($file)){
                self::$config = include $file;
            }
        }
        if(is_null($key)){
            return self::$config;
        }
        return Arr::get(self::$config, $key);
    }

    /**
     * 获取模块列表
     * @return array
     */
    public static function getList(){
        return self::getConfig("list");
    }

    /**
     * 获取模块配置文件数组
     * @param $name
     * @return ModuleObject|null
     */
    public static function getModuleConfig($name){
        //尝试从缓存获取
        return self::get($name);
    }

    /**
     * 将地址转换为正确的地址
     * @param $url
     * @param $moduleName
     * @return string
     */
    public static function parseUrl($url, $moduleName = null){
        //添加自动识别
        if(is_null($moduleName)){
            $self_module = App::getModule();
            if(!empty($self_module)){
                $moduleName = $self_module->id;
            }
        }
        $url_query = stripos($url, "?") !== false ? explode("?", $url, 2) : [$url, ""];
        $url = $url_query[0];
        $query = $url_query[1];
        $keyName = "url_".md5($moduleName."_".$url);
        $url_cache = self::getConfig("url_cache", false);
        $value = $url_cache ? Cache::get($keyName) : null;
        if(!empty($value)){
            return $value.(!empty($query) ? "?{$query}" : "");
        }
        if(stripos($url, "./") === 0){
            //使用当前的模块
            $str_array = explode("/", $url);
            //取path
            $path = $str_array[1] ?? null;
            //
            $url_array = count($str_array) > 2 ? array_slice($str_array, 2) : [];
        }elseif(stripos($url, "/") === 0){
            //使用外部模块
            $str_array = explode("/", $url);
            $moduleName = $str_array[1];
            $path = $str_array[2] ?? null;
            $url_array = count($str_array) > 3 ? array_slice($str_array, 3) : [];
        }else{
            return $url;
        }
        $config = self::getModuleConfig($moduleName);
        $http = $config->http_config;
        $list = $http['list'] ?? [];
        $default = $http['default'] ?? '';
        //反转
        if(!empty($list)){
            $flip = array_flip($list);
            if(isset($flip[$path])){
                //存在path，替换
                $path = $flip[$path];
            }
        }else{
            //空的
        }
        $modules = self::getList();
        //反转
        $flip = array_flip($modules);
        if(isset($flip[$moduleName])){
            $moduleName = $flip[$moduleName];
        }
        $value = "/".$moduleName.(!empty($path) ? ("/".$path) : "").(!empty($url_array) ? ("/".join("/", $url_array)) : "");
        if($url_cache){
            Cache::set($keyName, $value);
        }
        return $value.(!empty($query) ? "?{$query}" : "");
    }

    /**
     * 模块数据刷新
     */
    public static function refresh(){
        //配置清空，按需重新加载
        self::$config = [];
        //
        self::$modules_cache = [];
        Router::clear();
    }
}