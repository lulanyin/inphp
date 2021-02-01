<?php
namespace app\modules\inphp\model;

use Inphp\DB\Model\ModelBase;

/**
 * 会员拓展资料
 * Class OrderProModel
 * @package app\model
 */
class UserExtendModel extends ModelBase {
    //主表名
    protected $tableName = "user_extend";

    /**
     * 根据UID获取
     * @param $uid
     * @return array|null
     */
    public static function getUid($uid){
        $m = new UserExtendModel();
        $row = $m->mainQuery()
            ->where('uid', $uid)
            ->first();
        return !empty($row) ? $row : null;
    }

    /**
     * 判断拓展资料状态
     * @param $uid
     * @param int $state
     * @return bool
     */
    public static function checkState($uid, $state = 1){
        if($row = self::getUid($uid)){
            return $row['state'] == $state;
        }
        return false;
    }

    /**
     * 检测拓展资料
     * @param $uid
     * @param array $fields
     * @return bool
     */
    public static function checkFields($uid, array $fields){
        if($row = self::getUid($uid)){
            foreach ($fields as $field){
                if(empty($row[$field])){
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 检测身份证号码
     * @param $id_number
     * @return array|null
     */
    public static function checkIdNumber($id_number){
        $m = new UserExtendModel();
        $row = $m->mainQuery()
            ->where('id_number', $id_number)
            ->first();
        return !empty($row) ? $row : null;
    }

    public static function getState($state){
        return['无效', '已认证，不可更改', '待审核'][$state] ?? '未知状态';
    }

    /**
     * 数据备份
     * @param $uid
     */
    public static function backup($uid){
        $m = new UserExtendModel();
        $row = $m->mainQuery()
            ->where('uid', $uid)
            ->first();
        if(!empty($row)){
            $bm = new UserExtendBakModel();
            $bm->mainQuery()
                ->insert($row);
        }
    }

    public static function rows(){
        $m = new UserExtendModel();
        $row = $m->mainQuery()
            ->where('state', 1)
            ->rows();
        return $row ?? 0;
    }
}
