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
namespace app\cmd;

use Inphp\ICommend;

class test implements ICommend
{
    public function __construct(array $params = null)
    {

    }

    public function start()
    {
        // TODO: Implement run() method.
        echo 'test run...'.PHP_EOL;
    }
}