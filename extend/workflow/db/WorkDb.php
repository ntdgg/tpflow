<?php
namespace workflow;
/**
*+------------------
* Tpflow 节点事务处理
*+------------------
* Copyright (c) 2006~2020 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------
*/
use think\Db;
use think\facade\Session;

class WorkDb{
	
	/**
	 * 节点事务接口
	 *
	 * @param  $config 参数
	 **/
	public static function WorkApi($config)
	{
		$sql_return = 'null';
		$msg_return = 'null';
		//取出当前运行的步骤ID
		$run_flow_process = Db::name('run_process')->where('id','eq',$config['run_process'])->value('run_flow_process');
		//获取当前步骤版本ID，对应的所有信息
		$flow_process_info = Db::name('flow_process')->find($run_flow_process);
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
	public static function WorkSql($config,$flow_process_info)
	{
		$new_work_sql=str_replace(['@from_id','@run_id','@check_con'],[$config['wf_fid'],$config['run_id'],$config['check_con']],$flow_process_info['work_sql']);        //使用函数处理字符串
		try{
			$work_return = Db::query($new_work_sql);
		}catch(\Exception $e){
			$work_return = 'SQL_Err:'.$new_work_sql;
		}
		$result = Db::name('workinfo')->insertGetId(['datetime'=>date('Y-m-d h:i:s'),'type'=>'work_sql','bill_info'=>json_encode($config),'data'=>$new_work_sql,'info'=>$work_return]);
		if(!$result){
            return  '-1';
        }
        return $result;
	}
	/**
	 * 消息转换
	 *
	 **/
	public static function WorkMsg($config,$flow_process_info)
	{
		$new_work_msg=str_replace(['@from_id','@run_id','@check_con'],[$config['wf_fid'],$config['run_id'],$config['check_con']],$flow_process_info['work_msg']);        //使用函数处理字符串
		return Db::name('workinfo')->insertGetId(['datetime'=>date('Y-m-d h:i:s'),'type'=>'work_msg','bill_info'=>json_encode($config),'data'=>$new_work_msg,'info'=>'success']);
	}
	
	
	
}