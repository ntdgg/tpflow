<?php
namespace app\index\controller;

use app\common\controller\admin;
use think\Request;

class Test extends Admin
{
	public function index()
	{
		
		  $list=controller('Base', 'event')->commonlist('Test');
		  $this->assign('list', $list);
		 return $this->fetch();
	}
}
