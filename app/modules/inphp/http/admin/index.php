<?php
/**
 * Create By Hunter
 * 2020/12/2 10:08 上午
 *
 */
namespace app\modules\inphp\http\admin;

class index
{
    public function index(){
        assign("player", "me");

        \SmartyTags::add("myTag", function ($params = []){
            return "name = ".($params['name'] ?? 'not set');
        });
    }

    public function list(){
        echo "admin/list";
    }
}