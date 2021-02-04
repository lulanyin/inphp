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
function httpMessage($error = 0, $message = 'success', $data = null){
    return ajaxMessage($error, $message, $data);
}
function websocketMessage($event, $error = 0, $message = 'success', $data = null){
    $json = json($error, $message, $data);
    $json['event'] = $event;
    return new \Inphp\Service\Object\Message($json);
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
 * 文件地址
 * @param string|null $val
 * @param string|null $default
 * @return string|null
 */
function attachment(string $val = null, string $default = null){
    $val = !empty($val) ? $val : $default;
    $attachment_url = Config::get("domain.attachment_url");
    $val = !empty($val) ? (strripos($val, "http")===0 ? $val : ($attachment_url.(substr($val,0,1)=="/" ? $val : ("/".$val)))) : null;
    return $val;
}
//添加标签
SmartyTags::add("attachment", function($params = []){
    $url = $params['url'] ?? ($params['default'] ?? null);
    return attachment($url);
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
 * 重定向
 * @param $url
 * @param $code
 */
function redirect($url, $code = 302){
    $response = Context::getResponse();
    $response->redirect($url, $code);
}
/**
 * 转化地址
 * @param string $url
 * @param string $moduleName
 * @return string
 */
function url(string $url, string $moduleName = null){
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
        $response->withJson([
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

/**
 * @param $code
 * @return string
 */
function errorCode($code){
    $error = Config::get("public.error");
    $error = is_array($error) ? $error : [];
    return $error[$code] ?? "未知";
}

/**
 * 转换上传文件的地址
 * @param $images
 * @return array|false|mixed|string|null
 */
function progressUploadImageUrls($images){
    if(empty($images)){
        return null;
    }
    $images = is_array($images) ? $images : explode(",", $images);
    $attachment_url_prefix = Config::get("domain.attachment_url");
    $attachment_dir = Config::get("define.attachment");
    $new_urls = [];
    foreach($images as $image){
        if(stripos($image, $attachment_url_prefix) === 0){
            $url = substr($image, strlen($attachment_url_prefix));
        }else{
            $url = $image;
        }
        if((stripos($url, "http") !== 0 && is_file($attachment_dir.$url)) || stripos($url, "http") === 0){
            $new_urls[] = $url;
        }
    }
    return count($new_urls) > 1 ? $new_urls : (count($new_urls) == 1 ? $new_urls[0] : null);
}
function parseUploadFilesUrl($files){
    return progressUploadImageUrls($files);
}