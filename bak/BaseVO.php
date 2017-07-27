<?php
namespace Mogic;

// include(BASE_PATH."/Mogic/YafInterface.php");
// include(BASE_PATH."/Mogic/Session.php");
class BaseVO
{

    public function __construct($host, $port)
    {
        echo "worker进程启动";
        self::$_instance = $this;
        $this->initSwooleServer();
    }

}