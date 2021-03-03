<?php
/**
*+------------------
* Tpflow 普通提交工作流
*+------------------
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------ 
*/
namespace tpflow\service\command;

//数据库操作
use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Log;
use tpflow\adaptive\Bill;
use tpflow\adaptive\Run;

class SupFlow{
	/**
	 * 任务执行
	 *
	 * @param mixed $uid  用户ID
	 */
	public function doSupEnd($wfid,$uid) {
		//读取工作流信息
		$wfinfo = Info::workrunInfo($wfid);
		if(!$wfinfo){
				return ['msg'=>'流程信息有误！','code'=>'-1'];
			} 
		$config = [
				'wf_fid'=>$wfinfo['from_id'],
				'wf_type'=>$wfinfo['from_table'],
                'check_con'=>'编号：'.$uid.'的超级管理员终止了本流程！',
            ];
		//结束当前run 工作流
            Flow::end_flow($wfid);
			
			$run_flow_process_id = Run::FindRunProcess([['run_id','=',$wfid],['run_flow_process','=',$wfinfo['run_flow_process']]]);
		    $end = Flow::end_process([$run_flow_process_id['id']],$config['check_con']);
		    Log::AddrunLog($uid,$wfid,$config,'SupEnd');
		
		if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
		//更新单据状态
		$bill_update = Bill::updatebill($config['wf_type'],$config['wf_fid'],2);;
		if(!$bill_update){
			return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
		}
		return ['msg'=>'success!','code'=>'0'];
	}
}