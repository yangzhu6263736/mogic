<?php
namespace Swoole\Async;

use Swoole;

class Redis extends Pool
{
    const DEFAULT_PORT = 6379;

    public function __construct($config = array(), $poolSize = 1)
    {
        if (empty($config['host'])) {
            throw new \Exception("require redis host");
        }
        if (empty($config['port'])) {
            $config = self::DEFAULT_PORT;
        }
        $poolSize = empty($config['poolSize']) ? $poolSize : $config['poolSize'];
        parent::__construct($config, $poolSize);
        $this->create(array($this, 'connect'));
    }

    protected function connect()
    {
        $redis = new \swoole_redis();

        $redis->on('close', function ($redis) {
            $this->remove($redis);
        });

        return $redis->connect($this->config['host'], $this->config['port'], function ($redis, $result) {
            if ($result === false) {
                $this->failure();
                trigger_error("connect to redis server[{$this->config['host']}:{$this->config['port']}] failed. Error: {$redis->errMsg}[{$redis->errCode}].");
                return;
            }
            $redis->select($this->config['dbid'], function ($redis, $result) {
                if ($result === false) {
                    $this->failure();
                    trigger_error("connect to redis server[{$this->config['host']}:{$this->config['port']}] failed. Error: {$redis->errMsg}[{$redis->errCode}].");
                    return;
                }
                $this->join($redis);
            });
        });
    }

    public function __call($call, $params)
    {
        return $this->request(function (\swoole_redis $redis) use ($call, $params) {
            call_user_func_array(array($redis, $call), $params);
            //必须要释放资源，否则无法被其他重复利用
            $this->release($redis);
        });
    }
}
