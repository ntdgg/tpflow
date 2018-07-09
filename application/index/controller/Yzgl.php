<?php
namespace app\index\controller;

use app\common\controller\admin;
use think\Request;

class Yzgl extends Admin
{
	public function index($map='')
	{
		        if ($this->request->param("name")) {
            $map['name'] = ["like", "%" . $this->request->param("name") . "%"];
        }
        if ($this->request->param("bz")) {
            $map['bz'] = ["like", "%" . $this->request->param("bz") . "%"];
        }
        if ($this->request->param("user")) {
            $map['user'] = ["like", "%" . $this->request->param("user") . "%"];
        }
        if ($this->request->param("tel")) {
            $map['tel'] = ["like", "%" . $this->request->param("tel") . "%"];
        }
        if ($this->request->param("add")) {
            $map['add'] = ["like", "%" . $this->request->param("add") . "%"];
        }

		$list=controller('Base', 'event')->commonlist('Yzgl',$map);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		$list=controller('Base', 'event')->commonedit('Yzgl');
		$this->assign('vo', $list);
		return $this->fetch();
	}
	public function add()
	{
	if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('Yzgl',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
		return $this->fetch('edit');
	}
}
