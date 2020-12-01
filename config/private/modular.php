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
    //列表
    'list'  => [
        //inphp 官方模块，带有
        'inphp' => "inphp"
    ]
];