<?php
namespace Mogic;

define('Package_TYPE_HANDSHAKE', 1);
define('Package_TYPE_HANDSHAKE_ACK', 2);
define('Package_TYPE_HEARTBEAT', 3);
define('Package_TYPE_DATA', 4);
define('Package_TYPE_KICK', 5);

define('Message_TYPE_REQUEST', 0);
define('Message_TYPE_NOTIFY', 1);
define('Message_TYPE_RESPONSE', 2);
define('Message_TYPE_PUSH', 3);

class Protocal
{
    /**
     * 先采用\r\n拆包
     * swoole server 设置了
     *open_eof_check=> 1,//拆包 用
     *'package_eof' => "\r\n", //设置EOF
    * 会自动用\r\n拆包 但是如果同时传送多个包还需要自已再拆分
    * @param  [type] $message  [description]
    * @param  [type] $callback [description]
    * @return [type]           [description]
    */
    public static function unpack($message, $callback)
    {
        $array = explode("\r\n", $message);
        foreach ($array as $value) {
            call_user_func($callback, $value);
        }
    }

    public static function decode($data)
    {
        return json_decode($data, true);
    }

    public static function encode($data)
    {
        return json_encode($data)."\r\n";
    }
}

class Package
{
    /**
     * 通信包格式
     * @param  {[type]} type    [description]
     * @param  {[type]} message [description]
     * @return {[type]}         [description]
     */
    public static function getPackage($type, $message = false)
    {
        if (empty($message)) {
            return array($type);
        }
        return array($type, $message);
    }
}

class Message
{
    /**
     * 数据内容格式
     * @param  {[type]} type     [description]
     * @param  {[type]} data     [description]
     * @param  {[type]} addition [description]
     * @return {[type]}          [description]
     */
    public static function getMessage($type, $data, $addition)
    {
        if (empty($addition)) {
            return array($type, $data);
        }
        return array($type, $data, $addition);
    }
}
