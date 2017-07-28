<?php
namespace Mogic;

class Request
{
    public $route;
    public $module;
    public $controller;
    public $action;
    public $params;
    public $response;
    public $callback;
    public $session_id;

    public function __construct($route, $params, $callback)
    {
        if (!empty($params['session_id'])) {
            $this->session_id = $params['session_id'];
        }
        $this->route = $route;
        $this->params = $params;
        $this->callback = $callback;
        $this->response = new \Mogic\Response();
        $routes = explode('/', $route);
        list($module, $controller, $action) = $routes;
        $this->module = ucfirst($module);
        $this->controller = ucfirst($module);
        $this->action = ucfirst($action);
    }

    public function getRouter()
    {
        return $this->module.'\\Controller\\'.$this->controller;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * 请求完成 执行回调
     *      仅能在controller中完调用
     *
     * @return void
     */
    public function done($err = false, $msg = "")
    {
        if ($err) {
            call_user_func($this->callback, $err, $msg);
        } else {
            call_user_func($this->callback, false, $this->response);
        }
    }
}
