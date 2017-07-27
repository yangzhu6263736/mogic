<?php

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo "This is the default task and the default action" . PHP_EOL;
    }

    /**
     * @param array $params
     */
    public function testAction(array $params)
    {
    		echo "xxxx\n";
    		return "yyyy\n";
     	// echo json_encode($params);
    }
}