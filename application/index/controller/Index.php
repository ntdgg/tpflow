<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use think\facade\Session;
use workflow\workflow;

class Index  extends Controller{
    public function index(){
		
	  $this->assign('user',db('user')->field('id,username,role')->select());
	  $this->assign('menu',db('menu')->select());
      return $this->fetch();
    }
	public function welcome(){
	  $this->assign('user',db('user')->field('id,username,role')->select());
      return $this->fetch();
    }
	public function doc(){
	  
      return $this->fetch();
    }
	public function login(){
		Session::clear();
        Session::set('uid', input('id'));
		Session::set('uname', input('user'));
		Session::set('role', input('role'));
		$this->success('模拟登入成功！');
		exit;
	}
}