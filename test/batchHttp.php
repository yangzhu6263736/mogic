<?php
$start = 0;
$final = 0;

function testHttp()
{
    $st = getMillisecond();
    $cli = new swoole_http_client('60.191.205.121', 3736);
    $cli->setHeaders([
        'Host' => '60.191.205.121',
        "User-Agent" => 'Chrome/49.0.2587.3',
        'Accept' => 'text/html,application/xhtml+xml,application/xml',
        'Accept-Encoding' => 'gzip',
    ]);
    $cli->get('/hall/hall/enter', function ($cli) use ($st) {
        $et = getMillisecond();
        $lt = $et - $st;
        echo "Length: " . strlen($cli->body) ." -- ".$lt. "\n";
        // echo $cli->body;
    });
}

//每秒执行1000次请求并发
swoole_timer_tick(1000, function () {
    $st = getMillisecond();
    for ($i =1; $i<100; $i++) {
        testHttp();
    }
    $et = getMillisecond();
    echo $et - $st."\n";
});

function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1)+floatval($t2))*1000);
}
