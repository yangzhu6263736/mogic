<?php
/**
 * REDIS连接池配置
 */
$config['Redis'] = array(
    REDIS_GROUP_MOG   =>  array(
        'host'  =>  '127.0.0.1',
        'port'  =>  6379,
        'dbid'  => 10,
        'poolSize'  =>2//连接池最大实例数
    ),
    REDIS_GROUP_SESSION  =>  array(
        'host'  =>  '127.0.0.1',
        'port'  =>  6379,
        'dbid'  => 0,
        'poolSize'  =>2//连接池最大实例数
    )
);
