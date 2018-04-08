<?php
/*
 * 公司新闻模块
 * @2018年1月21日
 * @Gwb
 */

namespace app\index\controller;

use app\common\controller\admin;
use think\Request;

class News extends Admin
{
	/**
	 *前置方法
	 */
	protected $beforeActionList = [
        'newtype'  =>  ['only'=>'add,edit'],
    ];
	/**
	 *前置方法角色及类别部署
	 */
	protected function newtype()
    {
        $type = db('news_type')->select();
		$this->assign('type', $type);
    }
    /**
     * 新闻列表
     */
    public function index($map=[])
    {
        if ($this->request->param("new_title")) $map[] = ['new_title','like',"%" . $this->request->param("new_title") . "%"];
        $list=controller('Base', 'event')->commonlist('news',$map);
		$this->assign('list', $list);
		return $this->fetch();
    }

    /**
     * 新增新闻
     */
    public function add()
    {
		if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('news',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
        return $this->fetch();
    }

    /**
     * 修改新闻
     */
    public function edit()
    {
        if ($this->request->isPost()) {
		$data = input('post.');
	    $ret=controller('Base', 'event')->commonedit('news',$data);
		if($ret['code']==0){
			return msg_return('修改成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
	   if(input('id')){
		 $info = db('news')->find(input('id'));
		 $this->assign('info', $info);
	   }
       return $this->fetch('add');
    }

    /**
	 * 删除新闻
	 */
	public function del()
	{
	   $ret=controller('Base', 'event')->commondel('news',input('id'));
	   if($ret['code']==0){
			return msg_return('删除成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	}
	/**
     * 查看新闻
     */
    public function view()
    {
		$info = db('news')->find(input('id'));
		$this->assign('info', $info);
        return $this->fetch();
    }
	/**
     * 类别管理
     */
    public function type()
    {
	   if ($this->request->isPost()) {
		$data = input('post.');
	    $ret=controller('Base', 'event')->commonadd('news_type',$data);
		if($ret['code']==0){
			$this->success('新增成功！');
			}else{
			$this->error('新增失败---Db0001');
		}
	   }
	   if(input('tid')){
		 $info = db('news_type')->find(input('tid'));
		 $this->assign('info', $info);
	   }
	  $list=controller('Base', 'event')->commonlist('news_type');
	  
	  $this->assign('list', $list);
	  return $this->fetch();
    }
	/**
     * 类别编辑
     */
    public function type_edit()
    {
        $data = input('post.');
	    $ret=controller('Base', 'event')->commonedit('news_type',$data);
		if($ret['code']==0){
			$this->success('修改成功！',url('type'));
			}else{
			$this->error('新增失败---Db0001');
		}
    }
    /**
     * 类别删除
     */
    public function type_del()
    {
	   $not = db('news')->where('new_type',input('id'))->find();
	   if($not){
			return json(['code'=>1,'msg'=>'该类别已有通知文件，无法删除！']);
		}
	   $ret=controller('Base', 'event')->commondel('news_type',input('id'));
	   if($ret['code']==0){
			return json(['code'=>0,'msg'=>'删除成功！']);
			}else{
			return json(['code'=>1,'msg'=>$ret['data']]);
		}
    }
}
