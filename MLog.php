<?php
namespace Mogic;

class MLog
{
    public static $workerId = 0;
    public static $processId = 0;

    public static function getEnvParams()
    {
        // self::$workerId = \Mogic\Server::getInstance().
    }

    public static function log(...$args)
    {
        $msg = self::merge($args);
        \SeasLog::info($msg);
        echo $msg."\n";
    }

    // public static function info(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }

    // public static function debug(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }

    // public static function notice(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }

    // public static function warning(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }
    // public static function error(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }

    // public static function critical(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }

    // public static function alert(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }

    // public static function emergency(...$args){
    // 	$msg = self::merge($args);
    // 	SeasLog::log($msg);
    // }


    private static function merge(...$args)
    {
        foreach ($args as &$value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
        }
        $pid = posix_getpid();
        return $pid.":".implode(" ", $args);
    }

    // SeasLog::log(SEASLOG_ERROR,'this is a error test by ::log');
    // SeasLog::debug('this is a {userName} debug',array('{userName}' => 'neeke'));

    // SeasLog::info('this is a info log');

    // SeasLog::notice('this is a notice log');

    // SeasLog::warning('your {website} was down,please {action} it ASAP!',array('{website}' => 'github.com','{action}' => 'rboot'));

    // SeasLog::error('a error log');

    // SeasLog::critical('some thing was critical');

    // SeasLog::alert('yes this is a {messageName}',array('{messageName}' => 'alertMSG'));

    // SeasLog::emergency('Just now, the house next door was completely burnt out! {note}',array('{note}' => 'it`s a joke'));
}
