<?php
/**
 * MOG的session类
 *  数据存储交由redis
 *
 */
namespace Mogic;

class Session
{
    private static $_instances;

    public $session_id = '';
    private $updateTime;
    private $expireTime;
    private $di;//数据容器

    /**
     * 创建一个sessionId
     * 根据进程id 毫秒时间 和随机数 生成防止重复
     *
     * @return void
     */
    public static function createSessionId()
    {
        $array = array(
            \Mogic\Server::getInstance()->GID,//当前进程的集群服务号
            \Mogic\Utils::getMillisecond(),
            rand(100000, 99999)
        );
        return implode('-', $array);
    }

    public static function start()
    {
        $session_id = self::createSessionId();//模拟数据
        return self::getSession($session_id);
    }

    public static function getSession($session_id = '')
    {
        if (empty(self::$_instances[$session_id])) {
            new Session($session_id);
        }
        self::$_instances[$session_id]->expire();
        return self::$_instances[$session_id];
    }

    public function __construct($session_id)
    {
        $this->session_id = $session_id;
        self::$_instances[$session_id] = $this;
        $this->expire();
    }

    /**
     * 是否是本地创建的session
     * 通过GID判断 如果
     *
     * @return boolean
     */
    public function isLocal()
    {
        $GID = \Mogic\Server::getInstance()->GID;
        list($_GID, $mtime, $rand) = \explode('-', $session_id);
        return $GID !== $_GID;
    }

    /**
     * 更新过期时间
     *
     * @return void
     */
    public function expire()
    {
        if (time() - $this->updateTime < 120) {//防止频繁更新过期时间
            return;
        }
        $this->updateTime = time();
        $this->expireTime = time() + 1800;
        RedisPools::pool(REDIS_GROUP_SESSION)->expire($this->session_id, 1800, \json_encode($this->di), function (\swoole_redis $client, $result) {
        });
    }
    
    /**
     * 将数据推送到远端
     *
     * @return void
     */
    public function push()
    {
        MLog::log("session push");
        RedisPools::pool(REDIS_GROUP_SESSION)->set($this->session_id, \json_encode($this->di), function (\swoole_redis $client, $result) {
        });
    }

    /**
     * 从远端恢复数据
     *
     * @return void
     */
    public function fetch($next)
    {
        // if ($this->isLocal) {//如果是本地session
        //     return call_user_func($next, $this);
        // }
        MLog::log("session fetch");
        RedisPools::pool(REDIS_GROUP_SESSION)->get($this->session_id, function (\swoole_redis $client, $result) use ($next) {
            \Mogic\MLog::clog("red", "fetch back", $result);
            if (!is_null($result)) {
                $this->di = \json_decode($result, true);
                if ($next) {
                    call_user_func($next, false, $this);
                }
            } else {
                if ($next) {
                    call_user_func($next, ERROR_USER_NOT_LOGIN);
                }
            }
        });
    }

    public function __get($name)
    {
        return isset($this->di[$name]) ? $this->di[$name] : false;
    }

    public function __set($name, $value)
    {
        echo "session: __set, $name, $value\n";
        $this->di[$name] = $value;
    }

    public function del($key)
    {
        unset($this->di[$name]);
    }

    public function clear()
    {
        unset(self::$_instances[$this->session_id]);
    }

    /**
     * 持久化到redis中
     * @return [type] [description]
     */
    public function keep()
    {
    }
}
