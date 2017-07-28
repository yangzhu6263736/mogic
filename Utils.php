<?php
/**
 *
 * @authors  ()
 * @date    2017-07-28 02:40:34
 * @version
 */
namespace Mogic;

class Utils
{
    /**
     * 取得当前毫秒时间
     *
     * @return void
     */
    public static function getMillisecond()
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec=round($usec*1000);
        return $msec;
    }

    public static function getip()
    {
        
    }

}
