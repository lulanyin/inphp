<?php
/**
 * Create By Hunter
 * 2020/12/2 10:08 上午
 *
 */
namespace app\modules\inphp\http\admin;

use app\modules\inphp\lib\auth\User;
use Inphp\Modular;

/**
 * 控制台默认入口，需要登录后台的权限
 * @app\modules\inphp\attributes\Auth(uid="uid", redirect="./admin/login", module="inphp")
 * Class index
 * @package app\modules\inphp\http\admin
 */
class index
{
    /**
     * UID
     * @var int
     */
    public $uid = 0;

    /**
     * 注入用户资料
     * @Inphp\Annotation\Processor\Inject(User::class)
     * @var array
     */
    public $user = [];

    /**
     * 默认入口
     */
    public function index(){
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
        assign("menus", $menus);
        assign('user', $this->user);
    }

    public function exit(){
        $user = new User(true);
        $user->logout();
        //重定向
        redirect(url("./admin/login", 'inphp'));
    }
}