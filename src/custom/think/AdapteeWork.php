<?php
/**
*+------------------
* Tpflow 节点事务处理
*+------------------
* Copyright (c) 2006~2020 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------
*/
declare (strict_types = 1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeWork{
	
	/**
	 * 审批事务执行处理
	 *
	 **/
	function WorkSql($config,$flow_process_info)
	{
		$new_work_sql=str_replace(['@from_id','@run_id','@check_con'],[$config['wf_fid'],$config['run_id'],$config['check_con']],$flow_process_info['work_sql']);        //使用函数处理字符串
		try{
			$work_return = Db::query($new_work_sql);
		}catch(\Exception $e){
			$work_return = 'SQL_Err:'.$new_work_sql;
		}
		$result = Db::name('wf_workinfo')->insertGetId(['datetime'=>date('Y-m-d h:i:s'),'type'=>'work_sql','bill_info'=>json_encode($config),'data'=>$new_work_sql,'info'=>$work_return]);
		if(!$result){
            return  '-1';
        }
        return $result;
	}
	/**
	 * 消息转换
	 *
	 **/
	function WorkMsg($config,$flow_process_info)
	{
		$new_work_msg=str_replace(['@from_id','@run_id','@check_con'],[$config['wf_fid'],$config['run_id'],$config['check_con']],$flow_process_info['work_msg']);        //使用函数处理字符串
		return Db::name('wf_workinfo')->insertGetId(['datetime'=>date('Y-m-d h:i:s'),'type'=>'work_msg','bill_info'=>json_encode($config),'data'=>$new_work_msg,'info'=>'success']);
	}
}