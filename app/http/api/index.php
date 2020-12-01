<?php
namespace app\http\api;

use Inphp\DB\DB;

class index
{
    public function index(){
        return DB::from('user')->where("uid", ">", 5)->select("uid, username, phone")->get(10);
    }

    public function list(){
        return "api/list";
    }
}