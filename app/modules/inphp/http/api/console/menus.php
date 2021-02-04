<?php
namespace app\modules\inphp\http\api\console;

use Inphp\Modular;

/**
 * @app\modules\inphp\attributes\Auth(group="admin")
 * Class menus
 * @package app\modules\inphp\http\api\console
 */
class menus
{
    /**
     * @return array
     */
    public function index() : array{
        //框架的管理模块，提供一些基础的接口
        //首先，提供一些基础的菜单
        $menus = [];
        //获取所有的模块
        $modules = Modular::getList();
        //只取值
        $modules_values = array_values($modules);
        //从模块的http配置中，获取菜单
        foreach ($modules_values as $module){
            $module_config = Modular::getModuleConfig($module);
            if(is_null($module_config)){
                continue;
            }
            $http_config = $module_config->http_config;
            if(!empty($http_config)){
                //从中获取控制台菜单
                $console_set = $http_config['console'] ?? [];
                $_menus = $console_set['menus'] ?? [];
                if(!empty($_menus)){
                    //处理数组
                    foreach ($_menus as &$_menu){
                        //会员组别判断

                        //地址判断
                        $_list = [];
                        foreach ($_menu['list'] as $_ml){
                            //处理地址
                            if(!empty($_ml['url'])){
                                $_ml['url'] = Modular::parseUrl($_ml['url'], $module);
                            }
                            //归入
                            $_list[] = $_ml;
                        }
                        //替换原数组
                        $_menu['list'] = $_list;
                    }
                    //采用数组合并
                    $menus = array_merge($menus, $_menus);
                }
            }
        }

        return $menus;
    }
}