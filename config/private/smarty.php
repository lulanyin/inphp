<?php
/**
 * Smarty 模板引擎配置文件
 */
return [
    //左边界解析字符， 字符后带空格无效
    "left_delimiter"    => "{",
    //右边界字符
    "right_delimiter"   => "}",
    //缓存文件位置
    "cache_dir"         => RUNTIME."/smarty/cache",
    //编译文件
    "compile_dir"       => RUNTIME."/smarty/compile",
    //是否缓存
    "caching"           => false,
    //缓存有效时间
    "cache_lifetime"    => 3600
];