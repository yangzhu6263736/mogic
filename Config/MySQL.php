<?php
/**
 * REDIS连接池配置
 */
$config['MySQL'] = array(
    MYSQL_GROUP_HALL   =>  array(
        'host'  =>  '127.0.0.1',
        'port'  =>  3306,
        'user'  =>  'root',
        'password'  =>  'www.mmgame.net',
        'database'  => 'mogtest',
        'charset'   =>  'utf8',
        'timeout'   =>  2,
        'poolSize'  => 2//连接池最大实例数
    )
);
