<?php
namespace app\http\web;

use Inphp\DB\DB;
use Inphp\Service\Http\Session;

class index
{
    public function index(){
        echo(__METHOD__).PHP_EOL;
        echo "times :".time().PHP_EOL;
        //赋值变量给模板使用，使用的是smarty模板引擎
        assign("version", '0.1');
        //$list = DB::from("user")->select("uids, username, phone")->get(5);
    }
}