<?php
namespace app\http\web;

use app\annotation\Method;
use Inphp\DB\DB;
use Inphp\Service\Http\Session;

/**
 * @Method("AJAX_GET")
 * Class index
 * @package app\http\web
 */
class index
{
    public function index(){
        response(1, "请求错误");
    }
}