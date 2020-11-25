<?php
/**
 * 赋值变量到模板使用
 * @param string $name
 * @param $value
 */
function assign(string $name, $value){
    $data = \Inphp\Service\Http\Container::get("smarty_assign_data");
    $data = $data ?? [];
    $data[$name] = $value;
    \Inphp\Service\Http\Container::set("smarty_assign_data", $data);
}

/**
 * 获取全部的模板变量
 * @return mixed|null
 */
function getAssignData(){
    return \Inphp\Service\Http\Container::get("smarty_assign_data");
}

/**
 * 响应JSON
 * @param int $error
 * @param string $message
 * @param null $data
 */
function response($error = 0, $message = 'success', $data = null){
    \Inphp\Service\Http\Container::getResponse()->withJson([
        "error" => $error,
        "message" => $message,
        "data"  => $data
    ])->send();
}