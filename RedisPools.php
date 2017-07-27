<?php
namespace Mogic;

require_once('Swoole/Async/Pool.php');
require_once('Swoole/Async/Redis.php');
class RedisPools
{
    private static $pools;
    public static function getPool($group)
    {
        return self::pool($group);
    }

    public static function pool($group)
    {
        if (!isset(self::$pools[$group])) {
            $configs = Config::getConfig("Redis");
            $config = $configs[$group];
            self::$pools[$group] = new \Swoole\Async\Redis($config);
        }
        return self::$pools[$group];
    }

    public static function dropPool($group)
    {
        unset(self::$pools[$group]);
    }
}
