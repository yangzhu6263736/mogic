<?php
$data = array(
    "xxxx1"  =>  123,
);

function testAPCU($data){
    
    apcu_clear_cache();
    apcu_store("fuck", $data);
    $nd = apcu_fetch("fuck");
    print_R($nd);

    $st = getMillisecond();
        // apcu_store("xxx", "aaaa");
    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        apcu_store($key, $data);
    }
    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        $_d = apcu_fetch($key);
    }
    $et = getMillisecond();
    $lt = $et - $st;
    echo "acpu_store settime:".$lt."ms\n";
    // print_R(apcu_sma_info())."\n";
}

testAPCU($data);
function testOBJ($data)
{
    $mem = array();
    $st = getMillisecond();

    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        // $mem[$key] = json_encode($data);
        $mem[$key] = $data;
    }
    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        $_d = $mem[$key];
    }
    $et = getMillisecond();
    $lt = $et - $st;
    echo "obj settime:".$lt."ms\n";
}
testOBJ($data);

function testSwooleTable($data)
{
    $mem = array();
    $st = getMillisecond();
    $table = new swoole_table(1024000);
    $table->create();
    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        // $mem = json_encode($data);
        $table->set($key, $data);
    }
    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        // $mem = json_encode($data);
        $_d = $table->get($key);
    }
    $et = getMillisecond();
    $lt = $et - $st;
    echo "swoole table settime:".$lt."ms\n";
}
testSwooleTable($data);


function testRedis($data)
{
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $st = getMillisecond();
    $table = new swoole_table(1024000);
    $table->create();
    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        // $mem = json_encode($data);
        $redis->set($key, json_encode($data));
    }
    for ($i = 1; $i<100000; $i++) {
        $key = 'test'.$i;
        // $mem = json_encode($data);
        $_d = json_decode($redis->get($key), true);
    }
    $et = getMillisecond();
    $lt = $et - $st;
    echo "redis settime:".$lt."ms\n";
}
testRedis($data);

function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1)+floatval($t2))*1000);
}
