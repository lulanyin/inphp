<?php
return [
    //是否开启调试
    "debug"         => false,
    //数据遍历方式，具体查看PDO文档
    "fetch_model"   => PDO::FETCH_ASSOC,
    //字符集类型
    "charset"   => "utf8mb4",
    //表名前缀，为了方便使用，读写分离的前缀必须统一
    "prefix"    => 'pre_',
    //超时时间
    'timeout'   => 5,
    //日志文件位置
    'log_file'  => RUNTIME."/db",
    //默认连接，若使用读写分离，可以不定义
    "default"       => [
        //服务器地址
        'host'      => '127.0.0.1',
        //端口
        'port'      => 3306,
        //数据库用户名
        'user'      => 'root',
        //密码
        'password'  => '12345678',
        //数据库名
        'database'  => 'inphp'
    ],
    //读分离，若定义了，select 会优先使用
    //"read"          => ["数组值请参考上方的 default 连接"],
    //写分离，若定义了，insert update delete 会优先使用
    //"write"         => ["数组值请参考上方的 default 连接"],
    //如果有需要 redis, 请定义 redis 连接
    "redis"         => [
        //redis服务器地址
        'host'       => '127.0.0.1',
        //端口
        'port'       => 6379,
        //密码
        'password'   => '',
        //使用第几个库
        'select'     => 0,
        //超时时间
        'timeout'    => 0,
        //过期时间
        'expire'     => 0,
        //持久化
        'persistent' => false,
        //保存的所有 key 的前缀
        'prefix'     => 'redis_pre_',
    ]
];