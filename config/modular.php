<?php
/**
 * Create By Hunter
 * 2020/12/1 7:05 下午
 * 路由将使用模块化，不再区分http和ws，模块里边可以允许存在两种类型的服务控制器逻辑
 * http 和 ws 服务都将使用以下的路由配置
 */
return [
    //入口
    'home'  => 'app\modules\\',
    //默认
    'default' => 'inphp',
    //列表，key 的值，请勿重复，并且，请勿与默认模块里边的版块名重复，以免发生未知错误
    'list'  => [
        //inphp 官方模块，带有
        'inphp' => "inphp",
        //第三方
        "1024"  => "module_1024"
    ],
    //域名绑定列表
    'domains' => [

    ],
    //模块中间键
    'middlewares' => [
        //官方模块
        'inphp' => [
            //

        ]
    ],
    //URL缓存
    'url_cache' => false
];