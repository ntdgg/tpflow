<?php
namespace app\[MODULE]\controller[NAMESPACE];

use app\common\controller\admin;
use think\Request;

class [NAME] extends Admin
{
	public function index()
	{
		$list=controller('Base', 'event')->commonlist('[NAME]');
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		$list=controller('Base', 'event')->commonedit('[NAME]');
		$this->assign('vo', $list);
		return $this->fetch();
	}
}
