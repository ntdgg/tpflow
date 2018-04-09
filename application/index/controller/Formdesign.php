<?php
//------------------------
// 自动生成代码
//-------------------------

namespace app\index\controller;

use app\common\controller\admin;
use think\Config;
use think\Controller;
use think\Loader;
use think\Url;
use think\Db;
use tpdf\tpdf;

class Formdesign extends Admin
{
    /**
     * 首页
     * @return mixed
     */
    public function index($map=[])
    {
        if ($this->request->param("title")) $map[] = ['title','like',"%" . $this->request->param("title") . "%"];
        $list=controller('Base', 'event')->commonlist('form',$map);
		$this->assign('list', $list);
        return $this->view->fetch();
    }
	/**
     * 首页
     * @return mixed
     */
    public function desc($map=[])
    {
		if ($this->request->isPost()) {
		$data = input('post.');
		$data['ziduan'] = htmlspecialchars_decode($data['ziduan']);
	    $ret=controller('Base', 'event')->commonedit('form',$data);
		if($ret['code']==0){
			return msg_return('修改成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
	   $this->assign('ziduan','[]');
	   $this->assign('fid', input('id'));
        return $this->view->fetch();
    }
	public function edit_desc()
	{
		$info = db('form')->find(input('id'));
		$ziduan = json_decode($info['ziduan'],true);
		$this->assign('info',$info);
		$this->assign('ziduan',json_encode($ziduan['fields']));
		$this->assign('fid', input('id'));
		return $this->view->fetch('desc');
	}
	public function dsec_view()
	{
		$id = input('id');
		$info = db('form')->find($id);
		$ziduan = json_decode($info['ziduan'],true);
		$field = [];
		$form = [];
		foreach($ziduan['fields'] as $k=>$v){
			$field[$k]['name'] = 'demo';
			$field[$k]['type'] = 'text';
			$field[$k]['extra'] = '';
			$field[$k]['comment'] = $v['label'];
			$field[$k]['default'] = '';
			$form[$k]['title'] =  $v['label'];
			$form[$k]['name'] =  'demo';
			$form[$k]['type'] =  $v['field_type'];
			$form[$k]['option'] =  $v['field_options'];
			$form[$k]['default'] = '';
			$form[$k]['search'] = $v['search'];
			$form[$k]['lists'] = $v['lists'];
		}
		$data = [
		'module'=>'index',
		'controller'=>'demo',
		'menu'=>['add,del'],
		'title'=>$info['title'],
		'flow'=>$info['flow'],
		'table'=>$info['name'],
		'create_table'=>'demo',
		'field'=>$field,
		'form'=>$form
		];
		
		$tpdf = new tpdf();
		$tpdf->make($data,'demo');
		
		
	  return $this->view->fetch('demo/view');
	}
	/**
     * 首页
     * @return mixed
     */
    public function add($map=[])
    {
        if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('form',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
        return $this->view->fetch();
    }
	
	public function functions()
	{
		if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('form_function',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
	   $this->assign('fid', input('id'));
        return $this->view->fetch();
		
	}
	public function ajax_sql(){
		if ($this->request->isPost()){
            $sql=input("post.sql");
			$title=input("post.title");
			try{
			 $data = Db::query($sql);
			 $html = '<select><option value=>请选择'.$title.'</option>';
			 foreach($data as $k=>$v){
				 $html .= '<option value="'.$v["id"].'">'.$v["name"].'</option>';
			 }
			 $html .= '</select>';
			   echo  $html;
			}catch(\Exception $e){
				return  1; 	
			}
        }
	}
	public function shengcheng()
	{
		$id = input('id');
		$info = db('form')->find($id);
		$ziduan = json_decode($info['ziduan'],true);
		$field = [];
		$form = [];
		foreach($ziduan['fields'] as $k=>$v){
			$field[$k]['name'] = $v['name'];
			$field[$k]['type'] = 'text';
			$field[$k]['extra'] = '';
			$field[$k]['comment'] = $v['label'];
			$field[$k]['default'] = '';
			$form[$k]['title'] =  $v['label'];
			$form[$k]['name'] =  $v['name'];
			$form[$k]['type'] =  $v['field_type'];
			$form[$k]['option'] =  $v['field_options'];
			$form[$k]['default'] = '';
			$form[$k]['search'] = $v['search'];
			$form[$k]['lists'] = $v['lists'];
		}
		$data = [
		'module'=>'index',
		'controller'=>$info['name'],
		'menu'=>['add,del'],
		'title'=>$info['title'],
		'flow'=>$info['flow'],
		'table'=>$info['name'],
		'create_table'=>$info['name'],
		'field'=>$field,
		'form'=>$form
		];
		if($info['menu']==0){
			$menu = [
			'url'=>$info['name'].'/index',
			'name'=>$info['title'],
			];
			$ret=controller('Base', 'event')->commonadd('menu',$menu);
		}
		$tpdf = new tpdf();
		$tpdf->make($data);
		$up = [
			'id'=>$id,
			'status'=>1,
		];
		controller('Base', 'event')->commonedit('form',$up);
		$this->success('生成成功！','/index/index/welcome');
		
	}
}
