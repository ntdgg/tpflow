<?php
/**
*+------------------
* 普通提交工作流
*+------------------ 
*/
namespace workflow;

use think\Db;

class DosupFlow{
	/**
	 * 任务执行
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doSupEnd($wfid,$uid) {
		//读取工作流信息
		$wfinfo = InfoDB::workrunInfo($wfid);
		if(!$wfinfo){
				return ['msg'=>'流程信息有误！','code'=>'-1'];
			} 
		$config = array(
				'wf_fid'=>$wfinfo['from_id'],
				'wf_type'=>$wfinfo['from_table'],
                'check_con'=>'编号：'.$uid.'的超级管理员终止了本流程！',
            );
			
		//结束当前run 工作流
		$end = $this->end_flow($wfid);
		$end = $this->end_process($wfinfo,'编号：'.$uid.'的超级管理员终止了本流程！');
		$run_log = LogDb::AddrunLog($uid,$wfid,$config,'SupEnd');
		
		if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
		//更新单据状态
		$bill_update = InfoDB::UpdateBill($config['wf_fid'],$config['wf_type'],2);
		if(!$bill_update){
			return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
		}
	}
	/**
	 *结束工作流
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function end_flow($run_id)
	{	
		return Db::name('run')->where('id',$run_id)->update(['status'=>1,'endtime'=>time()]);	
	}
	/**
	 *结束工作流
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function end_process($wf,$check_con)
	{
		return Db::name('run_process')->where('run_id',$wf['id'])->where('run_flow_process',$wf['run_flow_process'])->where('run_flow',$wf['flow_id'])->update(['status'=>2,'remark'=>$check_con,'bl_time'=>time()]);
	}
	/**
	 *运行记录
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function Run($config,$uid,$todo)
	{
		$wf_process = ProcessDb::GetProcessInfo($config['npid']);
		//添加流程步骤日志
		$wf_process_log = InfoDB::addWorkflowProcess($config['flow_id'],$wf_process,$config['run_id'],$uid,$todo);
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