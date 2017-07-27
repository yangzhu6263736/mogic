<?php
///opt/lnmp/soft/php-protobuf-php7/protoc-gen-php.php 

function my_autoloader($class) {
	if (strstr($class, 'Google')) {
		// throw new Exception("Error Processing Request");
		return;
	}
	$class = "out/".str_replace("\\", '/', $class);
	echo $class."\n";

    include $class.'.php';
}
spl_autoload_register('my_autoloader');
$demo = GPBMetadata\Proto\Demo::initOnce();
print_R($demo);
