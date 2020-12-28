<?php
/**
*+------------------
* TPFLOW 工作流回退
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
use tpflow\adaptive\Process;
use tpflow\adaptive\Log;
use tpflow\adaptive\Bill;

class BackFlow{
	/**
	 * 工作流回退
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doTask($config,$uid) {
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$run_id = $config['run_id'];
		if($config['sup']=='1'){
			$check_con = '[管理员代办]'.$config['check_con'];
			$config['check_con'] = '[管理员代办]'.$config['check_con'];
		}else{
			$check_con = $config['check_con'];
		}
		$run_process = $config['run_process'];//运行中的process
		$wf_backflow = $config['wf_backflow'];//退回的步骤ID，如果等于0则默认是第一步
		if($wf_backflow==0){
			$back = true;
			}else{
			$back = false;
		}
		if(isset($config['btodo']) && $config['btodo'] != ''){
			$todo = $config['btodo'];
		}else{
			$todo = '';
		}
		if($back){//第一步
			$end = Flow::end_flow($run_id);//结束流程
			$end = Flow::end_process($run_process,$check_con);
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
			//更新单据状态
			$bill_update = Bill::updatebill($wf_type,$wf_fid,'-1');
			if(!$bill_update){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
			//消息通知
			//日志记录
			$run_log = Log::AddrunLog($uid,$run_id,$config,'Back');
			if(!$run_log){
					return ['msg'=>'消息记录失败，数据库错误！！！','code'=>'-1'];
				}
		}else{ //结束流程
			$end = Flow::end_process($run_process,$check_con);
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			}
			$run = $this->Run($config,$uid,$todo);//添加回退步骤流程
			//消息通知发起人
			$run_update = Flow::up($run_id,$wf_backflow);
		}
		return ['msg'=>'success!','code'=>'0'];
	}
	
	/**
	 *运行
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function Run($config,$uid,$todo)
	{
		$wf_process = Process::GetProcessInfo($config['wf_backflow'],$config['run_id']);
		//添加流程步骤日志
		$wf_process_log = Info::addWorkflowProcess($config['flow_id'],$wf_process,$config['run_id'],$uid,$todo);
		if(!$wf_process_log){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
		
		//日志记录
		$run_log = Log::AddrunLog($uid,$config['run_id'],$config,'Back');
		if(!$wf_process_log){
				return ['msg'=>'消息记录失败，数据库错误！！！','code'=>'-1'];
			}
	}
}