<?php
namespace workflow;

use think\Db;

class TaskFlow{
	/**
	 * 执行任务
	 *
	 */
	public function doTask($config,$uid) {
		//任务全局类
		$wf_title = $config['wf_title'];
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$flow_id = $config['flow_id'];
		$run_flow_process = $config['run_flow_process'];
		$npid = $config['npid'];//下一步骤流程id
		$run_id = $config['run_id'];
		$check_con = $config['check_con'];
		$submit_to_save = $config['submit_to_save'];
		if($npid != ''){//判断是否为最后
			//结束流程
			 $end = $this->end_flow($run_id);
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
			//记录下一个流程->消息记录
			$run = $this->Run($config,$uid);

			}else{ 
				//结束该流程
				$end = $this->end_flow($run_id);
				
				$run_log = LogDb::AddrunLog($uid,$run_id,$config,'ok');
				if(!$end){
					return ['msg'=>'结束流程错误！！！','code'=>'-1'];
				} 
			//更新单据状态
			$bill_update = InfoDB::UpdateBill($wf_fid,$wf_type,2);
			if(!$bill_update){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
			//消息通知发起人
		}
	}
	/**
	 *结束工作流
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function end_flow($run_id)
	{
		$result = Db::execute('update leipi_run set status = 1,endtime='.time().' where id = '.$run_id.' ');
		return $result;	
	}
	/**
	 *运行记录
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function Run($config,$uid)
	{
		//添加下一个工作流
		$wf_run = InfoDB::addWorkflowRun($config['flow_id'],$config['npid'],$config['wf_fid'],$config['wf_type']);
			if(!$wf_run){
				return ['msg'=>'流程发起失败，数据库操作错误！！','code'=>'-1'];
			}
		//添加流程步骤日志
		$wf_process_log = InfoDB::addWorkflowProcess($config['flow_id'],$config['npid'],$wf_run);
		if(!$wf_process_log){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
		//日志记录
		$run_log = LogDb::AddrunLog($uid,$config['run_id'],$config,'ok');
		if(!$wf_process_log){
				return ['msg'=>'消息记录失败，数据库错误！！！','code'=>'-1'];
			}
	}
	
	

}