<?php
namespace app\modules\inphp\model{

    use Inphp\DB\Cache;
    use Inphp\DB\Model\ModelBase;
    use Inphp\Service\Http\Request;

    /**
     * 登录记录，包含TOKEN
     * Class LoginHistoryModel
     * @package app\model
     */
    class LoginHistoryModel extends ModelBase {
        //主表名
        protected $tableName = "login_history";
        //主表别名
        protected $as = "h";
        //关联查询的表
        protected $join = [
            "user u"
        ];
        //关联相询方式, inner, left, right
        protected $joinType = [
            "inner"
        ];
        //关联条件
        protected $joinWhere = [
            ["h.uid", "u.uid"]
        ];

        /**
         * 由TOKEN获取登录记录
         * @param $token
         * @return array
         */
        public function getHistoryByToken($token){
            return $this->mainQuery()->where("token", $token)->first();
        }

        public function verifyToken($token){
            $row = $this->where("h.token", $token)->first();
            return !empty($row) ? true : false;
        }

        /**
         * 获取规定时间段内的错误次数
         * @param $uid
         * @param int $seconds
         * @return int
         */
        public function getErrorHistory($uid, $seconds = 3600){
            $time = time()-$seconds;
            $m = new LoginHistoryModel();
            $rows = $m->mainQuery("h")
                ->where("h.uid", $uid)
                ->where("h.clean", 0)
                ->where("success", 0)
                ->where("h.login_time", ">=", time2datetime($time))
                ->rows();
            return !empty($rows) ? $rows : 0;
        }

        /**
         * 保存TOKEN
         * @param $uid
         * @param int $time
         * @param null $token
         * @param null $group
         * @param int $level
         * @return null|string
         */
        public function saveToken($uid, $time = 86400, $token = null, $group = null, $level = -1){
            $now = time();
            if(is_null($token)){
                $token = $this->getToken($uid);
                if(is_null($group)){
                    $um = new UserModel();
                    $userInfo = $um->where("u.uid", $uid)
                        ->select("u.uid, u.level, g.group_type")->first();
                    $userInfo = $userInfo ?? [
                        'group_type'    => $group,
                        "level"         => $level
                    ];
                }else{
                    $userInfo = [
                        'group_type'    => $group,
                        "level"         => $level
                    ];
                }
                //保存到缓存、Cookie、Session
                Cache::set($token, [
                    "uid"       => $uid,
                    "group"     => $userInfo['group_type'],
                    "level"     => $userInfo['level'],
                    "exp_seconds" => $time,
                    "exp_time"  => $now + $time
                ], $time);
                Request::setCookie("token", $token, $time);
            }
            //如果此UID已存在此TOKEN，则直接更新时间即可
            $m = new LoginHistoryModel();
            $history = $m->mainQuery()->where("uid", $uid)
                ->where("token", $token)->first();
            if(!empty($history)){
                $m->mainQuery()
                    ->where("id", $history['id'])
                    ->update([
                        "success"   => 1,
                        "clean"     => 1,
                        "error_code"=> 0,
                        "login_ip"  => getIP(),
                        "exp_time"  => time2datetime($now + $time)
                    ]);
            }else{
                $this->saveHistory($uid, 0, $token, $time);
            }
            return $token;
        }

        /**
         * 保存登录记录，错误的也在这记录
         * @param $uid
         * @param $error
         * @param null $token
         * @param int $time
         */
        public function saveHistory($uid, $error, $token = null, $time = 0){
            $now = time();
            $data = [
                "uid"       => $uid,
                "login_ip"  => getIP(),
                "login_time"=> time2datetime($now),
                "success"   => $error==0 ? 1 : 0,
                "clean"     => 0,
                "error_code"=> $error,
                "token"     => $token ?? 'NULL',
                "exp_time"  => $token ? time2datetime($now+$time) : time2datetime($now)
            ];
            $m = new LoginHistoryModel();
            $m->mainQuery()
                ->insert($data);
        }

        /**
         * 生成TOKEN
         * @param $uid
         * @return string
         */
        public function getToken($uid){
            $now = time();
            return md5(sha1($uid).$now);
        }

    }
}