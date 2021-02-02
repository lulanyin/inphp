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
namespace app\modules\inphp\model{

    use Inphp\DB\Model\ModelBase;

    /**
     * 用户分组
     * Class UserGroupModel
     * @package app\model
     */
    class UserGroupModel extends ModelBase {
        //主表名
        protected $tableName = "user_group";
    }
}