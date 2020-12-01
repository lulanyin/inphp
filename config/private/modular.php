<?php
/**
 * Create By Hunter
 * 2020/12/1 7:05 下午
 * 路由将使用模块化
 */
return [
    //HTTP
    'http'  => [
        //入口
        'home'  => 'app\modules\\',
        //默认
        'default' => 'inphp',
        //列表
        'list'  => [
            //inphp 官方模块，带有
            'inphp' => [
                //文件夹
                'path'   => 'inphp',
                //指定配置文件，
                'config' => ''
            ]

        ]
    ],
    'ws'    => [

    ]
];