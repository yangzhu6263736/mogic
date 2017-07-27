<?php
namespace Mogic;

require_once('Swoole/Async/Pool.php');
require_once('Swoole/Async/MySQL.php');
class MysqlPools
{
    private static $pools;
    public static function getPool($group)
    {
        return self::pool($group);
    }

    public static function pool($group)
    {
        if (!isset(self::$pools[$group])) {
            $configs = Config::getConfig("MySQL");
            $config = $configs[$group];
            self::$pools[$group] = new \Swoole\Async\Mysql($config);
        }
        return self::$pools[$group];
    }

    public static function dropPool($group)
    {
        unset(self::$pools[$group]);
    }
}
