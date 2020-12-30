<?php
/**
*+------------------
* Tpflow 工作流任务服务驱动
*+------------------ 
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------
*/

namespace tpflow\service;

use tpflow\service\command\TaskFlow;
use tpflow\service\command\BackFlow;
use tpflow\service\command\SingFlow;
use tpflow\service\command\SupFlow;

use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Process;
use tpflow\lib\unit;
use tpflow\adaptive\Bill;
use tpflow\adaptive\Run;
use tpflow\adaptive\Log;

class TaskService{
	/**
	 * 发起工作流
	 *
	 **/
	public function StartTask($wf_id,$wf_fid,$check_con,$uid){
		if ($wf_id == '' || $wf_fid == ''|| $uid == '') {
				return ['msg'=>'参数不完整！','code'=>'-1'];
			}
			//判断流程是否存在
			$wf = Flow::getWorkflow($wf_id);
			if(!$wf){
				return ['msg'=>'未找到工作流！','code'=>'-1'];
			}
			$wf_type  = $wf['type'];
			//判断单据是否存在
			$getbill = Bill::getbill($wf_type,$wf_fid);
			if(!$getbill){
				return ['msg'=>'单据不存在！','code'=>'-1'];
			}
			//根据流程获取流程第一个步骤
			$wf_process = Process::getWorkflowProcess($wf_id);
			if(!$wf_process){
				return ['msg'=>'流程设计出错，未找到第一步流程，请联系管理员！','code'=>'-1'];
			}
			//加入流程步骤判断
			$BillWork = (unit::LoadClass($wf_type,$wf_fid))->before('Start');
			if(!$BillWork){
				return $BillWork;
			}
			//满足要求，发起流程
			$wf_run = Info::addWorkflowRun($wf_id,$wf_process['id'],$wf_fid,$wf_type,$uid);
			if(!$wf_run){
				return ['msg'=>'流程发起失败，数据库操作错误！！','code'=>'-1'];
			}
			//添加流程步骤日志
			$wf_process_log = Info::addWorkflowProcess($wf_id,$wf_process,$wf_run,$uid);
			if(!$wf_process_log){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
			//更新单据状态
			$bill_update = Bill::updatebill($wf_type,$wf_fid,1);
			if(!$bill_update){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
			$run_log = Log::AddrunLog($uid,$wf_run,['wf_id'=>$wf_id,'wf_fid'=>$wf_fid,'wf_type'=>$wf_type,'check_con'=>$check_con],'Send');
			return ['run_id'=>$wf_run,'msg'=>'success','code'=>'1'];
	}
	/*
	 * 工作流运行服务
	 *
	 * @param array $config 参数信息
	 * @param mixed  $uid 用户ID
	 **/
	function Runing($config,$uid)
	{
		if( @$config['run_id']=='' || @$config['run_process']==''){
			throw new \Exception ( "config参数信息不全！" );
		}
		$run = Run::FindRunId($config['run_id']);//根据运行步骤找出相对应的运行信息
		$config['flow_id'] = $run['flow_id'];//带出流程id
		$config['wf_fid'] = $run['from_id'];//带出业务ID
		$config['wf_type'] = $run['from_table'];//带出业务表名
		$config['sing_st'] = $run['is_sing'];//业务是否为会签模式
		$wf_actionid = $config['submit_to_save'];
		//用户提交审批前的校验
		$BillWork = (unit::LoadClass($config['wf_type'],$config['wf_fid'],$config['run_id'],$config))->before($wf_actionid);
		if(!$BillWork){
			return $BillWork;
		}
		if($config['sing_st'] == 0){
			$run_check = Process::run_check($config['run_process']);//校验流程状态
			if($run_check==2){
				return ['msg'=>'该业务已办理，请勿重复提交！','code'=>'-1'];
			}
			if ($wf_actionid == "ok") {//提交处理
				$ret = $this->doTask($config,$uid);
			} else if ($wf_actionid == "back") {//退回处理
				$ret = $this->doBack($config,$uid);
			} else if ($wf_actionid == "sing") {//会签
				$ret = $this->doSing($config,$uid);
			} else { //通过
				throw new \Exception ( "参数出错！" );
			}
		}else{
			$ret = $this->doSingEnt($config,$uid,$wf_actionid);
		}
		return $ret;
	}
	/**
	 * 结束流程接口
	 * @param string $bill_table 表名称
	 * @param mixed $bill_id id
	 */
	public function EndTask($user_id,$bill_table,$bill_id){
		//终止权限校验
		//1、所有人均可终止；2、单据发起人可以终止；3、指定uid可以终止；
		$wf_access_control = unit::gconfig('wf_access_control');
		$wf_access_control_uid = unit::gconfig('wf_access_control_uid');
		if($wf_access_control==2){
			$log = Log::RunLog($bill_id,$bill_table);//读取到log记录
			foreach($log as $k=>$v){
				if($v['btn']=='流程发起'){
					$access_uid = $v['uid'];
					break;
				}
			}
			if($user_id != $access_uid){
				return ['msg'=>'对不起您没有结束流程的权限~','code'=>'-1'];
			}
		}
		if($wf_access_control==3){
			if(!in_array($user_id,$wf_access_control_uid)){
				return ['msg'=>'对不起您没有结束流程的权限~','code'=>'-1'];
			}
		}
		//权限判断结束
		//终止流程及步骤
		$findwhere = [['from_id','=',$bill_id],['from_table','=',$bill_table]];
		$FindRun = Run::FindRun($findwhere);
		if(!$FindRun){
			return ['msg'=>'没有找到流程~','code'=>'-1'];
		}
		$end_flow = Flow::end_flow($FindRun['id']);
		if(!$end_flow){
			return ['msg'=>'结束流程失败~','code'=>'-1'];
		}
		$updatebill = Bill::updatebill($bill_table,$bill_id,0);
		if(!$end_flow){
			return ['msg'=>'更新单据信息出错~','code'=>'-1'];
		}
		return ['msg'=>'终止成功~','code'=>0];
	}
	/**
	 * 普通流程通过
	 * 
	 * @param  $config 参数信息
	 * @param  mixed $uid  用户ID
	 */
	public function doTask($config,$uid){
		$command = new TaskFlow();
		return $command->doTask($config,$uid);
	}
	/**
	 * 流程驳回
	 * 
	 * @param  Array $config 参数信息
	 * @param  mixed $uid  用户ID
	 */
	public function doBack($config,$uid){
		$command = new BackFlow();
		return $command->doTask($config,$uid);
	}
	/**
	 * 会签操作
	 * 
	 * @param Array $config 参数信息
	 * @param mixed $uid  用户ID
	 */
	public function doSing($config,$uid){
		$command = new SingFlow();
		return $command->doTask($config,$uid);
	}
	
	/**
	 * 普通流程通过
	 * 
	 * @param array $config 参数信息
	 * @param mixed $uid  用户ID
	 */
	public function doSingEnt($config,$uid,$wf_actionid){
		$command = new SingFlow();
		return $command->doSingEnt($config,$uid,$wf_actionid);
	}
	/**
	 * 实例超级接口
	 * 
	 * @param  string  $wfid 工作流ID run_id
	 * @param  mixed  $uid  用户ID
	 */
	public function doSupEnd($wfid,$uid){
		$command = new SupFlow();
		return $command->doSupEnd($wfid,$uid);
	}
}