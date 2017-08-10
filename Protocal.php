<?php
namespace Mogic;

define('PACKAGE_TYPE_HANDSHAKE', 1);
define('PACKAGE_TYPE_HANDSHAKE_ACK', 2);
define('PACKAGE_TYPE_HEARTBEAT', 3);
define('PACKAGE_TYPE_DATA', 4);
define('PACKAGE_TYPE_KICK', 5);

define('MESSAGE_TYPE_REQUEST', 0);
define('MESSAGE_TYPE_NOTIFY', 1);
define('MESSAGE_TYPE_RESPONSE', 2);
define('MESSAGE_TYPE_PUSH', 3);

class Protocal
{
    /**
     * 先采用\r\n拆包
     * swoole server 设置了
     *open_eof_check=> 1,//拆包 用
     *'PACKAGE_eof' => "\r\n", //设置EOF
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

    /**
     * 取得Push推送的数据包
     *
     * @param [type] $eventName
     * @param [type] $params
     * @return void
     */
    public static function getPushPack($eventName, $params)
    {
        $_rep = array($eventName, $params);
        $message = Message::getMessage(MESSAGE_TYPE_PUSH, $_rep, false);
        $package = Package::getPackage(PACKAGE_TYPE_DATA, $message);
        $pack = Protocal::encode($package);
        return $pack;
    }

    /**
     * 取得response数据包
     *
     * @param [type] $err 是否发生错误
     * @param [type] $res 返回内容
     * @param [type] $handleId 前端回调id
     * @return void
     */
    public static function getResponsePack($err, $res, $handleId)
    {
        $rep = array($err, $res);
        $message = Message::getMessage(MESSAGE_TYPE_RESPONSE, $rep, $handleId);
        $package = Package::getPackage(PACKAGE_TYPE_DATA, $message);
        $pack = Protocal::encode($package);
        return $pack;
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
