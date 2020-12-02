<?php
// +----------------------------------------------------------------------
// | INPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2020 https://inphp.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://opensource.org/licenses/MIT )
// +----------------------------------------------------------------------
// | Author: lulanyin <me@lanyin.lu>
// +----------------------------------------------------------------------
namespace Inphp\Object;

use Inphp\Modular;
use Inphp\Service\Service;

/**
 * 模块对象
 * Class ModuleObject
 * @package Inphp\Object
 */
class ModuleObject
{

    /**
     * ID，其实就是文件夹全称，正常的模块使用的是 module_{数字ID}
     * @var mixed|string
     */
    public $id = "";

    /**
     * 模块名称
     * @var mixed|string
     */
    public $name = '未知模块';

    /**
     * 根目录的命名空间
     * @var string
     */
    public $namespace = "";

    /**
     * 路径
     * @var string
     */
    public $file_path = "";

    /**
     * http配置
     * @var array|mixed
     */
    public $http_config = [];

    /**
     * ws配置
     * @var array|mixed
     */
    public $ws_config = [];

    /**
     * ModuleObject constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->name = $config['name'] ?? '未知模块';
        $this->id = $config['id'] ?? 'unknow';
        $this->http_config = $config['http'] ?? [];
        $this->ws_config = $config['ws'] ?? [];
        $home = Modular::getConfig("home");
        $this->namespace = str_replace("\\\\", '\\',$home."\\".$this->id."\\");
        $this->file_path = str_replace("//", "/", ROOT."/".str_replace("\\", "/", $this->namespace));
    }

    /**
     * 仅获取入口的值
     * @param string $group
     * @return string
     */
    public function getHomeValue($group = Service::HTTP){
        return $group == Service::HTTP ? $this->http_config['home'] : $this->ws_config['home'];
    }

    /**
     * 获取入口
     * @param string $group
     * @return string
     */
    public function getHome($group = Service::HTTP){
        return $group == Service::WS ? $this->getWsHome() : $this->getHttpHome();
    }

    /**
     * 获取http的入口
     */
    public function getHttpHome(){
        return str_replace("\\\\", '\\',$this->namespace.$this->http_config['home'])."\\";
    }

    /**
     * 获取ws的入口
     */
    public function getWsHome(){
        return str_replace("\\\\", '\\',$this->namespace.$this->ws_config['home'])."\\";
    }

    /**
     * 获取可用路由列表
     * @param string $group
     * @return array
     */
    public function getRouterList($group = Service::HTTP){
        return $group == Service::HTTP ? $this->getHttpRouterList() : $this->getWsRouterList();
    }

    /**
     * 获取http路由列表
     * @return array
     */
    public function getHttpRouterList(){
        return $this->http_config['list'] ?? [];
    }

    /**
     * 获取ws路由列表
     * @return array
     */
    public function getWsRouterList(){
        return $this->ws_config['list'] ?? [];
    }

    /**
     * 获取默认路由
     * @param string $group
     * @return mixed|string
     */
    public function getDefaultRouter($group = Service::HTTP){
        return $group == Service::HTTP ? $this->getHttpDefaultRouter() : $this->getWsDefaultRouter();
    }

    /**
     * 获取默认的http路由
     * @return mixed|string
     */
    public function getHttpDefaultRouter(){
        return $this->http_config['default'] ?? "/";
    }

    /**
     * 获取默认的ws路由
     * @return mixed|string
     */
    public function getWsDefaultRouter(){
        return $this->ws_config['default'] ?? "/";
    }

    /**
     * 获取视图文件位置，全路径
     * @return string
     */
    public function getViewDir(){
        $dir = $this->http_config['view'] ?? "view";
        return str_replace("//", "/", $this->file_path."/".$dir);
    }

    /**
     * 获取视图文件后续
     * @return string
     */
    public function getViewSuffix(){
        return $this->http_config['view_suffix'] ?? 'html';
    }

    /**
     * 获取内容响应类型，仅HTTP
     * @param null $part
     * @return string
     */
    public function getResponseContentType($part = null){
        $response_content_type = $this->http_config['response_content_type'] ?? [];
        return is_null($part) ? (is_string($response_content_type) ? $response_content_type : "default") : ($response_content_type[$part] ?? 'default');
    }
}