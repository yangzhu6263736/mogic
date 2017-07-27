<?php
	namespace Mogic;
	class Session{
		private static $_instances;

		public $session_id = '';

		public static function getInstance($session_id = ''){
			if (empty($session_id)) {//如果没有sessionID则从相应对像中取出
				$session_id = time();//模拟数据
			}
			if (empty(self::$_instances[$session_id])) {
				new Session($session_id);
			}
			return self::$_instances[$session_id];
			echo "xxxxxxsasdfasdf";
		}

		public function __construct($session_id) {
			$this->session_id = $session_id;
			self::$_instances[$session_id] = $this;
		}

		public function set($key, $value){
			$this->$key = $value;
		}

		public function get($key) {
			return $this->$key;
		}

		public function del($key){
			unset($this->$key);
		}

		public function clear(){
			unset(self::$_instances[$this->session_id]);
		}

		/**
		 * 持久化到redis中
		 * @return [type] [description]
		 */
		public function keep(){

		}
	}
?>