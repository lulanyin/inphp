<?php
namespace app\modules\inphp\lib\auth;

use Inphp\DB\DB;

class User extends AuthUser{

    /**
     * 实现Inject注解类的处理，Inject(User::class)
     * @param $class
     * @param string $target
     * @param string $targetType
     */
    public function Inject($class, string $target, string $targetType)
    {
        // TODO: Implement process() method.
        switch ($targetType){
            case "property" :
                $class->{$target} = $this->userInfo;
                break;
            case "class" :
                if(property_exists($class, "user")){
                    $class->user = $this->userInfo;
                }
                if(property_exists($class, "userInfo")){
                    $class->userInfo = $this->userInfo;
                }
                break;
        }
    }

    /**
     * 静态方法数据
     * @var array
     */
    public static $staticUserInfo = [];

    /**
     * 静态方法检测登录
     * @param null $group
     * @return bool
     */
    public static function isLogin($group = null){
        if(empty(static::$staticUserInfo)){
            $class = new User();
            if($class->isLogin){
                self::$staticUserInfo = $class->userInfo;
            }else{
                return false;
            }
        }
        return !is_null($group) ? self::$staticUserInfo['group_type']==$group : true;
    }

    /**
     * 检测二级密码
     * @param int $uid
     * @param string $value
     * @return bool
     */
    public static function checkSafePassword(int $uid, string $value){
        $sp = DB::from("user u")
            ->where("u.uid", $uid)
            ->pluck("sp");
        return md5(md5($value).$uid) == $sp;
    }

    /**
     * 更改二级密码
     * @param $uid
     * @param $newPassword
     */
    public static function updateSecondPassword($uid, $newPassword){
        @DB::from("user")
            ->where("uid", $uid)
            ->update([
                "sp"    => md5(md5($newPassword).$uid)
            ]);
    }

    /**
     * 检测是否已存在
     * @param $value
     * @param string $field
     * @param int $exists_uid
     * @return array|null
     */
    public static function exists($value, $field = 'username', $exists_uid = 0){
        $db = DB::from("user");
        if($exists_uid > 0){
            $db->where('uid', '!=', $exists_uid);
        }
        switch ($field){
            case "nickname" :
                $db->where("from_base64(substr(u.nickname, 8))", $value);
                break;
            default :
                $db->where($field, $value);
                break;
        }
        $row = $db->first();
        return !empty($row) ? $row : null;
    }

    /**
     * 退出登录
     */
    public static function exit(){
        $u = new User();
        $u->logout();
    }
}