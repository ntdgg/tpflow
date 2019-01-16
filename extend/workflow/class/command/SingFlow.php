<?php
/**
*+------------------
* 工作流回退
*+------------------ 
*/
namespace workflow;

use think\Db;

class SingFlow{
	/**
	 * 回退工作流
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doTask($config,$uid) {
		//任务全局类
		$wf_title = $config['wf_title'];
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$flow_id = $config['flow_id'];
		$run_process = $config['run_process'];
		$npid = $config['npid'];//下一步骤流程id
		$run_id = $config['run_id'];
		if($config['sup']=='1'){
			$check_con = '[管理员代办]'.$config['check_con'];
			$config['check_con'] = '[管理员代办]'.$config['check_con'];
		}else{
			$check_con = $config['check_con'];
		}
		$submit_to_save = $config['submit_to_save'];
		$sid = $this->AddSing($config);
		//结束当前流程，给个会签标志
		$end = $this->up_flow($run_id,$sid);
		//结束process
		$end = $this->end_process($run_process,$check_con);
		//加入会签
		$run_log = LogDb::AddrunLog($uid,$run_id,$config,'Sing');
		//日志记录
	}
	/**
	 *结束工作流
	 *
	 * @param $run_process 流程ID
	 * @param $check_con 审批意见
	 **/
	public function end_process($run_process,$check_con)
	{
		return Db::name('run_process')->where('id',$run_process)->update(['status'=>2,'remark'=>$check_con,'bl_time'=>time()]);
	}
	/**
	 *会签确认
	 *
	 * @param $config 参数信息
	 * @param $uid  用户ID
	 * @param $wf_actionid 操作按钮值
	 **/
	public function doSingEnt($config,$uid,$wf_actionid)
	{
		$sing_id = Db::name('run')->where('id',$config['run_id'])->value('sing_id');
		$this->EndSing($sing_id,$config['check_con']);//结束当前会签
		if ($wf_actionid == "sok") {//提交处理
			
			if($config['npid'] !=''){
				$wf_process = ProcessDb::GetProcessInfo($config['npid']);
				InfoDB::addWorkflowProcess($config['flow_id'],$wf_process,$config['run_id'],$uid);
				$this->up_flow_press($config['run_id'],$config['npid']);
			}
			
			$this->up_run($config['run_id']);
			
			//日志记录
		} else if ($wf_actionid == "sback") {//退回处理
			//$end = $this->end_flow($config['run_id']);//结束当前工作流
			//判断是否是第一步，第一步：更新单据，发起修改，不是第一步，写入新的工作流
			$wf_backflow = $config['wf_backflow'];//退回的步骤ID，如果等于0则默认是第一步
			
			if($wf_backflow==0){
				$back = true;
				}else{
				$back =false;
			}
			if($back){//第一步
				//更新单据状态
				$bill_update = InfoDB::UpdateBill($config['wf_fid'],$config['wf_type'],'-1');
				if(!$bill_update){
					return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
				}
				$run_log = LogDb::AddrunLog($uid,$config['run_id'],$config,'SingBack');
				$this->up_run($config['run_id']);
				//日志记录
			}else{ //结束流程
				$wf_process = ProcessDb::GetProcessInfo($wf_backflow);
				$wf_run_process = InfoDB::addWorkflowProcess($config['flow_id'],$wf_process,$config['run_id'],$uid);
				$this->up_run($config['run_id']);
				//消息通知发起人
			}
			//日志记录
		} else if ($wf_actionid == "ssing") {//会签
			//日志记录
			$run_log = LogDb::AddrunLog($uid,$config['run_id'],$config,'SingSing');
			$sid = $this->AddSing($config);
			$end = $this->up_flow($config['run_id'],$sid);
			//发起新的会签
		} else { //通过
			throw new \Exception ("参数出错！");
		}
		
	}
	/**
	 *会签执行
	 *
	 * @param $sing_sign 会签ID
	 * @param $check_con  审核内容
	 **/
	public function EndSing($sing_sign,$check_con)
	{
		return Db::name('run_sign')->where('id',$sing_sign)->update(['is_agree'=>1,'content'=>$check_con,'dateline'=>time()]);
	}
	/**
	 *更新单据信息
	 *
	 *@param $run_id 工作流run id
	 **/
	public function up_run($run_id)
	{
		return Db::name('run')->where('id',$run_id)->update(['is_sing'=>0]);
	}
	/**
	 *更新流程信息
	 *
	 *@param $run_id 工作流ID
	 *@param $run_process 运行步骤
	 **/
	public function up_flow_press($run_id,$run_process)
	{
		return Db::name('run')->where('id',$run_id)->update(['run_flow_process'=>$run_process]);
	}
	/**
	 *新增会签
	 *
	 *@param $config 参数信息
	 **/
	public function AddSing($config)
	{
		$data = [
			'run_id'=>$config['run_id'],
			'run_flow'=>$config['flow_id'],
			'run_flow_process'=>$config['run_process'],
			'uid'=>$config['wf_singflow'],
			'dateline'=>time()
		];
		$run_sign = Db::name('run_sign')->insertGetId($data);
		if(!$run_sign){
            return  false;
        }
        return $run_sign;	
	}
	/**
	 *更新流程
	 *
	 *@param $run_id 工作流ID
	 *@param $sid 会签ID
	 **/
	public function up_flow($run_id,$sid)
	{
		return Db::name('run')->where('id',$run_id)->update(['is_sing'=>1,'sing_id'=>$sid,'endtime'=>time()]);
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
}