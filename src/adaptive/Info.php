<?php
/**
 *+------------------
 * Tpflow 流信息处理
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Info
{
	
	protected $mode;
	
	public function __construct()
	{
		if (unit::gconfig('wf_db_mode') == 1) {
			$className = '\\tpflow\\custom\\think\\AdapteeInfo';
		} else {
			$className = unit::gconfig('wf_db_namespace') . 'AdapteeInfo';
		}
		$this->mode = new $className();
	}
	
	/**
	 * 添加工作流
	 *
	 * @param int $wf_id 流程主ID
	 * @param int $wf_process 流程信息
	 * @param int $wf_fid 业务id
	 * @param string $wf_type 业务表名
	 */
	public static function addWorkflowRun($wf_id, $wf_process, $wf_fid, $wf_type, $uid)
	{
		$data = array(
			'uid' => $uid,
			'flow_id' => $wf_id,
			'from_table' => $wf_type,
			'from_id' => $wf_fid,
			'run_flow_id' => $wf_id,
			'run_flow_process' => $wf_process,
			'dateline' => time(),
		);
		$run_id = Run::AddRun($data);
		if (!$run_id) {
			return false;
		}
		return $run_id;
	}
	
	/**
	 * 添加运行步骤信息
	 *
	 * @param int $wf_id 流程主ID
	 * @param int $wf_process 流程信息
	 * @param int $wf_fid 业务id
	 * @param string $wf_type 业务表名
	 */
	public static function addWorkflowProcess($wf_id, $wf_process, $run_id, $uid, $todo = '')
	{
		if ($wf_process['auto_person'] == 6 && $wf_process['process_type'] == 'is_one') { //事务人员
			$wf = Run::FindRunId($run_id);
			$user_id = Bill::getbillvalue($wf['from_table'], $wf['from_id'], $wf_process['work_text']);
			$user_info = User::GetUserInfo($user_id);
			$wf_process['user_info'] = $user_info;
			$wf_process['todo'] = $user_info['username'];
		}
		//非自由
		if ($todo == '') {
			if ($wf_process['auto_person'] == 2) {
				$sponsor_ids = $wf_process['auto_xt_ids'];
				$sponsor_text = $wf_process['auto_xt_text'];
			}
			if ($wf_process['auto_person'] == 3) { //办理人员
				$sponsor_ids = $wf_process['range_user_ids'];
				$sponsor_text = $wf_process['range_user_text'];
			}
			if ($wf_process['auto_person'] == 4) { //办理人员
				$sponsor_ids = $wf_process['auto_sponsor_ids'];
				$sponsor_text = $wf_process['auto_sponsor_text'];
			}
			if ($wf_process['auto_person'] == 5) { //办理角色
				$sponsor_text = $wf_process['auto_role_text'];
				$sponsor_ids = $wf_process['auto_role_ids'];
			}
			if ($wf_process['auto_person'] == 6) { //事务接收者 2020年1月17日15:28:37
				$sponsor_text = $wf_process['user_info']['username'];
				$sponsor_ids = $wf_process['user_info']['id'];
			}
		} else {
			$todo = explode("*%*", $todo);
			$sponsor_text = $todo[1];
			$sponsor_ids = $todo[0];
		}
		$data = array(
			'uid' => $uid,
			'run_id' => $run_id,
			'run_flow' => $wf_id,
			'run_flow_process' => $wf_process['id'],
			'remark' => '',
			'status' => 0,
			'sponsor_ids' => $sponsor_ids,//办理人id
			'sponsor_text' => $sponsor_text,//办理人信息
			'auto_person' => $wf_process['auto_person'],//办理类别
			'js_time' => time(),
			'dateline' => time(),
			'is_sing' => $wf_process['is_sing'],
			'is_back' => $wf_process['is_back'],
			'wf_mode' => $wf_process['wf_mode'],
			'wf_action' => $wf_process['wf_action'],
		);
		$process_id = Run::AddRunProcess($data);
		//如果是角色办理，将角色ID转化为用户ID
		if ($wf_process['auto_person'] == 5) {
			$sponsor_ids = '';
		}
		//取出当前所有授权信息
		$map[] = ['old_user', 'in', [$sponsor_ids]];
		$Raw = 'flow_process = 0 or flow_process=' . $wf_process['id'];
		$all_Entrust = Entrust::get_Entrust($map, $Raw);
		if (count($all_Entrust) > 0) {
			//写入授权表
			Entrust::save_rel($all_Entrust, $process_id);
		}
		if (!$process_id) {
			return false;
		}
		//加入消息接口Api
		$msg_api = unit::gconfig('msg_api') ?? '';
		if (class_exists($msg_api)) {
			$user_info = (new $msg_api())->send($process_id);;
		}
		return $process_id;
	}
	
	/**
	 * 根据单据ID，单据表 获取流程信息
	 *
	 * @param int $run_id 运行的id
	 * @param string $wf_type 业务表名
	 */
	public static function workflowInfo($wf_fid, $wf_type, $userinfo)
	{
		$workflow = [];
		//根据表信息，判断当前流程是否还在运行  
		$findwhere = [['from_id', '=', $wf_fid], ['from_table', '=', $wf_type], ['is_del', '=', 0], ['status', '=', 0]];
		$count = Run::SearchRun($findwhere);//查询运行的流程
		if (count($count) > 0) {
			//查询运行中的步骤信息
			$result = $count[0];
			$info_list = Run::SearchRunProcess([['run_id', '=', $result['id']], ['run_flow_process', 'in', $result['run_flow_process']], ['status', '=', 0]]);
			if (count($info_list) == 0) {
				$info_list[0] = Run::FindRunProcess([['run_id', '=', $result['id']], ['run_flow_process', '=', $result['run_flow_process']], ['status', '=', 0]]);
			}
			/*
			 * 2019年1月27日
			 *1、先计算当前流程下有几个步骤 2、如果有多个步骤，判定为同步模式，（特别注意，同步模式下最后一个步骤，也会认定会是单一步骤） 3、根据多个步骤进行循环，找出当前登入用户对应的步骤 4、将对应的步骤设置为当前审批步骤 5、修改下一步骤处理模式 6、修改提醒模式
			 */
			//如果有两个以上的运行步骤，则认定为是同步模式
			if (count($info_list) < 2) {
				$info = $info_list[0];
				$workflow ['wf_mode'] = 0;//wf_mode
			} else {
				$workflow ['wf_mode'] = 2;//同步模式
				foreach ($info_list as $k => $v) {
					if ($v['auto_person'] == 4 || $v['auto_person'] == 3) {
						$uids = explode(",", $v['sponsor_ids']);
						if (in_array($userinfo['uid'], $uids)) {
							$info = $v;
							break;
						}
					} else {
						$uids = explode(",", $v['sponsor_ids']);
						if (in_array($userinfo['role'], $uids)) {
							$info = $v;
							break;
						}
					}
				}
				if (!isset($info)) {
					return -1;
				}
			}
			
			//4.0版本新增查找是否有代理审核人员，并给与权限，权限转换
			$info = Entrust::change($info);
			
			//拼接返回数据
			if ($result) {
				if ($result['is_sing'] != 1) {
					$workflow ['sing_st'] = 0;//会签模式 
					$workflow ['run_id'] = $result['id'];
					$workflow ['status'] = $info;
					$workflow ['flow_process'] = $info['run_flow_process'] ?? '';//运行的flow_processid
					$workflow ['process'] = Process::GetProcessInfo($info['run_flow_process'], $result['id']);//读取当前原设计步骤的详细信息
					$workflow ['nexprocess'] = Process::GetNexProcessInfo($wf_type, $wf_fid, $info['run_flow_process'], $result['id'], $workflow ['wf_mode']);//获取当前原设计下一个步骤
				} else {
					$info = Run::FindRunProcess([['run_id', '=', $result['id']], ['run_flow', '=', $result['flow_id']], ['run_flow_process', '=', $result['run_flow_process']]]);
					$workflow ['sing_st'] = 1;
					$workflow ['run_id'] = $result['id'];
					$workflow ['status'] = $info;
					$workflow ['flow_process'] = $result['run_flow_process'];
					$process = Process::GetProcessInfo($result['run_flow_process'], $result['id']);
					$workflow ['status']['wf_mode'] = $process['wf_mode'];
					$workflow ['status']['wf_action'] = $process['wf_action'];
					$workflow ['nexprocess'] = Process::GetNexProcessInfo($wf_type, $wf_fid, $result['run_flow_process'], $result['id']);
					$workflow ['process'] = $process;
					$workflow ['sing_info'] = Run::FindRunSign([['id', '=', $result['sing_id']]]);
				}
				
				if ($workflow['status']['wf_mode'] != 2) {
					$workflow['nexid'] = $workflow ['nexprocess']['id'];//下一步骤
				} else {
					$workflow['nexid'] = $workflow ['process']['process_to'];//下一步骤
				}
				
				$workflow['run_process'] = $info['id'];//运行的run_process步骤ID
				$workflow['npi'] = unit::nexnexprocessinfo($workflow['status']['wf_mode'], $workflow['nexprocess']);//显示下一步骤的信息
			}
		}
		
		return $workflow;
	}
	
	/**
	 * 根据单据ID，单据表 获取流程信息
	 *
	 * @param int $run_id 运行的id
	 * @param string $wf_type 业务表名
	 */
	public static function workrunInfo($run_id)
	{
		return Run::FindRunId($run_id);
	}
	
	/**
	 * 工作流列表
	 *
	 */
	public static function worklist()
	{
		$result = Run::SearchRun([['status', '=', 0]]);
		foreach ($result as $k => $v) {
		    $Flow =  Flow::GetFlowInfo($v['flow_id']);
            $bill_info = Bill::getbill($v['from_table'],$v['from_id']);
			$result[$k]['flow_name'] = $Flow['flow_name'];
			$process = Run::SearchRunProcess([['run_id', '=', $v['id']], ['run_flow_process', '=', $v['run_flow_process']]]);
			$sponsor_text = '';
			foreach ($process as $p => $s) {
				$sponsor_text .= $s['sponsor_text'] . ',';
			}
            $strSubject = $Flow['tmp'];
            $strPattern = "/(?<=【)[^】]+/";
            $arrMatches = [];
            preg_match_all($strPattern, $strSubject, $arrMatches);
            foreach($arrMatches[0] as $k1 => $v1){// Guoke 2021/11/25 17:08 官方BUG
                $strSubject = str_ireplace(['【' . $v1 . '】'], [($bill_info[$v1] ?? ' sys field err ')], $strSubject);
            }
            $result[$k]['tmp'] =$strSubject;
			$result[$k]['user'] = rtrim($sponsor_text, ",");
		}
		return $result;
	}
	
	/**
	 * 接入工作流的类别
	 *
	 */
	public static function get_wftype()
	{
		return (new Info())->mode->get_wftype();
	}
}