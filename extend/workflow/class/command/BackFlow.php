<?php
/**
*+------------------
* 工作流回退
*+------------------ 
*/

namespace workflow;

use think\Db;

class BackFlow{
	/**
	 * 工作流回退
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doTask($config,$uid) {
		$wf_title = $config['wf_title'];
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$flow_id = $config['flow_id'];
		$npid = $config['npid'];//下一步骤流程id
		$run_id = $config['run_id'];
		$check_con = $config['check_con'];
		$run_process = $config['run_process'];//运行中的process
		$submit_to_save = $config['submit_to_save'];
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
			$end = $this->end_flow($run_id);//结束流程
			$end = $this->end_process($run_process,$check_con);
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
			$run_log = LogDb::AddrunLog($uid,$config['run_id'],$config,'Back');
			if(!$run_log){
					return ['msg'=>'消息记录失败，数据库错误！！！','code'=>'-1'];
				}
		}else{ //结束流程
			//$end = $this->end_flow($run_id);//结束该流程
			$end = $this->end_process($run_process,$check_con);
			if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			}
			$run = $this->Run($config,$uid,$todo);//添加回退步骤流程
			//消息通知发起人
			$run_update = $this->up($run_id,$wf_backflow);
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
		return Db::name('run')->where('id','eq',$run_id)->update(['status'=>1,'endtime'=>time()]);
	}
	/**
	 *结束结束流程缓存
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function end_process($run_process,$check_con)
	{
		return Db::name('run_process')->where('id','eq',$run_process)->update(['status'=>2,'remark'=>$check_con,'bl_time'=>time()]);
	}
	/**
	 *运行
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function Run($config,$uid,$todo)
	{
		$wf_process = ProcessDb::GetProcessInfo($config['wf_backflow']);
		//添加流程步骤日志
		$wf_process_log = InfoDB::addWorkflowProcess($config['flow_id'],$wf_process,$config['run_id'],$uid,$todo);
		if(!$wf_process_log){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
		
		//日志记录
		$run_log = LogDb::AddrunLog($uid,$config['run_id'],$config,'Back');
		if(!$wf_process_log){
				return ['msg'=>'消息记录失败，数据库错误！！！','code'=>'-1'];
			}
	}
	/**
	 *更新单据信息
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function up($run_id,$flow_process)
	{
		return Db::name('run')->where('id','eq',$run_id)->update(['run_flow_process'=>$flow_process]);	
	}
	
}