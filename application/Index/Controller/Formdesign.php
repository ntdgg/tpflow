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
		
		$this->assign('fid', input('id'));
		
        return $this->view->fetch();
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
			try{
			   dump(Db::query($sql));
			}catch(\Exception $e){
				return  1; 	
			}
        }
	}
	public function shengcheng()
	{
		$generate = new \Generate();
		$info = db('form')->find(1);
		
		$ziduan = json_decode($info['ziduan'],true);
		$field = [];
		
		foreach($ziduan['fields'] as $k=>$v){
			$field[$k]['name'] = $v['name'];
			$field[$k]['type'] = 'text';
			$field[$k]['extra'] = '';
			$field[$k]['comment'] = $v['label'];
			$field[$k]['default'] = '';
		}
		$data = [
		'module'=>'index',
		'controller'=>'Test',
		'menu'=>['index'],
		'title'=>'title',
		'table'=>'test',
		'create_table'=>'test',
		'field'=>$field
			
		
		];
		$generate->run($data);
		
	}
    /**
     * 生成代码
     */
    public function run()
    {
        $generate = new \Generate();
        $data = $this->request->post();
        unset($data['file']);
        $generate->run($data, $this->request->post('file'));

        if (isset($data['delete_file']) && $data['delete_file']) {
            return ajax_return_adv('删除成功', '', false, '', '', ['action' => '']);
        }
        return ajax_return_adv('生成成功', '', false, '', '', ['action' => Url::build($data['module'] . '/' . $data['controller'] . '/index')]);
    }
}
