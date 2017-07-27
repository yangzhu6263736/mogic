<?php
	namespace JoyEngine\Process;
	class WorkerProcess{
		private $serv;
		public function __construct($serv, $workerId) {
			echo "worker进程启动";
			$this->serv = $serv;
	        	swoole_set_process_name("php JoySwoole Worker ".$workerId);
	        	$this->initEvent();
		}

		public function initEvent(){
			echo "initEvent";
			// $this->serv->on('Request', function($request, $response) {
			//     var_dump($request->get);
			//     var_dump($request->post);
			//     var_dump($request->cookie);
			//     var_dump($request->files);
			//     var_dump($request->header);
			//     var_dump($request->server);

			//     $response->cookie("User", "Swoole");
			//     $response->header("X-Server", "Swoole");
			//     $response->end("<h1>Hello Swoole!</h1>");
			// });
		}
	}
?>