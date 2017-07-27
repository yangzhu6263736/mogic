<?php
    /**
     * 数据输出漏斗
     * 所有输出数据将通过此类
     */
    namespace Mogic;

class DataLeaker
{
    private $content;

    public function clear()
    {
        $this->content = array();
    }

    public function set($key, $val)
    {
        $this->content[$key] = $val;
    }

    public function pushTo($key, $val)
    {
        if (empty($this->content[$key])) {
            $this->content[$key] = array();
        }
        $this->content[$key].push($val);
    }

    public function getOutput()
    {
        return $this->content;
    }
}
