<?php 
/**
 * 代码中直接使用 Common::test();
 * new Common()
 * 即可自动require
 */
	class Common{
		public static function test($a, $b) 
		{
			echo $a + $b;
		}

	}