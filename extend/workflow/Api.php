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
		return view($this->patch.'/wfindex.html',['type'=>$this->table,'list'=>$this->work->FlowApi('List')]);
    }
	/**
	 * 流程添加
	 */
    public function wfadd()
    {
		if ($this->request::isPost()) {
			$data = input('post.');
			$data['uid']=session('uid');
			$data['add_time']=time();
			$ret= $this->work->FlowApi('AddFlow',$data);
			if($ret['code']==0){
				return $this->msg_return('发布成功！');
				}else{
				return $this->msg_return($ret['data'],1);
			}
	   }
	   return view($this->patch.'/wfadd.html',['type'=>$this->table]);
    }

		
}