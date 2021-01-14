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
use Inphp\Config;
use Inphp\Service\Context;
/**
 * 赋值变量到模板使用
 * @param string $name
 * @param $value
 */
function assign(string $name, $value){
    $data = Context::get("smarty_assign_data");
    $data = $data ?? [];
    $data[$name] = $value;
    Context::set("smarty_assign_data", $data);
}

/**
 * 获取全部的模板变量
 * @return mixed|null
 */
function getAssignData(){
    return Context::get("smarty_assign_data");
}

/**
 * 响应JSON
 * @param int $error
 * @param string $message
 * @param null $data
 */
function response($error = 0, $message = 'success', $data = null){
    Context::getResponse()->withJson(json($error, $message, $data))->send();
}

/**
 * 返回一组数据，用于JSON响应
 * @param int $error
 * @param string $message
 * @param null $data
 * @return array
 */
function json($error = 0, $message = 'success', $data = null){
    return [
        "error" => $error,
        "message" => $message,
        "data"  => $data
    ];
}

/**
 * 生成资源文件地址
 * @param string $url
 * @param null $module
 * @return string
 */
function assets(string $url, $module = null){
    $assets_url = Config::get("domain.assets_url");
    $assets_url = strrchr($assets_url, "/") == "/" ? $assets_url : "{$assets_url}/";
    if(!empty($module)){
        $assets_url .= "{$module}/";
    }
    return $assets_url . str_replace("//", "/", $url);
}
//添加标签
SmartyTags::add("assets", function($params = []){
    return assets($params['url'], $params['module'] ?? null);
});

/**
 * 设置临时的全局变量
 * @param string $name
 * @param null $value
 * @return mixed|null
 */
function globals(string $name, $value = null){
    if(!is_null($value)){
        if(Context::isSwoole()){
            \Swoole\Coroutine::getContext()[$name] = $value;
        }else{
            $GLOBALS[$name] = $value;
        }
    }else{
        if(Context::isSwoole()){
            $value = \Swoole\Coroutine::getContext()[$name] ?? null;
        }else{
            $value = $GLOBALS[$name] ?? null;
        }
    }
    return $value;
}