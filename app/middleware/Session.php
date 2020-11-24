<?php
namespace app\middleware;

class Session implements ISessionMiddleWare
{
    private $session_id = null;

    public function __construct(string $session_id)
    {
        $this->session_id = $session_id;
    }

    public function get(string $name, $default = null){

    }

    public function set(string $name, $value){

    }

    public function drop(string $name){

    }
}