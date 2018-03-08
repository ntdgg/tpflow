<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;

class Index  extends Controller{
    public function index(){
	    
       $this->success("马上带你进入示例",url('/index/demo/index'));
    }
  
   
}