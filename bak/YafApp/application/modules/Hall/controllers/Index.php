<?php
/**
 * @name IndexController
 * @author yangzhu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/sample/index/index/index/name/yangzhu 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

		//2. fetch model
		$model = new SampleModel();
		echo "asdfasdf";
		//3. assign
		// $this->getView()->assign("content", $model->selectSample());
		// $this->getView()->assign("name", $name);
        // $this->getResponse()->setBody("{}\n");
        // $this->getResponse()->appendBody("{'xxxx'}\n");
        Mogic\DataLeaker::set('xx', "fuck");
		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return false;
	}

	public function helloAction($name = "Stranger") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");
		// $a->abc();
		//2. fetch model
		// $model = new hall\models\SampleModel();
		$model = new Hall_SampleModel();
		Common::test(1, 2);

		A_B::test(1, 2);

		$b = new A_B();
		$b->test2(2, 4);
		// $session=Yaf_Session::getInstance();
		// Common::test(1,2);
		// $common = new common();
		// $common->test(1, 2);
		//显示session[username]
		// var_dump($session->username);
		//创建session
		// $session->username='admin';

		//显示session[username]
		// var_dump($session->username);
		echo "asdfasdf";
		        Mogic\DataLeaker::set('xx', "fuck");

		//3. assign
		// $this->getView()->assign("content", $model->selectSample());
		// $this->getView()->assign("name", $name);
        $this->getResponse()->setBody("{}\n");
        $this->getResponse()->appendBody("{'xxxx'}\n");

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return false;
	}
}
