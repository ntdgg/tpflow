<?php
namespace tpflow\custom\think;
/**
*+------------------
* Tpflow 工作流日志消息
*+------------------
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------
*/
use think\facade\Db;

class AdapteeLog{
	
	/**
	 * 工作流审批日志记录
	 *
	 **/
	function AddrunLog($data)
	{
		 $ret = Db::name('wf_run_log')->insertGetId($data);
		 if(!$ret){
				return  false;
		 }
		return $ret;
	}
	function SearchRunLog($wf_fid,$wf_type){
		return Db::name('wf_run_log')->where('from_id',$wf_fid)->where('from_table',$wf_type)->select()->all();
	}
	
	
}