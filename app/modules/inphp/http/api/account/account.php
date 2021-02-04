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
namespace app\modules\inphp\http\api\account;

use app\modules\inphp\model\UserGroupModel;
use app\modules\inphp\model\UserModel;
use Inphp\Service\Object\Message;
use Inphp\Util\Arr;
use Inphp\Util\Str;

/**
 * @app\modules\inphp\attributes\Auth(uid="uid")
 * Class account
 * @package app\modules\inphp\http\api\auth
 */
class account
{
    /**
     * 自动注入的UID
     * @var int
     */
    public $uid = 0;

    /**
     * 注入当前的账号数据
     * @Inphp\Annotation\Processor\Inject(\app\modules\inphp\lib\auth\User::class)
     * @var array
     */
    public $user = [];

    /**
     * 获取账号资料
     */
    public function index(){
        //直接使用另一方法
        return $this->detail();

    }

    /**
     * 查看账户的详细资料
     */
    public function detail(){
        //地址参数
        $uid = GET("uid", $this->uid);

        //判断是不是获取非自己的账号数据
        if($this->uid != $uid){
            //检测权限
            if(!UserGroupModel::check($this->user, UserGroupModel::GROUP_ADMIN)){
                return httpMessage(errorCode(403));
            }
            //开始查询特定UID的数据
            $um = new UserModel();
            $account = $um->startQuery()->where("u.uid", $uid)->first();
            if(!empty($account)){
                $account['nickname'] = Str::base64Decode($account['nickname']);
            }
        }else{
            //直接使用当前账号数据
            $account = $this->user;
        }

        //处理数据
        $account['face'] = attachment($account['face']);

        //清除隐私数据
        $account = Arr::unsetKey($account, [
            "extend", "extend_table", "group_lock", "open_register",
            "password", "safe_code", "sp"
        ]);

        //键名排序
        ksort($account);

        //返回数据
        return $account;
    }

    /**
     * 获取账户列表
     * @return Message|array
     */
    public function list(){
        //父级条件
        $parent_uid = GET("parent_uid");
        $parent_uid = is_numeric($parent_uid) && $parent_uid >= 0 ? ceil($parent_uid) : -1;

        //权限
        $admin = UserGroupModel::check($this->user, UserGroupModel::GROUP_ADMIN);

        //如果查询的并非自己账号的下级账号，则需要特定权限
        if($parent_uid != $this->uid){
            //判断权限，仅特定权限组的账号才能使用该接口
            if(!$admin){
                return httpMessage(errorCode(403));
            }
        }

        //字段，区分管理员和普通用户获取时的数据结构
        $fields = $admin ? [
            //用户表
            "u.uid, u.parent_uid, u.group_id, u.username, u.email, u.phone, u.nickname, u.face, u.sex, u.register_time, u.register_ip, u.frozen, u.founder, u.floor",
            //用户组表
            "g.group_type, g.group_name",
            //父级推荐人
            "p.face as parent_face, p.nickname as parent_nickname, p.phone as parent_phone"
        ] : [
            //用户表
            "u.uid, u.parent_uid, u.group_id, u.username, u.email, u.phone, u.nickname, u.face, u.sex, u.register_time,u.frozen, u.founder, u.floor",
            //用户组表
            "g.group_type, g.group_name",
            //父级推荐人
            "p.face as parent_face, p.nickname as parent_nickname, p.phone as parent_phone"
        ];

        //定义空列表
        $list = [];

        //
        $um = new UserModel();

        //开始构造查询
        $db = $um->startQuery()->leftJoin("user p", ["u.parent_uid", "p.uid"])->select($fields);

        //查询特定账号下的会员
        if($parent_uid >= 0){
            $db->where("u.parent_uid", $parent_uid);
        }

        //搜索
        $keyword = GET('keyword');
        $keyword = trim($keyword);
        if(!empty($keyword)){
            if(Str::isPhoneNumber($keyword)){
                //手机号码
                $db->where("u.phone", $keyword);
            }elseif(Str::isEmail($keyword)){
                //邮箱
                $db->where("u.email", $keyword);
            }elseif(mb_strlen($keyword) >= 2){
                //其它：用户名、昵称
                $db->where(function ($q) use($keyword){
                    $q->whereLike("u.username", "%{$keyword}%")
                        ->orWhereLike("u.nickname", "%{$keyword}%")
                        ->orWhereRaw("from_base64(substr(u.nickname, 8)) like '%{$keyword}%'");
                });
            }
        }

        //分组
        $group_id = GET("group_id");
        $group_id = is_numeric($group_id) && $group_id > 0 ? ceil($group_id) : 0;
        if($group_id > 0){
            $db->where("u.group_id", $group_id);
        }

        //账号状态
        $frozen = GET("frozen");
        $frozen = $frozen == '1' || $frozen == '0' ? $frozen : null;
        if(!is_null($frozen)){
            $db->where("u.frozen", $frozen);
        }

        //总数据行数
        $rows = $db->rows();
        //总页数
        $pages = 0;
        //当前页
        $page = GET("page", 1);
        $page = is_numeric($page) && $page>0 ? ceil($page) : 1;
        //分页数量
        $total = GET("total", 30);
        //默认30行分页
        $total = is_numeric($total) && $total > 0 ? ceil($total) : 30;
        //最多一次获取不超过500行
        $total = $total > 500 ? 500 : $total;
        //仅在有数据情况下处理...
        if($rows>0){
            //取得总页数
            $pages = ceil($rows/$total);
            $page = $page>$pages ? $pages : $page;
            //起始读取下标
            $offset = $total*($page-1);
            //读取
            $list = $db->orderBy("u.uid", "desc")
                ->get($total, $offset);
            foreach ($list as &$item){
                $item['nickname'] = Str::base64Decode($item['nickname']);
                $item['face'] = attachment($item['face']);
                $item['parent_nickname'] = Str::base64Decode($item['parent_nickname']);
                $item['parent_face'] = attachment($item['parent_face']);
            }
        }

        //返回列表数据
        return httpMessage([
            "rows"  => intval($rows),
            "page"  => intval($page),
            "pages" => intval($pages),
            "list"  => $list
        ]);
    }

    /**
     * 编辑保存
     */
    public function save(){
        //权限
        $admin = UserGroupModel::check($this->user, UserGroupModel::GROUP_ADMIN);
        //如果何在的不是自己的资料，需要超级管理员权限
        $uid = POST("uid", $this->uid);
        $uid = is_numeric($uid) && $uid > 0 ? $uid : $this->uid;
        if($this->uid != $uid){
            if(!$admin){
                return httpMessage(errorCode(403));
            }
        }

        $data = [];
        $um = new UserModel();

        //开始修改
        $nickname = POST("nickname", null, "缺少参数");
        $nickname = trim($nickname);
        if(mb_strlen($nickname) > 20 || strlen($nickname) > 40){
            return httpMessage("昵称无效");
        }
        $data['nickname'] = $nickname;

        if($admin){
            //如果修改的是超级管理员
            $edit = $um->mainQuery()
                ->where("uid", $uid)
                ->first();
            if(empty($edit)){
                return httpMessage("参数无效");
            }
            if($this->uid != $uid) {
                //除了超管，不可修改其它管理员
                if($this->user['founder'] == 0){
                    if ($edit['founder'] == 1 || $edit['group_id'] == 1) {
                        return httpMessage(errorCode(403));
                    }
                }
            }
            //用户名
            $username = POST("username");
            $username = Str::trim($username);
            if(!Str::isLettersOrNumbersOrUnderscores($username)){
                return httpMessage("用户名无效");
            }
            if(!empty($username)){
                //需要查询用户名是否已被使用
                $row = $um->startQuery()->where("uid", "!=", $uid)->where("username", $username)->first();
                if(!empty($row)){
                    return httpMessage("用户名已存在");
                }
                $data['username'] = $username;
            }
            //手机号码
            $phone = POST("phone");
            $phone = Str::trim($phone);
            if(!empty($phone)){
                if(!Str::isPhoneNumber($phone)){
                    return httpMessage("手机号码格式错误");
                }
                //查询是否存在相同的手机号码
                $row = $um->startQuery()->where("uid", "!=", $uid)->where("phone", $phone)->first();
                if(!empty($row)){
                    return httpMessage("手机号码已被使用");
                }
                $data['phone'] = $phone;
            }
            //
            $group_id = POST('group_id');
            $group_id = is_numeric($group_id) && $group_id > 0 ? $group_id : 0;
            if($group_id > 0){
                //查询
                $gm = new UserGroupModel();
                $row = $gm->mainQuery()->where("group_id", $group_id)->first();
                if(empty($row)){
                    return httpMessage("分组参数无效");
                }
                $data['group_id'] = $group_id;
            }
        }

        //头像
        $face = POST("face");
        $face = trim($face);
        $face = Str::cleanIllegalString($face);
        if(!empty($face)){
            $face = parseUploadFilesUrl($face);
            $data['face'] = $face;
        }

        $db = $um->startQuery()->where("uid", $uid)->set($data);
        if($db->update()){
            return httpMessage(0, "保存成功");
        }else{
            return httpMessage("保存失败");
        }

    }

    /**
     * 添加账号，这里仅支持管理员使用
     */
    public function register(){
        //权限
        $admin = UserGroupModel::check($this->user, UserGroupModel::GROUP_ADMIN);
        if(!$admin){
            return httpMessage(errorCode(403));
        }
    }
}