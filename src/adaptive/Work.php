<?php
/**
 *+------------------
 * Tpflow 统一标准接口------代理模式数据库操作统一接口
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace tpflow\adaptive;

use tpflow\lib\unit;

Class Work{
    
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('wf_db_mode')==1){
			$className = '\\tpflow\\custom\\think\\AdapteeWork';
		}else{
			$className = unit::gconfig('wf_db_namespace').'AdapteeWork';
		}
		$this->mode = new $className();
    }
	/**
	 * 节点事务接口
	 *
	 * @param  $config 参数
	 **/
	static function WorkApi($config)
	{
		$sql_return = 'null';
		$msg_return = 'null';
		//取出当前运行的步骤ID
		$run_process = Run::FindRunProcessId($config['run_process']);
		//获取当前步骤版本ID，对应的所有信息
		$flow_process_info = Process::find($run_process['run_flow_process']);;
		if(!$flow_process_info){
			return 'flow_process_info err!';
		}
		if($flow_process_info['work_sql'] <> ''){
			$sql_return = self::WorkSql($config,$flow_process_info);
		}
		if($flow_process_info['work_msg'] <> ''){
			$msg_return= self::WorkMsg($config,$flow_process_info);
		}
		return 'work_sql:'.$sql_return.'|work_msg:'.$msg_return;
	}
	/**
	 * 审批事务执行处理
	 *
	 **/
	static function WorkSql($config,$flow_process_info)
	{
		return (new Work())->mode->WorkSql($config,$flow_process_info);
	}
	/**
	 * 消息转换
	 *
	 **/
	public static function WorkMsg($config,$flow_process_info)
	{
		return (new Work())->mode->WorkMsg($config,$flow_process_info);
	}
	
}