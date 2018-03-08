<?php
namespace app\Home\controller;
use Common\Controller\CommonController;

class Home extends CommonController {
    public $_obj_model='';
    
    protected function _initialize(){
        //准备数据
         parent::_initialize();
    }
    
    public function test()
    {
        echo '<h1>HomeController.class.php</h1>';
        phpinfo();
    }

	

}
