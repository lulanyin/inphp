<?php
namespace app\modules\inphp\ws;

use Inphp\Service\Context;
use Inphp\Service\Object\Message;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class index
{
    public function index(Server $server, Frame $frame, Message $message){

        $ctx_server = Context::getServer();
        $ctx_server->push($frame->fd, "您好，我已经收到您的消息：{$message->data}");
    }
}