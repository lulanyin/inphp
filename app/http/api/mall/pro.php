<?php
namespace app\http\api\mall;

class pro
{
    public function index(){
        return [
            "pid"   => 1,
            "name"  => "iPhone 12 mini"
        ];
    }

    public function list(){
        return [
            [
                "pid"   => 1,
                "name"  => "iPhone 12 mini"
            ],
            [
                "pid"   => 2,
                "name"  => "iPhone 12"
            ]
        ];
    }
}