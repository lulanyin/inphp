<?php
namespace app\modules\inphp\lib\sms;

use app\modules\inphp\model\SmsModel;
use Inphp\Config;
use Inphp\Util\Str;

trait TSms{

    public static $config = [];

    public static function init(){
        if(empty(self::$config)){
            self::$config = Config::get("private.sms");
        }
    }

    /**
     * 插入消息到数据库
     * @param int $phone
     * @param string $type
     * @param int $temp_id
     * @param null $message
     * @return bool|null|array
     */
    public static function insertMessage(int $phone, string $type, int $temp_id, $message = null){
        self::init();
        if(Str::isPhoneNumber($phone)){
            $m = new SmsModel();
            $maxRows = self::$config["max_times"] ?? 5;
            $lifeTime = self::$config['life_time'] ?? 1800;
            $rows = $m->mainQuery("s")
                ->where("s.phone", $phone)
                ->where("s.checked", 0)
                ->where("s.create_time", ">=",time2datetime(time()-$lifeTime))
                ->rows();
            //如果未定义，则定义为验证码并自动生成
            $message = is_null($message) ? Str::randomNumber(6) : $message;
            if($rows>=$maxRows){
                //次数已超过限制，获取最后一行数据
                $row = $m->mainQuery()
                    ->where("phone", $phone)
                    ->orderBy("create_time", "desc")
                    ->first();
                $row = !empty($row) ? $row : [];
                return [$row['id'] || 0, $row['code'] || $message];
            }
            $data = [
                'phone'         => $phone,
                'code'          => $message,
                'type'          => $type,
                'lost_time'     => time2datetime(time()+$lifeTime),
                'state'         => 9,
                'temp_id'       => $temp_id,
                'ip'            => getIP()
            ];
            $db = $m->mainQuery();
            if($db->insert($data)){
                return [$db->getLastInsertId(), $message];
            }
        }
        return false;
    }

    /**
     * 设置发送状态结果
     * @param $id
     * @param $state
     */
    public static function setState($id, $state){
        $m = new SmsModel();
        $m->mainQuery()
            ->where("id", $id)
            ->update([
                'state'     => $state
            ]);
    }

    /**
     * 检测短信是否可用
     * @param $phone
     * @param $value
     * @param $type
     * @return bool
     */
    public static function checkSMS($phone, $value, $type){
        $m = new SmsModel();
        $rows = $m->mainQuery("s")
            ->where("s.phone", $phone)
            ->where("s.code", $value)
            ->where("s.type", $type)
            ->where("s.checked", 0)
            ->rows();
        return $rows>0;
    }

    /**
     * 使用短信
     * @param $phone
     * @param $type
     */
    public static function checkedSMS($phone, $type){
        $m = new SmsModel();
        @$m->mainQuery()
            ->where("phone", $phone)
            ->where("type", $type)
            ->where("checked", 0)
            ->update([
                "checked"   => 1
            ]);
    }
}