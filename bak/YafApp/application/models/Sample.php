<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author yangzhu
 */
class SampleModel {
    public function __construct() {
    		echo "\nmodel/sample\n";
    }   
    
    public function selectSample() {
        return 'Hello World!'; 
    }

    public function insertSample($arrInfo) {
        return true;
    }
}
