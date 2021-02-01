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
 * @return array
 */
function response($error = 0, $message = 'success', $data = null){
    $json = json($error, $message, $data);
    Context::getResponse()->withJson($json)->send();
    return $json;
}

/**
 * 返回一个message对象
 * @param int $error
 * @param string $message
 * @param null $data
 * @return \Inphp\Service\Object\Message
 */
function ajaxMessage($error = 0, $message = 'success', $data = null){
    return new \Inphp\Service\Object\Message(json($error, $message, $data));
}

/**
 * 返回一组数据，用于JSON响应
 * @param int $error
 * @param string $message
 * @param null $data
 * @return array
 */
function json($error = 0, $message = 'success', $data = null){
    $data = is_array($error) || is_object($error) ? $error : (is_array($message) || is_object($message) ? $message : $data);
    $message = is_string($error) ? $error : (is_array($message) || is_object($message) ? null : $message);
    $error = is_array($error) || is_object($error) ? 0 : (is_string($error) ? 1 : ($error==1 || $error==0 ? $error : $error));
    $json = [
        "error" => $error,
        "message" => $message
    ];
    if(!empty($data)){
        $json['data'] = $data;
    }
    return $json;
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

/**
 * 转化地址
 * @param $url
 * @param $moduleName
 * @return string
 */
function url($url, $moduleName){
    return \Inphp\Modular::parseUrl($url, $moduleName);
}
//添加标签
SmartyTags::add("url", function($params = []){
    return url($params['url'], $params['module'] ?? null);
});

/**
 * 获取POST数据
 * @param $name
 * @param $default
 * @param null $message
 * @return mixed|null
 */
function POST($name, $default = null, $message = null){
    $value = \Inphp\Service\Http\Request::post($name, $default);
    if(empty($value) && !is_null($message)){
        $response = Context::getResponse();
        return $response->withJson([
            "error" => 1,
            "message" => $message
        ])->send();
    }
    return $value;
}

/**
 * 获取GET数据
 * @param $name
 * @param $default
 * @param null $message
 * @return mixed|null
 */
function GET($name, $default = null, $message = null){
    $value = \Inphp\Service\Http\Request::get($name, $default);
    if(empty($value) && !is_null($message)){
        $response = Context::getResponse();
        $response->withJson([
            "error" => 1,
            "message" => $message
        ])->send();
    }
    return $value;
}
/**
 * datetime 转 int
 * @param $datetime
 * @return false|int
 */
function datetime2time(string $datetime) : int{
    return strtotime($datetime);
}

/**
 * int 转 datetime
 * @param $time
 * @return false|string
 */
function time2datetime(int $time) : string{
    return date("Y-m-d H:i:s", $time);
}

/**
 * 获取客户端IP
 * @return mixed|string
 */
function getIP(){
    $client = Context::getClient();
    return $client->ip;
}