<?php
namespace Mogic;

define('APPLICATION_PATH', dirname(__FILE__).'/../YafApp/');
// include(BASE_PATH."/Mogic/DataLeaker.php");
class YafInterface
{
    private static $yafApp;
    public static function createYaf()
    {
        self::$yafApp = new \Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
        self::$yafApp->bootstrap();
    }

    public static function request($module, $controller, $action, $params, $client)
    {
        \Yaf_Registry::set('client', $client);
        $res = false;
        try {
            \Mogic\DataLeaker::clear();
            $params['callback'] = function ($res) {
                echo "ffffffuckkkkk";
				echo $res;
            };
            $request = new \Yaf_Request_Simple("index", $module, $controller, $action, $params);
            $dispatcher = self::$yafApp->getDispatcher();
            $dispatcher->returnResponse(true);
            $dispatcher->disableView();
            $response = self::$yafApp->getDispatcher()->dispatch($request);
            // $res = $response->getBody();
            $res = \Mogic\DataLeaker::getOutput();
            $res = json_encode($res);
        } catch (Exception $e) {
            print_r($e);
        }
        \Yaf_Registry::del('client');
        return $res;
    }
}
