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
        if (\count($funcs) == 0) {
            return call_user_func($func, false, $params);
        }
        call_user_func($func, $params, function ($err, $params) use ($funcs) {
            if ($err) {
                $finalFunc = array_pop($funcs);
                return call_user_func($finalFunc, $err, $params);
            }
            self::asyncCalls($funcs, $params);
        });
    }

    public static function tree($path)
    {
        $mydir = dir($path);
        $array = array();
        // echo $path."\n";
        while ($file = $mydir->read()) {
            $_file = $path."/".$file;
            if ($file == ".") {
                continue;
            }
            if ($file == "..") {
                continue;
            }
            if (is_dir($_file)) {
                $array[$file] = self::tree($_file);
            } else {
                if (\strstr($file, '.DS_Store')) {
                    continue;
                }
                $array[] = $file;
            }
        }
        $mydir->close();
        return $array;
    }

    /**
     * 取得某个API文件的详情
     *      利用反射API获取php类文件的所有接口详细信息
     * @param [type] $route
     * @return void
     */
    public static function getApiDetail($file, $class)
    {
        include_once($file);
        $detail = array();
        $reflection = new \ReflectionClass($class);
        // $doc = $reflection->getDocComment();
        include_once(MOG_PATH.'library/vendor/autoload.php');
        $classReflection = new \Nette\Reflection\ClassType($class);
        $detail['class'] = $classReflection->getAnnotations();
        $methods = $classReflection->getMethods();
        foreach ($methods as $method) {
            $name = $method->name;
            $detail['methods'][$name] = $method->getAnnotations();
        }
        return $detail;
    }
}
