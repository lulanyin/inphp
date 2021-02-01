<?php
namespace app\modules\inphp\attributes;

use app\modules\inphp\model\LoginHistoryModel;
use Doctrine\Common\Annotations\Annotation\Target;
use Inphp\Annotation\IAnnotation;
use Inphp\DB\Cache;
use Inphp\Modular;
use Inphp\Service\Context;
use Inphp\Service\Http\Request;
use Inphp\Util\Str;

/**
 * @Annotation()
 * @Target({"CLASS", "METHOD"})
 * Class Auth
 * @package app\modules\inphp\attributes
 */
class Auth implements IAnnotation
{
    /**
     * @var string
     */
    public $group = null;

    /**
     * @var int
     */
    public $level = null;

    /**
     *
     * @var null
     */
    public $inject = null;

    /**
     *
     * @var null
     */
    public $uid = null;

    /**
     * 重定向
     * @var string
     */
    public $redirect = "./login";

    /**
     * 当前重定向模块
     * @var string
     */
    public $module = 'inphp';

    public function __construct(array $values)
    {
        if(isset($values['value'])){
            $this->group = $values['value'];
            $this->group = Str::trim($this->group);
            $this->group = !empty($this->group) ? explode(",", $this->group) : [];
        }
        if(isset($values['group'])){
            $this->group = $values['group'];
            $this->group = Str::trim($this->group);
            $this->group = !empty($this->group) ? explode(",", $this->group) : [];
        }
        if(isset($values['level'])){
            $this->level = $values["level"];
        }
        if(isset($values['inject'])){
            $this->inject = $values["inject"];
        }
        if(isset($values['uid'])){
            $this->uid = $values["uid"];
        }
        if(isset($values['redirect'])){
            $this->redirect = $values['redirect'];
        }
        if(isset($values['module'])){
            $this->module = $values['module'];
        }
    }

    public function process($class, string $target = null, string $targetType = null)
    {
        // TODO: Implement process() method.
        // TODO: Implement process() method.
        // 首先检测登录情况
        // 获取token
        $token = Request::get("token", Request::post('token', Request::getCookie('token')));
        if(!empty($token)){
            if($info = Cache::get($token)){
                //判断过期时间
                $expTime = $info['exp_time'] ?? 0;
                if($expTime > time()){
                    //还在登录有效期
                    //检查
                    $bool1 = true;
                    if(!is_null($this->group)){
                        $bool1 = in_array($info['group'], $this->group);
                        if(!$bool1){
                            //非此会员组
                            $response = Context::getResponse();
                            if(stripos($response->status->response_content_type, 'json') !== false){
                                //输出
                                $response->withJson(json(1, "当前账户无权限"))->send();
                            }else{
                                //重定向
                                $response->redirect(Modular::parseUrl($this->redirect, $this->module));
                            }
                            return;
                        }
                    }
                    $bool2 = true;
                    if(!is_null($this->level)){
                        $bool2 = $info['level'] >= $this->level;
                    }
                    //判断是否都通过检测
                    if(!$bool2 || !$bool1){
                        //无权限
                        $response = Context::getResponse();
                        if(stripos($response->status->response_content_type, 'json') !== false){
                            //输出
                            $response->withJson(json(1, "账号无权限"))->send();
                        }else{
                            //重定向
                            $response->redirect(Modular::parseUrl($this->redirect, $this->module));
                        }
                        return;
                    }else{
                        //更新登录记录，以保证能持续有效
                        $hm = new LoginHistoryModel();
                        //校验TOKEN
                        if(!$hm->verifyToken($token)){
                            Cache::remove($token);
                            $response = Context::getResponse();
                            if(stripos($response->status->response_content_type, 'json') !== false){
                                //输出
                                $response->withJson(json(1, "Token已过期"))->send();
                            }else{
                                //重定向
                                $response->redirect(Modular::parseUrl($this->redirect, $this->module));
                            }
                            return;
                        }else{
                            $hm->saveToken($info['uid'], $info['exp_seconds'], $token, $info['group'], $info['level']);
                            Cache::update($token, [
                                "exp_time"  => time() + $info['exp_seconds']
                            ]);
                            //
                            if($this->inject){
                                if(property_exists($class, $this->inject)){
                                    $class->{$this->inject} = $info;
                                }
                            }
                            if($this->uid){
                                if(property_exists($class, $this->uid)){
                                    $class->{$this->uid} = $info['uid'];
                                }
                            }
                        }
                    }
                    return;
                }else{
                    Cache::remove($token);
                }
            }
        }
        //未登录，将触发跳转
        $response = Context::getResponse();
        if(stripos($response->status->response_content_type, 'json') !== false){
            //输出
            $response->withJson(json(1, "未登录，无权限"))->send();
        }else{
            //重定向
            $response->redirect(Modular::parseUrl($this->redirect, $this->module));
        }
    }
}