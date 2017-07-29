<?php
/**
 * memo内存表结构配置
 *      key => tablename
 *      @deprecated 数据类型 \swoole\table::TYPE_INT \swoole\table::TYPE_STRING  字节
 *      @see https://wiki.swoole.com/wiki/page/p-table.html
 */
$config['Memo'] = array(
    MEMO_TABLE_USER_FD    =>  array(
        'size'  =>  262144,
        'columns'    =>  array(
            array('fd', \swoole\table::TYPE_INT, 10),
            array('endtime',  \swoole\table::TYPE_INT, 10)
        )
    ),
    MEMO_TABLE_USERS =>  array(
        'size'  =>  1024,
        'columns'    =>  array(
            array('userId', \swoole\table::TYPE_INT, 10),
            array('userName', \swoole\table::TYPE_STRING, 10),
            array('coin', \swoole\table::TYPE_INT, 4),
            array('endtime',  \swoole\table::TYPE_INT, 10)
        )
    )
);
