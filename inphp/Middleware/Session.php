<?php
namespace Inphp\Middleware;

use Inphp\DB\Cache;
use Inphp\Service\Middleware\ISessionMiddleware;

class Session implements ISessionMiddleware
{
    /**
     * 当前的Session id
     * @var string|null
     */
    private $session_id = null;

    /**
     * 已保存的数据
     * @var array
     */
    private $session = [];

    /**
     * Session constructor.
     * @param string $session_id
     */
    public function __construct(string $session_id)
    {
        $this->session_id = $session_id;
        $data = Cache::get($this->session_id);
        $data = is_array($data) ? $data : [];
        $this->session = $data;
    }

    /**
     * 获取session
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function get(string $name = null, $default = null){
        return is_null($name) ? $this->session : ($this->session[$name] ?? $default);
    }

    /**
     * 设置session
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value = null){
        echo $name." : ".$value;
        $this->session[$name] = $value;
        if(is_null($this->session[$name])){
            unset($this->session[$name]);
        }
        Cache::set($this->session_id, $this->session);
    }

    /**
     * 移除session
     * @param string $name
     */
    public function drop(string $name){
        $this->set($name, null);
    }
}