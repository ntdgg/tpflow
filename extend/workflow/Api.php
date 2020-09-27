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