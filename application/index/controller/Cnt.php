<?php
namespace app\index\controller;

use app\common\controller\admin;
use think\Request;

class Cnt extends Admin
{
	public function index($map='')
	{
		        if ($this->request->param("name2")) {
            $map['name2'] = ["like", "%" . $this->request->param("name2") . "%"];
        }
        if ($this->request->param("tel2")) {
            $map['tel2'] = ["like", "%" . $this->request->param("tel2") . "%"];
        }
        if ($this->request->param("cont2")) {
            $map['cont2'] = ["like", "%" . $this->request->param("cont2") . "%"];
        }
        if ($this->request->param("name")) {
            $map['name'] = ["like", "%" . $this->request->param("name") . "%"];
        }
        if ($this->request->param("tel")) {
            $map['tel'] = ["like", "%" . $this->request->param("tel") . "%"];
        }
        if ($this->request->param("cont")) {
            $map['cont'] = ["like", "%" . $this->request->param("cont") . "%"];
        }

		$list=controller('Base', 'event')->commonlist('Cnt',$map);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		$list=controller('Base', 'event')->commonedit('Cnt');
		$this->assign('vo', $list);
		return $this->fetch();
	}
	public function add()
	{
	if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('Cnt',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
		return $this->fetch('edit');
	}
}
