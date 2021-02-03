<?php
namespace app\modules\inphp\http\api\account;

use app\modules\inphp\model\UserGroupModel;

/**
 * 分组
 * Class group
 * @package app\modules\inphp\http\api\account
 */
class group
{

    /**
     * 获取分组列表
     */
    public function list(){
        $gm = new UserGroupModel();
        return $gm->startQuery()->get();
    }
}