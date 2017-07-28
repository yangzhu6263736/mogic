<?php
namespace Mogic;

class Response
{
    public $di = array();
    public function __set($key, $val)
    {
        \Mogic\MLog::log("Response", $key, $val);
        $this->di[$key] = $val;
    }

    // public function set($key, $val)
    // {

    // }

    public function sessionStart($session)
    {
    }

    public function out()
    {
        return $this->di;
    }
}
