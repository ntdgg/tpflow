<?php
/**
 *+------------------
 * Tpflow 模板驱动类
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace workflow;

use workflow\workflow;
use think\Db;
use think\facade\Request;
define('ROOT_PATH',\Env::get('root_path') );

	class Api{
		public $patch = '';
		public $topconfig = '';
		function __construct(Request $request) {
			$this->int_url = 'index';//定义默认使用index模块，可以直接修改
			$this->work = new workflow();
			$this->uid = session('uid');
			$this->role = session('role');
			$this->Tmp  = '../extend/workflow/view/';
			$this->table  = Db::query("select replace(TABLE_NAME,'".config('database.prefix')."','')as name,TABLE_COMMENT as title from information_schema.tables where table_schema='".config('database.database')."' and table_type='base table' and TABLE_COMMENT like '[work]%';");
			$this->patch =  ROOT_PATH . 'extend/workflow/view';
			$this->request = $request;
			
	   }
	/**
	 * 版权信息，请勿删除
	 * 授权请联系：632522043@qq.com
	 */
	public function welcome(){
		return '<br/><br/><style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} 
		a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; }
		h1{ font-size: 40px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 35px }</style>
		<div style="padding: 24px 48px;"> <h1>\﻿ (•◡•) / </h1><p> TpFlow V3.2正式版<br/>
		<span style="font-size:19px;">PHP开源工作流引擎系统</span></p>
		<span style="font-size:15px;">[ ©2018-2020 Guoguo <a href="https://www.cojz8.com/">TpFlow</a> 本版权不可删除！ ]</span></div>';
    }
	/**
	 * 流程设计首页
	 * @param $map 查询参数
	 */
	public function wfindex($map = []){
		$type = [];
		foreach($this->table as $k=>$v){
			$type[$v['name']] = str_replace('[work]', '', $v['title']);;
		}
		return view($this->patch.'/wfindex.html',['int_url'=>$this->int_url,'type'=>$type,'list'=>$this->work->FlowApi('List')]);
    }
	/**
	 * 流程添加
	 */
    public function wfadd()
    {
		if ($this->request::isPost()) {
			$data = input('post.');
			$data['uid']=$this->uid;
			$data['add_time']=time();
			$ret= $this->work->FlowApi('AddFlow',$data);
			if($ret['code']==0){
				return $this->msg_return('发布成功！');
				}else{
				return $this->msg_return($ret['data'],1);
			}
	   }
	   return view($this->patch.'/wfadd.html',['int_url'=>$this->int_url,'type'=>$this->table]);
    }
		 /**
	 * 流程修改
	 */
	public function wfedit()
    {
        if ($this->request::isPost()) {
			$data = input('post.');
			$ret= $this->work->FlowApi('EditFlow',$data);
			if($ret['code']==0){
				return $this->msg_return('修改成功！');
				}else{
				return $this->msg_return($ret['data'],1);
			}
	   }
	   return view($this->patch.'/wfadd.html',['int_url'=>$this->int_url,'type'=>$this->table,'info'=>$this->work->FlowApi('GetFlowInfo',input('id'))]);
    }
	/**
	 * 工作流设计界面
	 */
    public function wfdesc(){
		 
        $flow_id = intval(input('flow_id'));
        if($flow_id<=0){
            $this->error('参数有误，请返回重试!');
		}
        $one = $this->work->FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
            $this->error('未找到数据，请返回重试!');
        }
		//Url转换地址
		$urls = [
			'welcome'=>url($this->int_url.'/wf/welcome'),
			'add_process'=>url($this->int_url.'/wf/add_process'),
			'Checkflow'=>url($this->int_url.'/wf/Checkflow'),
			'save_canvas'=>url($this->int_url.'/wf/save_canvas'),
			'del_allprocess'=>url($this->int_url.'/wf/del_allprocess'),
			'delete_process'=>url($this->int_url.'/wf/delete_process'),
			'wfatt'=>url($this->int_url.'/wf/wfatt')
		];
		return view($this->patch.'/wfdesc.html',['url'=>$urls,'one'=>$one,'process_data'=>$this->work->ProcessApi('All',$flow_id)]);
    }
	/**
	 * 添加流程
	 **/
    public function add_process()
    {
        $flow_id = input('flow_id');
        $one = $this->work->FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
          return json(['status'=>0,'msg'=>'添加失败,未找到流程','info'=>'']);
        }
		return json($this->work->ProcessApi('ProcessAdd',$flow_id));
    }
	/**
	 * 保存布局
	 **/
    public function save_canvas()
    {
		return json($this->work->ProcessApi('ProcessLink',input('flow_id'),input('process_info')));
    }
	public function save_attribute()
    {
	    $data = input('post.');
		return json($this->work->ProcessApi('ProcessAttSave',$data['process_id'],$data));
    }
	//步骤属性
    public function wfatt()
    {
	    $info = $this->work->ProcessApi('ProcessAttView',input('id'));
		return view($this->patch.'/wfatt.html',['int_url'=>$this->int_url,'op'=>$info['show'],'one'=>$info['info'],'from'=>$info['from'],'process_to_list'=>$info['process_to_list'],'child_flow_list'=>$info['child_flow_list']]);
    }
	/**
	 * 删除流程
	 **/
   public function delete_process()
    {
		return json($this->work->ProcessApi('ProcessDel',input('flow_id'),input('process_id')));
    }
	public function del_allprocess()
	{
		return json($this->work->ProcessApi('ProcessDelAll',input('flow_id')));
	}
	public function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
	{
		$ret = ["code" => $code, "msg" => $msg, "data" => $data];
		$extend['opt'] = [
			'alert'    => $alert,
			'close'    => $close,
			'redirect' => $redirect,
			'url'      => $url,
		];
		$ret = array_merge($ret, $extend);
		return json($ret);
	}

		
}