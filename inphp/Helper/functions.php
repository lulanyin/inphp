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