<?php
// +----------------------------------------------------------------------
// | INPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2021 https://inphp.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://opensource.org/licenses/MIT )
// +----------------------------------------------------------------------
// | Author: lulanyin <me@lanyin.lu>
// +----------------------------------------------------------------------
namespace app\modules\inphp\http\api\auth;

use app\modules\inphp\lib\auth\User;
use app\modules\inphp\lib\sms\SMS;
use app\modules\inphp\model\LoginHistoryModel;
use app\modules\inphp\model\UserModel;
use Inphp\Util\Str;

/**
 * Class token
 * @package app\modules\inphp\http\api\auth
 */
class token
{
    public function index()
    {
        //常规账号（包含手机号码）
        $username = POST('username', '');
        //手机号码，与上方 username 二取一
        $phone = POST('phone', '');
        if(!empty($phone) && !Str::isPhoneNumber($phone))
        {
            return ajaxMessage("手机号码格式错误");
        }
        $username = Str::isPhoneNumber($phone) ? $phone : $username;
        if(!Str::isLNU($username) && !Str::isPhoneNumber($username))
        {
            return ajaxMessage('账号格式错误');
        }
        //登录密码/手机验证码
        $password = POST('password', null, "缺少参数 : password");
        //登录方式， password = 密码登录, phone_code = 验证码登录
        $type = POST('type', 'password');
        $type = $type == 'password' ? 'password' : 'phone_code';
        if($type == 'phone_code' && !Str::isPhoneNumber($username))
        {
            return ajaxMessage('手机号码格式错误');
        }
        //登录密码是否已加密
        $hash = POST('hash');

        if($type == 'password' )
        {
            //使用密码登录
            $auth = new User(false);
            $auth->md5Pwd = !empty($hash);
            if($info = $auth->login($username, $password))
            {
                return ajaxMessage([
                    "token" => $info["token"],
                    "username" => $info['username']
                ]);
            }
            else
            {
                return ajaxMessage($auth->errorInfo);
            }
        }
        else
        {
            //使用验证码登录
            if(!SMS::checkSMS($username, $password, "login"))
            {
                return ajaxMessage("手机验证码错误");
            }
            else
            {
                //插入一行数据的方法去创建Token
                $um = new UserModel();
                $info = $um->where("u.phone", $username)->first();
                if(empty($info))
                {
                    return ajaxMessage("手机号码无效");
                }
                //判断是否账户正常
                $frozen = isset($info['frozen']) ? $info['frozen'] : 0;
                $destroy = isset($info['destroy']) ? $info['destroy'] : 0;
                if($frozen == 1 || $destroy == 1)
                {
                    return ajaxMessage("账号已禁止登录");
                }
                $m = new LoginHistoryModel();
                $token = $m->saveToken($info['uid'], 86400, null, $info['group_type'], $info['level'] ?? -1);
                //标记短信已被使用
                SMS::checkedSMS($username, 'login');
                return ajaxMessage([
                    "token" => $token,
                    "username" => $info['username']
                ]);
            }
        }
    }
}