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
namespace app\modules\inphp\model;

use Inphp\DB\Cache;
use Inphp\DB\DB;
use Inphp\DB\Model\ModelBase;
use Inphp\Util\Str;

/**
 * 第三方绑定
 * Class OrderProModel
 * @package app\model
 */
class UserBindModel extends ModelBase
{
    //主表名
    protected $tableName = "user_bind";

    const PLATFORM_WECHAT = "wechat";
    const PLATFORM_ALIPAY = "alipay";

    public static function getPlatform($uid, $platform)
    {
        $m = new UserBindModel();
        $row = $m->mainQuery()->where("platform", $platform)->where("uid", $uid)->first();
        if(!empty($row))
        {
            return [
                "id"        => $row['id'],
                "platform"  => $row['platform'],
                "open_id"   => $row['open_id'],
                "union_id"  => $row['union_id']
            ];
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取微信绑定数据
     * @param $res
     * @return array|null
     */
    public static function getWechatBind($res){
        $bm = new UserBindModel();
        //先查open_id
        $row = $bm->mainQuery()
            ->where("platform", self::PLATFORM_WECHAT)
            ->where("open_id", $res['openid'])
            ->first();
        //如果open_id查不到，再查union_id，因为有可能平台运营问题，导致一开始没有绑定到union_id
        if(empty($row) && !empty($res['unionid'])){
            $row = $bm->mainQuery()
                ->where("platform", self::PLATFORM_WECHAT)
                ->where("union_id", $res['unionid'])
                ->first();
        }
        return !empty($row) ? $row : null;
    }

    public static function bindWechat($uid, $res){
        $bm = new UserBindModel();
        @$bm->mainQuery()
            ->insert([
                "uid"       => $uid,
                "platform"  => "wechat",
                "open_id"   => $res['openid'],
                "union_id"  => $res['unionid'] ?? '',
                "userinfo"  => json_encode($res)
            ]);
    }

    /**
     * 需要验证的绑定
     * @param $type
     * @param $target_id
     * @param $res
     * @param $info
     * @return array|bool|int|mixed
     */
    public static function bindWechatWait($type, $target_id, $res, $info = []){
        $db = DB::from("user_bind_wait");
        if($db->insert([
            "type"      => $type == 'user' ? 'user' : 'mall',
            "target_id" => $target_id,
            "platform"  => "wechat",
            "open_id"   => $res['openid'],
            "union_id"  => $res['unionid'] ?? '',
            "userinfo"  => json_encode($res),
            //公开资料
            "open_info" => json_encode($info)
        ])){
            return $db->getLastInsertId();
        }else{
            return false;
        }
    }

    const WAIT_TYPES = ["user", "mall", "employee"];

    public static function setWaitPassword($type, $id){
        $password = Str::randomNumber(6);
        Cache::set((in_array($type, self::WAIT_TYPES) ? $type : "user")."_bind_password_".$id, md5($password));
        return $password;
    }

    public static function checkWaitPassword($type, $id, $password){
        $md5 = Cache::get((in_array($type, self::WAIT_TYPES) ? $type : "user")."_bind_password_".$id);
        return md5($password) == $md5;
    }

    public static function deleteBindWait($type, $id){
        @DB::from("user_bind_wait")
            ->where("type", (in_array($type, self::WAIT_TYPES) ? $type : "user"))
            ->where("target_id", $id)
            ->delete();
        Cache::remove((in_array($type, self::WAIT_TYPES) ? $type : "user")."_bind_password_".$id);
    }
}
