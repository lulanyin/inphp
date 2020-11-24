<?php

function assign(string $name, $value){
    $data = \Inphp\Service\Http\Container::get("smarty_assign_data");
    $data = $data ?? [];
    $data[$name] = $value;
    \Inphp\Service\Http\Container::set("smarty_assign_data", $data);
}

function getAssignData(){
    return \Inphp\Service\Http\Container::get("smarty_assign_data");
}