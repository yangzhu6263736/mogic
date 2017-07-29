<?php
namespace Mogic;

// include(BASE_PATH."/Mogic/YafInterface.php");
    // include(BASE_PATH."/Mogic/Session.php");
class Client
{
    private static $_instances;
    public $fd;
    public $userId;
    public $session;
    public static function getClient($fd)
    {
        if (empty(self::$_instances[$fd])) {
            return false;
        }
        return self::$_instances[$fd];
    }

    public static function getClientByUserId($userId)
    {
        $fd = \Mogic\Memo::getInstance()->table(MEMO_TABLE_USER_FD)->GET($userId);
        if (!$fd) {
            return false;
        }
        if (empty(self::$_instances[$fd])) {
            return false;
        }
        return self::$_instances[$fd];
    }

    public function __construct($fd)
    {
        echo "新的用户:".$fd."\n";
        \SeasLog::info('新的用户'.$fd);
        self::$_instances[$fd] = $this;
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

    public function bind($userId)
    {
        $this->userId = $userId;
        \Mogic\Memo::getInstance()->table(MEMO_TABLE_USER_FD)->SET($userId, array(
            'fd'  =>$this->fd,
            'endtime'  =>time()+3600,
        ));
    }

    public function send($data)
    {
        Server::getInstance()->sendToFd($this->fd, $data);
        // $server->push($frame->fd, json_encode(["hello", "world"]));
    }

    public function push($eventname, $params)
    {
        $_rep = array($eventname, $params);
        $message = Message::getMessage(Message_TYPE_PUSH, $_rep, false);
        $package = Package::getPackage(Package_TYPE_DATA, $message);
        $pack = Protocal::encode($package);
        var_dump($pack);
        $this->send($pack);
    }

    public function onRequest($route, $params, $callback)
    {
        \Mogic\MLog::log($route, $params);
        // $err = false;
        // $res = array("fuck response");
        // $routes = explode('/', $route);
        // list($module, $controller, $action) = $routes;
        // $res = YafInterface::request($module, $controller, $action, $params, $this);
        // call_user_func($callback, $err, $res);
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
            case Package_TYPE_HANDSHAKE:
                break;
            case Package_TYPE_HANDSHAKE_ACK:
                break;
            case Package_TYPE_HEARTBEAT:
                // echo "client:Package_TYPE_HEARTBEAT";
                $package = Package::getPackage(Package_TYPE_HEARTBEAT);
                $pack = Protocal::encode($package);
                // var_dump($pack);
                $this->send($pack);
                break;
            case Package_TYPE_DATA:
                $this->onMessage($message);
                break;
            case Package_TYPE_KICK:
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
            case Message_TYPE_REQUEST:
                list($route, $params) = $data;
                $this->onRequest($route, $params, function ($_err, $_rep) use ($addition) {
                    $rep = array($_err, $_rep);
                    $message = Message::getMessage(Message_TYPE_RESPONSE, $rep, $addition);
                    $package = Package::getPackage(Package_TYPE_DATA, $message);
                    $pack = Protocal::encode($package);
                    var_dump($pack);
                    $this->send($pack);
                });
                break;
            case Message_TYPE_NOTIFY:
                break;
            case Message_TYPE_RESPONSE:
                break;
            case Message_TYPE_PUSH:
                break;
            default:
                # code...
                break;
        }
    }
}
