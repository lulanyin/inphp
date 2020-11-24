<?php
function tag_config($params = []){
    //您设置了一个全局配置文件，大概值：
    $configs = [
        "email"    => "me@lanyin.lu",
        "url"      => "http://www.lanyin.lu"
    ];
    //参数字段
    $name = $params['name'] ?? null;
    if(!is_null($name)){
        return $configs[$name] ?? "";
    }
    return "";
}