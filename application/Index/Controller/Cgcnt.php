<?php
namespace app\index\controller;

use app\common\controller\admin;
use think\Request;

class Cgcnt extends Admin
{
	public function index($map='')
	{
		 if ($this->request->param("yestel")) {
            $map['tel'] = ["like", "%" . $this->request->param("tel") . "%"];
        }

		$list=controller('Base', 'event')->commonlist('cgcnt',$map);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		$list=controller('Base', 'event')->commonedit('cgcnt');
		$this->assign('vo', $list);
		return $this->fetch();
	}
}
