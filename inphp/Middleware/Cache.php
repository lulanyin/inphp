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
namespace Inphp\Middleware;

use Inphp\Service\Middleware\ICacheMiddleware;

/**
 * 使用Redis实现缓存
 * 这里的缓存，是即时的，退出时，会全部清理
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
        return \Inphp\DB\Cache::select(2)->get($name, $default);
    }

    /**
     * 设置
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        // TODO: Implement set() method.
        \Inphp\DB\Cache::select(2)->set($name, $value);
    }

    /**
     * 移除
     * @param string $name
     */
    public function remove(string $name)
    {
        // TODO: Implement remove() method.
        \Inphp\DB\Cache::select(2)->rm($name);
    }

    /**
     * 清除
     */
    public function clean()
    {
        // TODO: Implement clean() method.
        \Inphp\DB\Cache::select(2)->clear();
    }
}