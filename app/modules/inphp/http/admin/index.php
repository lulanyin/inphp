<?php
/**
 * Create By Hunter
 * 2020/12/2 10:08 上午
 *
 */
namespace app\modules\inphp\http\admin;

use app\modules\inphp\lib\auth\User;

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
     * 默认入口
     */
    public function index(){

    }

    public function exit(){
        $user = new User(true);
        $user->logout();
        //重定向
        redirect(url("./admin/login", 'inphp'));
    }
}