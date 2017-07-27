<?php
namespace Mogic;

class Memo
{
    private static $instance;
    private $tables;
    public static function getInstance()
    {
        if (!self::$instance) {
            new Memo();
        }
        return self::$instance;
    }

    public function __construct()
    {
        if (self::$instance) {
            throw new \Exception("不能重复创建");
        }
        self::$instance = $this;
        $this->tables = array();
    }

    public function create()
    {
        $configs = Config::getConfig("Memo");
        foreach ($configs as $tableName => $config) {
            $this->createTable($tableName, $config);
        }
        // print_R($configs);
    }

    public function createTable($tableName, $config)
    {
        $table = new \swoole\table($config['size']);
        foreach ($config['columns'] as $value) {
            list($colname, $type, $length) = $value;
            $table->column($colname, $type, $length);       //1,2,4,8
        }
        $table->create();
        $this->tables[$tableName] = $table;
        // $this->memtable->create();
        // $this->memtable->set(1, array(
        //     'id'=>1,
        //     'name'=>"yangzhu",
        //     'num'=>100
        //     ));
        // $user = $this->memtable->get(1);
    }

    public function test()
    {
        \Mogic\MLog::log("testset");

        $array = array(
            // MEMO_TABLE_USER_FD => array(
            //     array(
            //         1   => array(
            //             'userId'    =>  1,
            //             'fd'    =>  1,
            //             'endtime'   => time()
            //         )
            //     )
            // ),
            MEMO_TABLE_USERS => array(
                1 =>array(
                    'userId'    =>  1,
                    'userName'  =>  'yangzhu',
                    'coin'  => 1000,
                    'endtime'   =>  time() + 1000
                )
            )
        );
        foreach ($array as $tablename => $list) {
            foreach ($list as $key => $cell) {
                $this->table($tablename)->set($key, $cell);
                $out = $this->table($tablename)->get($key);
                print_r($out);
            }
        }
    }

    public function testget()
    {
        \Mogic\MLog::log("testget");
        $out = $this->table(MEMO_TABLE_USERS)->get(1);
        \Mogic\MLog::log($out);
    }

    public function table($tableName)
    {
        return $this->tables[$tableName];
    }
}
