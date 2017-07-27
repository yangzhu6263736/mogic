<?php
namespace Mogic;

class Application
{
    private static $instance;
    public static function getInstance()
    {
        if (!self::$instance) {
            new Application();
        }
        return Application::$instance;
    }

    public function __construct()
    {
        Application::$instance = $this;
        $this->autoload();
    }

    /**
     * 根据环境变量 和服务器角色 执行启进进程
     *
     * @return void
     */
    public function start()
    {
    }

    public function autoload()
    {
        spl_autoload_register(function ($className) {
            \Mogic\MLog::log("spl_autoload_register", $className);
            if (strstr($className, 'Controller')) {
                $array = explode("\\", $className);
                $file = APP_PATH.'Module/'.$array[0].'/Controller/'.$array[2].'.php';
                if (is_file($file)) {
                    include_once($file);
                }
            }
        });

        spl_autoload_register(function ($className) {
             \Mogic\MLog::log("spl_autoload_register2", $className);
            if (strstr($className, 'Model')) {
                $array = explode("\\", $className);
                $file = APP_PATH.'Model/'.$array[1].'.php';
                if (is_file($file)) {
                    include_once($file);
                }
            }
        });

        spl_autoload_register(function ($className) {
             \Mogic\MLog::log("spl_autoload_register3", $className);
            if (strstr($className, 'Service')) {
                $array = explode("\\", $className);
                $file = APP_PATH.'Service/'.$array[1].'.php';
                if (is_file($file)) {
                    include_once($file);
                }
            }
        });
    }

    public function run($request)
    {
        $module = 'Hall';
        $controller = 'User';
        $action = 'test';
        $route = "Hall/User/test";
        $_route = "Hall\Controller\Hall";
        // include_once(APP_PATH.'Module/Hall/Controller/User.php');
        \Mogic\MLog::log("application start 1");
        // print_R($request);
        $err = false;
        try {
            call_user_func_array(array($request->getRouter(), $request->action), array($request));
        } catch (Exception $e) {
            print_r($e);
            $err = true;
            $request->done($err, $e);
        }
        \Mogic\MLog::log("application start 2");
    }
}
