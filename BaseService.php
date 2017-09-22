<?php
/**
 * base manage object
 *  基本管理对像
 *      单例模式
 *      长驻内存
 */
namespace Mogic;

// include(BASE_PATH."/Mogic/YafInterface.php");
// include(BASE_PATH."/Mogic/Session.php");
class BaseService
{
    public static $instances;
    private $di = array();//数据容器

    public static function getInstance()
    {
        $cName = get_called_class();
        \Mogic\MLog::clog("blue", $cName);
        if (empty(self::$instances[$cName])) {
            new $cName;
        }
        return self::$instances[$cName];
    }

    public function __construct()
    {
        $cName = get_called_class();
        if (isset(self::$instances[$cName])) {
            throw new \Exception("单例模式已存在, 不能重复创建。使用 xxxMO::getInstance()获取");
        }
        self::$instances[$cName] = $this;
        // $cName::$instance = $this;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function get($key)
    {
        return $this->$key;
    }
}
