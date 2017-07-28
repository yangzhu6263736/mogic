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

    /**
     * 异步回调转同步
     *
     * @param [type] $funcs
     * @param [type] $params
     * @return void
     */
    public static function asyncCalls($funcs, $params)
    {
        $func = array_shift($funcs);
        if (!$func) {
            return;
        }
        call_user_func($func, $params, function ($err, $params) use ($funcs) {
            if ($err) {
                $finalFunc = array_pop($funcs);
                return call_user_func($finalFunc, $err, $params);
            }
            self::asyncCalls($funcs, $params);
        });
    }
}
