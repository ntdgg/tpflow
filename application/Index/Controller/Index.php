<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use think\facade\Session;

class Index  extends Controller{
    public function index(){
		//Session::clear();
	  $this->assign('user',db('user')->field('id,username')->select());
      return $this->fetch();
    }
	public function login(){
		Session::clear();
        Session::set('uid', input('id'));
		Session::set('uname', input('user'));
		$this->success('模拟登入成功！');
		exit;
	}
}