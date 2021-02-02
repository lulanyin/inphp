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

use Inphp\DB\Model\ModelBase;
use Inphp\Util\Str;

/**
 * 用户表
 * Class UserModel
 * @package app\model
 */
class UserModel extends ModelBase
{
    //主表名
    protected $tableName = "user";
    //主表别名
    protected $as = "u";
    //关联查询的表
    protected $join = [
        "user_group g"
    ];
    //关联相询方式, inner, left, right
    protected $joinType = [
        "inner"
    ];
    //关联条件
    protected $joinWhere = [
        ["u.group_id", "g.group_id"]
    ];


    /**
     * 获取推荐码对应的会员数据
     * @param $invite
     * @return array
     */
    public static function getInviteInfo($invite){
        $invite = Str::trim($invite);
        $invite = Str::replace(".", "", $invite);
        if(!empty($invite)){
            $um = new UserModel();
            $parent = $um->mainQuery()
                ->where('frozen', 0)
                ->where(Str::isPhoneNumber($invite) ? 'phone' : 'username', $invite)->first();
            if(!empty($parent)){
                return [
                    "uid"   => $parent['uid'],
                    "parents_uid" => $parent['parents_uid'],
                    "floor" => $parent['floor']
                ];
            }
        }
        return [
            "uid"       => 0,
            "parents_uid" => null,
            "floor"     => 0
        ];
    }

    public static function getParent($uid){
        $um = new UserModel();
        $user = $um->mainQuery()
            ->where("uid", $uid)
            ->first();
        if(!empty($user)){
            $parent_uid = $user['parent_uid'];
            if($parent_uid > 0){
                $parent = $um->mainQuery()
                    ->where("uid", $parent_uid)
                    ->first();
                if(!empty($parent)){
                    return $parent;
                }
            }
        }
        return null;
    }

    public static function getLevelManager($uid){
        $um = new UserModel();
        $user = $um->mainQuery()
            ->where("uid", $uid)
            ->first();
        if(!empty($user)){
            $parents_uid = $user['parents_uid'];
            if(!empty($parents_uid)){
                //每4级一个管理员
                //获取我这个等级的管理员, 如果我是管理员等级，则获取高一级的管理员
                $less = $user['level'] % 4;
                if($less == 0){
                    $target_level = ceil($user['level'] / 4) * 4 + 4;
                }else{
                    $target_level = ceil($user['level'] / 4) * 4;
                }
                $manager = $um->mainQuery()
                    ->whereIn("uid", $parents_uid)
                    //被4整除
                    ->whereRaw("`level` mod 4 = 0")
                    //未冻结
                    ->where("frozen", 0)
                    //同时等级要大于等于目标等级
                    ->where("level", ">=", $target_level)
                    //按层级倒序排列
                    ->orderBy("floor", "desc")
                    ->first();
                if(!empty($manager)){
                    return $manager;
                }
            }
        }
        return null;
    }

    public static function getExtend($uid){
        return UserExtendModel::getUid($uid);
    }

    public static function isGroup($user, $group){
        return $user['group_type'] == $group;
    }

    public static function isFrozen($user){
        return $user['frozen'] == 1;
    }
}