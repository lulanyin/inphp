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
namespace Inphp;
/**
 * 用于PHP CLI运行的接口类，实现它即可
 * Interface ICommend
 * @package Inphp
 */
interface ICommend{

    /**
     * 初始化
     * ICommend constructor.
     * @param array|null $params
     */
    public function __construct(array $params = null);

    /**
     * 执行命令
     * @return mixed
     */
    public function start();
}