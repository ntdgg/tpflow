<?php
/**
 *+------------------
 * Tpflow 流信息处理
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;
use tpflow\service\command\AutoFlow;
use tpflow\service\command\AiFlow;

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
		if ($wf_process['auto_person'] == 6 && $wf_process['process_type'] == 'node-start') { //事务人员
			$wf = Run::FindRunId($run_id);
			$user_id = Bill::getbillvalue($wf['from_table'], $wf['from_id'], $wf_process['work_text']);
            //人员
            if($wf_process['work_ids']==1){
                $user_info = User::GetUserInfo($user_id);
                $wf_process['user_info'] = $user_info;
                $wf_process['todo'] = $user_info['username'];
            }
            //角色
            if($wf_process['work_ids']==2){
                $user_info = User::GetRoleInfo($user_id);
                $wf_process['user_info'] = $user_info;
                $wf_process['todo'] = $user_info['username'];
            }
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
            if ($wf_process['auto_person'] == 7) { //办理人上一级角色
                $wfrun = Run::FindRunId($run_id);//运行步骤信息
                $uid = Bill::getbillvalue($wfrun['from_table'], $wfrun['from_id'],'uid');//找出发起人的步骤信息//(unit::gconfig('user_role'))[1]
                $role_info = User::GetRoleInfoByuid($uid);
                $sponsor_text = $role_info['username'];
                $sponsor_ids = $role_info['id'];
            }
            if ($wf_process['auto_person'] == 8) {
                $sponsor_ids = 0;
                $sponsor_text = 'Ai审核专家';
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
            'word_type'=>$wf_process['work_ids'],
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
            (new $msg_api())->send($process_id);;
		}
        /*2021-12-26 加入自动执行判断*/
        $doAuto = (new AutoFlow())->doAuto($wf_process['id'],$run_id,$process_id);
        if($doAuto['code']!=0){
            return false;
        }
        /*2025-02-27 加入Ai审核专家*/
        $aiFlow = (new AiFlow())->doAuto($wf_process['id'],$run_id,$process_id);

		return $process_id;
	}
	
	/**
	 * 根据单据ID，单据表 获取流程信息
	 *
	 * @param int $run_id 运行的id
	 * @param string $wf_type 业务表名
	 */
	public static function workflowInfo($wf_fid, $wf_type, $userinfo,$sup=0)
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
            if(empty($info_list[0]) && $result['is_sing']==0){
                return [];
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
						if(!empty(array_intersect((array)$userinfo['role'], $uids))){// Guoke 2021/11/26 13:30 扩展多多用户组的支持
							$info = $v;
							break;
						}
					}
				}
				if($sup==1){
                    $info = $info_list[0];
                }else{
                    if (!isset($info)) {
                        return -1;
                    }
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
					$workflow ['nexprocess'] = Process::GetNexProcessInfo($wf_type, $wf_fid, $info['run_flow_process'], $result['id']);//获取当前原设计下一个步骤

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

				//0 直线 1转出 2同步 3自由
				if ($workflow['status']['wf_mode'] != 2) {
                    /*自由模式*/
                    if ($workflow['status']['wf_mode'] == 3) {
                        $workflow['nexid'] = '-1';
                    }else{
                        if(($workflow ['nexprocess']['process_type'] ?? '')=='node-end'){
                            $workflow['nexid'] = '';//终点节点，直接结束步骤
                        }else{
                            $workflow['nexid'] = $workflow ['nexprocess']['id'];//下一步骤
                        }
                    }
				} else {
					$workflow['nexid'] = $workflow ['process']['process_to'];//下一步骤
				}
				$workflow['run_process'] = $info['id'];//运行的run_process步骤ID
                $wf_flow = Flow::GetFlowInfo($info['run_flow']);
                $workflow['is_signature'] = $wf_flow['is_signature'];//运行的run_process步骤ID

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
            $wf_action = $process[0]['wf_action'] ?? '';
            if($wf_action!=''){
                if (strpos($wf_action, '@') !== false) {
                    $urldata = explode("@", $wf_action);
                    $url = url(unit::gconfig('int_url') . '/' . $urldata[0] . '/' . $urldata[1], ['id' => $v['from_id'], $urldata[2] => $urldata[3]]).($urldata[4] ?? '');
                }else if(strpos($flowinfo['status']['wf_action'], '%') !== false){
                    //增加了自定义网址
                    $url = str_replace("%", "", $wf_action).$v['from_id'];
                } else {
                    if (strpos($flowinfo['status']['wf_action'], '/') !== false) {
                        $url = url(unit::gconfig('int_url') . '/' . $wf_action, ['id' => $v['from_id']]);
                    }else{
                        $url = url(unit::gconfig('int_url') . '/' . $v['from_table'] . '/' . $wf_action, ['id' => $v['from_id']]);
                    }
                }
            }else{
                $url = '';
            }
			$sponsor_text = '';
			foreach ($process as $p => $s) {
				$sponsor_text .= $s['sponsor_text'] . ',';
			}
            $strSubject = $Flow['tmp'];
            $strPattern = "/(?<=【)[^】]+/";
            $arrMatches = [];
            preg_match_all($strPattern, $strSubject, $arrMatches);
            foreach($arrMatches[0] as $k1 => $v1){// Guoke 2021/11/25 17:08 官方BUG
                //*增加模板变量*//
                if (strpos($v1, '@') !== false) {
                    $v1_array = explode("@", $v1);
					$v1_value = $bill_info[$v1_array[0]] ?? '';
                    if($v1_value==''){
                         $v1_rvalue = '';
                    }else{
                         $v1_rvalue = Bill::getbillvalue($v1_array[1],$v1_value,$v1_array[2]) ?? ' sys field err ';
                    }
                    $strSubject = str_ireplace(['【' . $v1 . '】'], [$v1_rvalue], $strSubject);
                }else{
                    $strSubject = str_ireplace(['【' . $v1 . '】'], [($bill_info[$v1] ?? ' sys field err ')], $strSubject);
                }
            }
            $result[$k]['tmp'] =$strSubject;
            $result[$k]['url'] =$url;
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