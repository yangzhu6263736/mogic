<?php
namespace Mogic;

class Config
{
    public static $config = array();
    public static function getConfig($name)
    {
        if (empty(self::$config[$name])) {
            include_once("Config/".$name.'.php');
            self::$config += $config;
        }
        return self::$config[$name];
    }
}
