<?php
namespace workflow;

use think\Db;

class BackFlow{
	/**
	 * 执行任务
	 *
	 */
	public function doTask($config) {
		$wf_title = $config['wf_title'];
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$flow_id = $config['flow_id'];
		$run_flow_process = $config['run_flow_process'];
		$npid = $config['npid'];//下一步骤流程id
		$run_id = $config['run_id'];
		$check_con = $config['check_con'];
		$submit_to_save = $config['submit_to_save'];
		$wf_backflow = $config['wf_backflow'];//退回的步骤ID，如果等于0则默认是第一步
		if($wf_backflow==0){
			$back = true;
			}else{
			$back = $this->IsOneFlow($wf_backflow);
		}
		if($back){//第一步
			$end = $this->end_flow($run_id);//结束流程
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
			//更新单据状态
			$bill_update = InfoDB::UpdateBill($wf_fid,$wf_type,'-1');
			if(!$bill_update){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
			//消息通知
			//日志记录
		}else{ //结束流程
			$end = $this->end_flow($run_id);//结束该流程
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			}
			$run = $this->Run($flow_id,$wf_backflow,$wf_fid,$wf_type);//添加回退步骤流程
			//消息通知发起人
		}
	}
	/**
	 *判断是否是第一步
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function IsOneFlow($run_flow_process)
	{
		$info = Db::name('flow_process')->find($run_flow_process);
		if($info['process_type']=='is_one'){
			return true;
		}else{
			return false;
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
	public function Run($wf_id,$wf_process,$wf_fid,$wf_type)
	{
		$wf_run = InfoDB::addWorkflowRun($wf_id,$wf_process,$wf_fid,$wf_type);
			if(!$wf_run){
				return ['msg'=>'流程发起失败，数据库操作错误！！','code'=>'-1'];
			}
		//添加流程步骤日志
		$wf_process_log = InfoDB::addWorkflowProcess($wf_id,$wf_process,$wf_run);
			if(!$wf_process_log){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
		$run_log = InfoDB::AddrunLog(1,$wf_run,'[回退]审批意见',$wf_fid,$wf_type);
	}
}