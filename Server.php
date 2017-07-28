<?php
namespace Mogic;

// include(BASE_PATH."/Mogic/YafInterface.php");
// include(BASE_PATH."/Mogic/Session.php");
class Server
{
    private static $_instance;
    private $swooleServer;
    private $application;
    private $redis;
    public $memtable;
    public $GID;//group process id集群中每个进程一个单独的id
    public static function getInstance()
    {
        return Server::$_instance;
    }

    public static function createServer($host, $port)
    {
        if (!Server::$_instance) {
            new Server($host, $port);
        }
        return Server::$_instance;
    }

    public function __construct($host, $port)
    {
        echo "worker进程启动";
        self::$_instance = $this;
        $this->creatTable();
        $this->initSwooleServer();
    }

    public function creatTable()
    {
        // $this->memtable = new \swoole\table(1024);
        // $this->memtable->column('id', \swoole\table::TYPE_INT, 4);       //1,2,4,8
        // $this->memtable->column('name', \swoole\table::TYPE_STRING, 64);
        // $this->memtable->column('num', \swoole\table::TYPE_FLOAT);
        // $this->memtable->create();
        // $this->memtable->set(1, array(
        //     'id'=>1,
        //     'name'=>"yangzhu",
        //     'num'=>100
        //     ));
        // $user = $this->memtable->get(1);
        // print_R($user);
        \Mogic\Memo::getInstance()->create();
        \Mogic\Memo::getInstance()->test();
        \Mogic\Memo::getInstance()->table(MEMO_TABLE_USERS)->SET(100, array(
            'userId'=>1,
            'name'  =>'yangzhu',
            'coin'  =>100,
        ));
    }

    public function sendToFd($fd, $data)
    {
        echo "Server pushToFd $fd $data";
        $this->swooleServer->push($fd, $data);
    }

    public function sendToUser($userId, $data)
    {
        $client = Client::getClientByUserId($userId);
        if (!$clientId) {
            return false;
        }
        $this->pushToFd($client->getFd(), $data);
    }

    public function initSwooleServer()
    {
        // $this->swooleServer = new \Swoole\Http\Server("localhost", 3736, SWOOLE_PROCESS);
        $this->swooleServer = new \Swoole\Websocket\Server("localhost", 3736, SWOOLE_PROCESS);
        $this->swooleServer->set(array(
            'worker_num' => 1,
            'dispatch_mode'    =>    4,//与worker通信模式 1轮询 2 描述符固定 3 抢占 4 IP分配 5 UID分配
            'heartbeat_check_interval' => 10,
                'heartbeat_idle_time' => 15,
                'open_eof_check'    => 1,//拆包 用
                'package_eof' => "\r\n", //设置EOF
            // 'task_worker_num' => 1,
        //    'task_ipc_mode' => 3,
        //    'message_queue_key' => 0x70001001,
            //'task_tmpdir' => '/data/task/',
        ));
        $this->swooleServer->on('Start', function ($serv) {
            swoole_set_process_name("php JoySwoole master Process");
        });
        $this->swooleServer->on('Request', function ($request, $response) {
            $this->onRequest($request, $response);
        });
        $this->swooleServer->on('WorkerStart', function ($serv, $workerId) {
            // echo "WorkerStart1=================\n";
            // print_r($serv);
            // echo "WorkerStart2=================\n";

            // print_r($this->swooleServer);
            $this->fetchGid();
            var_dump($serv == $this->swooleServer);
            if ($workerId >= $serv->setting['worker_num']) {
                swoole_set_process_name("php JoySwoole task worker");
            } else {
                $this->onWorkerStart($serv, $workerId);
                // swoole_set_process_name("php JoySwoole event worker");
                // include("Process/WorkerProcess.php");
                // new Process\WorkerProcess($serv, $workerId);
            }
        });
        $this->swooleServer->on('ManagerStart', function ($serv) {
            \Mogic\MLog::log("on ManagerStart");
            swoole_set_process_name("phpJoySwoole Manager Porcess");
        });
        $this->swooleServer->on('WorkerStop', function ($serv) {
            echo "WorkerStop";
            print_r($serv);
        });
        $this->swooleServer->on('Open', function ($server, $req) {
            // print_r($server);
            // print_r($req);
            echo "Server:open  $server->worker_pid;\n";
            new Client($req->fd);
            // echo $server->worker_pid;

            echo "connection open server: ".$server->worker_pid.'-req:'.$req->fd;
        });
        $this->swooleServer->on('Message', array($this, '_onMessage'));
        // $this->swooleServer->on('Message', function($server, $frame) {
        //     echo "message: ".$frame->data;
        //     $server->push($frame->fd, json_encode(["hello", "world"]));
        // });

        $this->swooleServer->on('Connect', function ($serv, $fd, $fromReactorId) {
            echo "on Connect".$fd.$fromReactorId;
        });
        $this->swooleServer->on('Receive', function ($serv, $fd, $fromReactorId, $data) {
            echo "on Receive".$fd.$fromReactorId;
        });
        //udp端口回调
        $this->swooleServer->on('Packet', function ($serv, $data, $client_info) {
            echo "on Receive".$fd.$fromReactorId;
        });
        $this->swooleServer->on('Close', function ($serv, $fd, $fromReactorId) {
            echo "on Close".$fd.$fromReactorId;
        });
        $this->swooleServer->on('BufferFull', function () {
        });
        $this->swooleServer->on('BufferEmpty', function () {
            echo 'onBufferEmpty';
        });
        $this->swooleServer->on('Task', function () {
            echo 'onTask';
        });
        $this->swooleServer->on('Finish', function ($serv, $task_id, $data) {
            echo "onFinish";
        });
        $this->swooleServer->on('PipeMessage', function ($server, $from_worker_id, $message) {
            echo 'onPipeMessage';
        });
        $this->swooleServer->on('WorkerError', function ($serv, $worker_id, $worker_pid, $exit_code, $signal) {
            echo 'onWorkerError';
        });
        $this->swooleServer->on('ManagerStop', function ($serv) {
            echo 'onManagerStop';
        });

        $this->swooleServer->start();
    }

    public function initEvent()
    {
    }

    public function onRequest($request, $response)
    {
        $_GET['_url'] = $request->server['request_uri'];
        if ($request->server['request_method'] == 'GET' && isset($request->get)) {
            foreach ($request->get as $key => $value) {
                $_GET[$key] = $value;
                $_REQUEST[$key] = $value;
            }
        }
        if ($request->server['request_method'] == 'POST' && isset($request->post)) {
            foreach ($request->post as $key => $value) {
                $_POST[$key] = $value;
                $_REQUEST[$key] = $value;
            }
        }
        $session_id = empty($request->cookie['MOGIC_SESSION_ID']) ? '' : $request->cookie['MOGIC_SESSION_ID'];
        $_SESSION = \Mogic\Session::getInstance($session_id);
        $res = \Mogic\YafInterface::request('index', 'index');
        $response->cookie("MOGIC_SESSION_ID", $_SESSION->get('session_id'));
        $response->cookie("User", "Swoole");
        $response->header("X-Server", "Swoole");
        // $response->end("<h1>".$res."</h1>");
        $response->end($res);
    }

    public function _onMessage($server, $frame)
    {
        // print_r($frame);
        echo "_onMessage:  $server->worker_pid\n";
        echo "Server:onMessage:$frame->fd\n";
        $fd = $frame->fd;
        $client = Client::getClient($fd);
        Protocal::unpack($frame->data, function ($package) use ($client) {
            $client->onPackage($package);
        });
    }

    /**
     * 进程获取集群服务id
     * 同步阻塞的从redis中获取
     *
     * @return void
     */
    public function fetchGid()
    {
        $configs = Config::getConfig('Redis');
        $config = $configs[REDIS_GROUP_MOG];
        $redis = new \Redis();
        $redis->connect($config['host'], $config['port']);
        $redis->select($config['dbid']);
        $this->GID = $redis->incr('MOG_GRUOP_PROCESS_ID', 1);
    }
    

    public function onWorkerStart()
    {
        \Mogic\MLog::log("on onWorkerStart");
        swoole_set_process_name("php Mogic worker");
   
        RedisPools::pool(REDIS_GROUP_MOG)->set('key', "fuck", function (\swoole_redis $client, $result) {
            // \Mogic\MLog::log("xxxx", $result);
            $client->get('key', function (\swoole_redis $client, $result) {
                // \Mogic\MLog::log("xxxx2", $result);
            });
        });
        $sql = "SELECT * FROM tutorials_tbl";
        MysqlPools::pool(MYSQL_GROUP_HALL)->query($sql, function ($db, $result) {
            // \Mogic\MLog::log("db query back", $result);
        });
        // \Mogic\YafInterface::createYaf();
        $app = \Mogic\Application::getInstance();
        \Mogic\Memo::getInstance()->testget();
        // $app->run();
    }
}
