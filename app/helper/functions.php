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