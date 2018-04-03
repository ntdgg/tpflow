<?php
namespace app\index\controller;

use app\common\controller\admin;
use think\Request;

class test extends Admin
{
	public function index()
	{
		$list=controller('Base', 'event')->commonlist('test');
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		$list=controller('Base', 'event')->commonedit('test');
		$this->assign('vo', $list);
		return $this->fetch();
	}
}
