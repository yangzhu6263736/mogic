<?php 
/**
 * 代码中直接使用 Common::test();
 * new Common()
 * 即可自动require
 */
	class A_B{
		public static function test($a, $b) 
		{
			echo $a - $b;
		}

		public function test2($a, $b){
			echo $a * $b;
		}

	}