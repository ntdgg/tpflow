<?php
namespace workflow;

use think\Db;

class SingFlow{
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
		$sid = $this->AddSing($config);
		//结束当前流程，给个会签标志
		$end = $this->up_flow($run_id,$sid);
		//加入会签
		$run_log = LogDb::AddrunLog($uid,$run_id,$config,'Sing');
		//日志记录
		}
	
	public function doSingEnt($config,$uid,$wf_actionid)
	{
		$sing_id = Db::name('run')->where('id',$config['run_id'])->value('sing_id');
		$this->EndSing($sing_id,$config['check_con']);//结束当前会签
		if ($wf_actionid == "sok") {//提交处理
			$end = $this->end_flow($config['run_id']);//结束当前工作流
			//进入工作流下一步骤
			$wf_run = InfoDB::addWorkflowRun($config['flow_id'],$config['npid'],$config['wf_fid'],$config['wf_type']);
			if(!$wf_run){
				return ['msg'=>'流程发起失败，数据库操作错误！！','code'=>'-1'];
			}
			//日志记录
		} else if ($wf_actionid == "sback") {//退回处理
			$end = $this->end_flow($config['run_id']);//结束当前工作流
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
				//日志记录
			}else{ //结束流程
				InfoDB::addWorkflowRun($config['flow_id'],$wf_backflow,$config['wf_fid'],$config['wf_type']);//添加回退步骤流程
				//消息通知发起人
			}
			//日志记录
		} else if ($wf_actionid == "ssing") {//会签
			//日志记录
			$run_log = LogDb::AddrunLog($uid,$config['run_id'],$config,'SingSing');
			$sid = $this->AddSing($config);
			//发起新的会签
		} else { //通过
			throw new \Exception ("参数出错！");
		}
		
	}
	public function EndSing($sing_sign,$check_con)
	{
		$result = Db::execute('update leipi_run_sign set is_agree = 1,content="'.$check_con.'",dateline='.time().' where id = '.$sing_sign.' ');
		return $result;	
	}
	public function AddSing($config)
	{
		$data = [
			'run_id'=>$config['run_id'],
			'run_flow'=>$config['flow_id'],
			'run_flow_process'=>$config['run_flow_process'],
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
	 *结束工作流
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public function up_flow($run_id,$sid)
	{
		$result = Db::execute('update leipi_run set status = 0,is_sing = 1,sing_id='.$sid.',endtime='.time().' where id = '.$run_id.' ');
		return $result;	
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
}