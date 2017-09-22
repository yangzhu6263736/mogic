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
            array('userId', \swoole\table::TYPE_INT, 11),
            array('channelId', \swoole\table::TYPE_INT, 11),
            // array('account', \swoole\table::TYPE_STRING, 30),
            array('passWord', \swoole\table::TYPE_STRING, 30),
            array('userName', \swoole\table::TYPE_STRING, 30),
            array('picUrl', \swoole\table::TYPE_STRING, 60),
            array('money_1', \swoole\table::TYPE_INT, 11),
            array('money_2', \swoole\table::TYPE_INT, 11),
            array('money_3', \swoole\table::TYPE_INT, 11),
            array('money_4', \swoole\table::TYPE_INT, 11),
            array('money_5', \swoole\table::TYPE_INT, 11),
            array('vipType', \swoole\table::TYPE_INT, 4),
            array('vipEndTime', \swoole\table::TYPE_INT, 11),
            array('addTime', \swoole\table::TYPE_INT, 11),
            array('lastLoginTime', \swoole\table::TYPE_INT, 11),
            array('updateTime', \swoole\table::TYPE_INT, 11),
            array('isBind', \swoole\table::TYPE_INT, 4),
            array('gtSdkCid', \swoole\table::TYPE_STRING, 40),
            array('simDeviceKey', \swoole\table::TYPE_STRING, 40),
            array('dModel', \swoole\table::TYPE_STRING, 40),
            array('dSVersion', \swoole\table::TYPE_STRING, 40)
        )
    ),
    MEMO_TABLE_ROOMS =>  array(
        'size'  =>  1024,
        'columns'    =>  array(
            array('roomType', \swoole\table::TYPE_INT, 11),
            array('orderId', \swoole\table::TYPE_INT, 11),
            array('playType', \swoole\table::TYPE_INT, 4),
            array('basicScore', \swoole\table::TYPE_INT, 11),
            array('oncePlayCostNum', \swoole\table::TYPE_INT, 11),
            array('needMinNum', \swoole\table::TYPE_INT, 11)
        )
    ),
    
);
