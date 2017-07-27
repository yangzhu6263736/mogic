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
    public static $instance;
    private $di = array();//数据容器

    public static function getInstance()
    {
        $cName = get_called_class();
        if (!$cName::$instance) {
            new $cName;
        }
        return $cName::$instance;
    }

    public function __construct()
    {
        $cName = get_called_class();
        if ($cName::$instance) {
            throw new \Exception("单例模式已存在, 不能重复创建。使用 xxxMO::getInstance()获取");
        }
        $cName::$instance = $this;
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
