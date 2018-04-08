<?php
namespace app\[MODULE]\controller[NAMESPACE];

use app\common\controller\admin;
use think\Request;

class [NAME] extends Admin
{
	public function index($map='')
	{
		[FILTER]
		$list=controller('Base', 'event')->commonlist('[NAME]',$map);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		$list=controller('Base', 'event')->commonedit('[NAME]');
		$this->assign('vo', $list);
		return $this->fetch();
	}
	public function add()
	{
	if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('[NAME]',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
		return $this->fetch('edit');
	}
}
