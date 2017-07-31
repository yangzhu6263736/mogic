<?php
namespace Mogic;

require_once("library/Colors.php");
define('COLOR_RED', 'red');
define('COLOR_PURPLE', 'purple');
define('COLOR_BLUE', 'blue');
define('COLOR_YELLOW', 'yellow');
define('COLOR_GRAY', 'light_gray');
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

    /**
     * 输出带颜色的文字 便于调试
     *
     * @param [type] $color 
     * @param [type] ...$args
     * @return void
     */
    public static function clog($color, ...$args)
    {
        $msg = self::merge($args);
        \SeasLog::info($msg);
        $colors = new \Wujunze\Colors();
        echo $colors->getColoredString($msg, $color).PHP_EOL;
    }

    public static function cwarn($color, ...$args)
    {
        self::clog($color, $args);
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
