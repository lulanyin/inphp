<?php
//错误码
$error_en = [
    101 => "password is incorrect",
    102 => "frequent login errors, login has disable, please wait for moment",
    103 => "use multiple accounts for the same device",
    104 => "account not found",
    105 => "permission error",
    106 => "account disable",
    201 => "account formatting error",
    202 => "password formatting error",
    203 => "account exists",
    204 => "account type not exist",
    205 => "account type not open register",
    206 => "register fail, please try later",
    403 => "unauthorized"
];

$error_zh = [
    101 => "登录密码错误",
    102 => "由于账号频繁登录错误，已禁止登录，请稍后再试",
    103 => "同一设备无法使用过多账号",
    104 => "找不到账号",
    105 => "账号无权限",
    106 => "账号已禁止登录",
    201 => "账号格式错误",
    202 => "密码格式错误",
    203 => "账号已存在",
    204 => "注册的账号类型不存在",
    205 => "此账号类型暂未开放注册",
    206 => "注册失败，请稍后再试",
    403 => "无权限"
];

$language = "zh";

return $language == "en" ? $error_en : $error_zh;