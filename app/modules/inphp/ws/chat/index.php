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
namespace app\modules\inphp\ws\chat;

use Inphp\Service\Object\Message;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * 聊天测试
 * Class index
 * @package app\modules\inphp\ws\chat
 */
class index
{
    /**
     * 默认订阅入口
     * @param Server $server
     * @param Frame $frame
     * @param Message $message
     */
    public function index(Server $server, Frame $frame, Message $message){

        //立即回复消息
        $server->push($frame->fd, "我已经收到您的消费：{$message->data}");

    }
}