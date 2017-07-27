<?php
	namespace JoyEngine;
	include(BASE_PATH."/app/MogicYaf.php");
	include(BASE_PATH."/Mogic/Session.php");
	class JoyServer{
		private static $_instance;
		private $swooleServer;
		private $application;
		public static function getInstance(){
			return JoyServer::instance;
		}

		public static function createServer($host, $port){
			if (!JoyServer::$_instance) {
				new JoyServer($host, $port);
			}
			return JoyServer::$_instance;
		}
		public function __construct($host, $port) {
			echo "worker进程启动";
			self::$_instance = $this;
			$this->initSwooleServer();
		}

		public function initSwooleServer()
		{
			$this->swooleServer = new \Swoole\Http\Server("localhost", 3736, SWOOLE_PROCESS);
			$this->swooleServer->set(array(
			    'worker_num' => 1,
			    'dispatch_mode'	=>	1,//与worker通信模式 1轮询
			    // 'task_worker_num' => 1,
			//    'task_ipc_mode' => 3,
			//    'message_queue_key' => 0x70001001,
			    //'task_tmpdir' => '/data/task/',
			));
			$this->swooleServer->on('Start', function($serv) {
			    swoole_set_process_name("php JoySwoole master Process");
			});
			$this->swooleServer->on('Request', function($request, $response) {
			 	$this->onRequest($request, $response);
			});
			$this->swooleServer->on('WorkerStart', function ($serv, $workerId){
				// echo "WorkerStart1=================\n";
				// print_r($serv);
				// echo "WorkerStart2=================\n";

				// print_r($this->swooleServer);
				var_dump($serv == $this->swooleServer);
			    if($workerId >= $serv->setting['worker_num']) {
			        swoole_set_process_name("php JoySwoole task worker");
			    } else {
			    		$this->onWorkerStart($serv, $workerId);
			        // swoole_set_process_name("php JoySwoole event worker");
			        // include("Process/WorkerProcess.php");
			        // new Process\WorkerProcess($serv, $workerId);
			    }
			});
			$this->swooleServer->on('ManagerStart', function ($serv){
			    echo "on ManagerStart\n";
			    swoole_set_process_name("phpJoySwoole Manager Porcess");
			});
			$this->swooleServer->on('WorkerStop', function($serv){
				echo "WorkerStop";
				print_r($serv);
			});
			$this->swooleServer->on('Connect', function($serv, $fd, $fromReactorId){
				echo "on Connect".$fd.$fromReactorId;
			});
			$this->swooleServer->on('Receive', function($serv, $fd, $fromReactorId, $data){
				echo "on Receive".$fd.$fromReactorId;
			});
			//udp端口回调
			$this->swooleServer->on('Packet', function($serv, $data, $client_info){
				echo "on Receive".$fd.$fromReactorId;
			});
			$this->swooleServer->on('Close', function($serv, $fd, $fromReactorId){
				echo "on Close".$fd.$fromReactorId;
			});
			$this->swooleServer->on('BufferFull', function(){

			});
			$this->swooleServer->on('BufferEmpty', function(){
				echo 'onBufferEmpty';
			});
			$this->swooleServer->on('Task', function(){
				echo 'onTask';
			});
			$this->swooleServer->on('Finish', function($serv, $task_id, $data){
				echo "onFinish";
			});
			$this->swooleServer->on('PipeMessage', function($server, $from_worker_id, $message){
				echo 'onPipeMessage';
			});
			$this->swooleServer->on('WorkerError', function($serv, $worker_id, $worker_pid, $exit_code, $signal){
				echo 'onWorkerError';
			});
			$this->swooleServer->on('ManagerStop', function($serv){
				echo 'onManagerStop';
			});

			$this->swooleServer->start();
		}

		public function initEvent(){
		
		}

		public function onRequest($request, $response){
		   $_GET['_url'] = $request->server['request_uri'];
	        if ($request->server['request_method'] == 'GET' && isset($request->get)) {
	            foreach ($request->get as $key => $value) {
	                $_GET[$key] = $value;
	                $_REQUEST[$key] = $value;
	            }
	        }
	        if ($request->server['request_method'] == 'POST' && isset($request->post) ) {
	            foreach ($request->post as $key => $value) {
	                $_POST[$key] = $value;
	                $_REQUEST[$key] = $value;
	            }
	        }
	        $session_id = empty($request->cookie['MOGIC_SESSION_ID']) ? '' : $request->cookie['MOGIC_SESSION_ID'];
	        $_SESSION = \Mogic\Session::getInstance($session_id);
	        $res = \MogicYaf::request('index', 'index');
		    $response->cookie("MOGIC_SESSION_ID", $_SESSION->get('session_id'));
		    $response->cookie("User", "Swoole");
		    $response->header("X-Server", "Swoole");
		    // $response->end("<h1>".$res."</h1>");
        		$response->end($res);
		}

		public function onWorkerStart(){
			echo "onWorkerStart";
	         swoole_set_process_name("php Mogic worker");
			// $this->application = new Application();
			// $this->application = new \Yaf\Application("conf.ini");
			\MogicYaf::createYaf();
		}
	}
?>