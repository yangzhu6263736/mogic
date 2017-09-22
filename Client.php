<?php
namespace Mogic;

// include(BASE_PATH."/Mogic/YafInterface.php");
    // include(BASE_PATH."/Mogic/Session.php");
class Client
{
    private static $_instances;
    public $fd;
    public $isSocketFd;//是否是websocket句柄
    public $userId;
    public $session;
    public static function getClient($fd)
    {
        if (empty(self::$_instances[$fd])) {
            // return false;
            new Client($fd);
        }
        return self::$_instances[$fd];
    }

    public static function getClientByUserId($userId)
    {
        $userCell = \Mogic\Memo::getInstance()->table(MEMO_TABLE_USER_FD)->GET($userId);
        if (empty($userCell)) {
            return false;
        }
        $fd = $userCell['fd'];
        if (empty(self::$_instances[$fd])) {
            return false;
        }
        return self::$_instances[$fd];
    }

    /**
     * 移除当前进程持有的client
     * 移除client并不会造成客户端断开 仅为移除当前进程的client实例
     *
     * @param [type] $fd
     * @return void
     */
    public static function removeClient($fd)
    {
        unset(self::$_instances[$fd]);
    }

    /**
     * 向指定用户推送 事件
     *
     * @param [type] $userId
     * @param [type] $data
     * @return void
     */
    public static function emitUser($userId, $eventname, $params)
    {
        $client = self::getClientByUserId($userId);
        if (!$client) {
            return false;
        }
        $client->emitClient($eventname, $params);
    }

    public function __construct($fd, $isSocketFd = true)
    {
        // echo "新的用户:".$fd."\n";
        \SeasLog::info('新的用户'.$fd);
        // print_R(self::$_instances);
        if (isset(self::$_instances[$fd])) {
            throw new Exception("已存在的client");
        }
        self::$_instances[$fd] = $this;
        $this->isSocketFd = $isSocketFd;
        $this->fd = $fd;
        $this->onConnect();
    }

    public function onConnect()
    {
        swoole_timer_after(1000, function () {
            $this->push("ClientEvent", array("a new payer enter"));
        });
    }

    public function sessionStart()
    {
    }

    public function getFd()
    {
        return $this->fd;
    }

    public function onClose()
    {
        unset(Client::$_instances[$this->fd]);
        if ($this->userId) {
            \Mogic\Memo::getInstance()->table(MEMO_TABLE_USER_FD)->DEL($this->userId);
        }
    }

    /**
     * 针客户端绑定用户 用于向指定用户推送信息
     *  存放在内存表 同组worker可跨进程共享
     * @param [type] $userId
     * @return void
     */
    public function bind($userId)
    {
        $this->userId = $userId;
        \Mogic\Memo::getInstance()->table(MEMO_TABLE_USER_FD)->SET($userId, array(
            'fd'  =>$this->fd,
            'endtime'  =>time()+3600,
        ));
    }

    public function removeSelf()
    {
        self::removeClient($this->fd);
    }

    /**
     * 绑定句柄分配依据
     *      uid  roomid
     * 用于让master进程将请求分发到不同的worker进程
     *      仅当dispatch_mode为5时可设置
     *      可用于将同一房间(同一服)的玩家请求分发到同一进程
     *
     * @param [type] $dispatchId
     * @return void
     */
    public function bindDispatch($dispatchId)
    {
        \Mogic\Server::getInstance()->swooleServer->bind($this->fd, $dispatchId);
        swoole_timer_after(1, function () {//绑定分发ID后可能造成该fd的响应不会派发到当前进程 因此移除这个client对像
            self::removeClient($this->fd);
        });
    }


    public function send($data)
    {
        if ($this->isSocketFd) {//如果是http请求 则不允许推送
            Server::getInstance()->sendToFd($this->fd, $data);
        }
        // $server->push($frame->fd, json_encode(["hello", "world"]));
    }

    public function push($eventname, $params)
    {
        $pack = \Mogic\Protocal::getPushPack($eventname, $params);
        $this->send($pack);
    }

    public function emitClient($eventname, $params)
    {
        $this->push($eventname, $params);
    }

    public function onRequest($route, $params, $callback)
    {
        \Mogic\MLog::clog("red", $route, $params);
        $request = new \Mogic\Request($route, $params, function ($err, $response) use ($callback) {
            call_user_func($callback, $err, $response);
        });
        $request->client = $this;
        \Mogic\Application::getInstance()->run($request);
    }

    public function onPackage($data)
    {
        $package = Protocal::decode($data);
        $packageType = false;
        $message = "";
        if (count($package) == 1) {
            list($packageType) = $package;
        } else {
            list($packageType, $message) = $package;
        }
        switch ($packageType) {
            case PACKAGE_TYPE_HANDSHAKE:
                break;
            case PACKAGE_TYPE_HANDSHAKE_ACK:
                break;
            case PACKAGE_TYPE_HEARTBEAT:
                // echo "client:PACKAGE_TYPE_HEARTBEAT";
                $package = Package::getPackage(PACKAGE_TYPE_HEARTBEAT);
                $pack = Protocal::encode($package);
                // var_dump($pack);
                $this->send($pack);
                break;
            case PACKAGE_TYPE_DATA:
                $this->onMessage($message);
                break;
            case PACKAGE_TYPE_KICK:
                break;
            default:
                # code...
                break;
        }
    }

    public function onMessage($message)
    {
        $messageType = false;
        $data = false;
        $addition = false;
        if (count($message) == 2) {
            list($messageType, $data) = $message;
        } else {
            list($messageType, $data, $addition) = $message;
        }
        switch ($messageType) {
            case MESSAGE_TYPE_REQUEST:
                list($route, $params) = $data;
                $this->onRequest($route, $params, function ($err, $res) use ($addition) {
                    $pack = \Mogic\Protocal::getResponsePack($err, $res, $addition);
                    $this->send($pack);
                });
                break;
            case MESSAGE_TYPE_NOTIFY:
                break;
            case MESSAGE_TYPE_RESPONSE:
                break;
            case MESSAGE_TYPE_PUSH:
                break;
            default:
                # code...
                break;
        }
    }
}
