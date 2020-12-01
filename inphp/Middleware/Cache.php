<?php
/**
 * Create By Hunter
 * 2020/12/1 4:54 下午
 *
 */
namespace Inphp\Middleware;

use Inphp\Service\Middleware\ICacheMiddleware;

/**
 * 使用Redis实现缓存
 * Class Cache
 * @package Inphp\Middleware
 */
class Cache implements ICacheMiddleware
{
    /**
     * 获取
     * @param string $name
     * @param $default
     * @return mixed
     */
    public function get(string $name, $default)
    {
        // TODO: Implement get() method.
        return \Inphp\DB\Cache::get($name, $default);
    }

    /**
     * 设置
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        // TODO: Implement set() method.
        \Inphp\DB\Cache::set($name, $value);
    }

    /**
     * 移除
     * @param string $name
     */
    public function remove(string $name)
    {
        // TODO: Implement remove() method.
        \Inphp\DB\Cache::remove($name);
    }

    /**
     * 清除
     * @param string $name
     */
    public function clean(string $name)
    {
        // TODO: Implement clean() method.
        \Inphp\DB\Cache::clear();;
    }
}