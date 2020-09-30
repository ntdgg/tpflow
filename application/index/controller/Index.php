<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use think\facade\Session;
use workflow\workflow;

class Index  extends Controller{
    public function index(){
	  $this->assign('user',db('user')->field('id,username,role')->select());
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
		$info = db('user')->find(input('id'));
        Session::set('uid', $info['id']);
		Session::set('uname', $info['username']);
		Session::set('role', $info['role']);
		$this->success('模拟登入成功！');
	}
}