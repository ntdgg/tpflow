<?php
/**
 *+------------------
 * Tpflow 普通提交工作流
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\service\command;

//数据库操作
use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Msg;
use tpflow\adaptive\Process;
use tpflow\adaptive\Log;
use tpflow\adaptive\Bill;
use tpflow\adaptive\Run;

class TaskFlow
{
	/**
	 * 任务执行
	 *
	 * @param array $config 参数信息
	 * @param mixed $uid 用户ID
	 */
	public function doTask($config, $uid)
	{
		//任务全局类
		$npid = $config['npid'];//下一步骤流程id
		$run_id = $config['run_id'];//运行中的id
		$run_process = $config['run_process'];//运行中的process
		if ($config['sup'] == '1') {
			$check_con = '[管理员代办]' . $config['check_con'];
			$config['check_con'] = '[管理员代办]' . $config['check_con'];
		} else {
			$check_con = $config['check_con'];
		}
		if (isset($config['todo'])) {
			$todo = $config['todo'];
		} else {
			$todo = '';
		}
		$data = Flow::getflowprocess($config['flow_process']);//获取设计器中的步骤信息
		/*
		 * 2021.05.26
		 * 协同模式
		 */
		$xt_runprocess = Run::FindRunProcess(['id'=>$run_process]);//查找协同步骤的信息
		if($xt_runprocess['auto_person']==2 && $xt_runprocess['sponsor_ids'] != ''){
            //超审时，跳过所有办理人结束当前步骤
            if($config['sup'] == '1') {
                $xt_text_val = "";
				$xt_ids_val = "";
            }else{
                $xt_ids = explode(",",$xt_runprocess['sponsor_ids']);
                $xt_text = explode(",",$xt_runprocess['sponsor_text']);
                foreach($xt_ids as $k=>$v){
                    if($v==$uid){
                        unset($xt_ids[$k]);
                        unset($xt_text[$k]);
                    }
                }
                $xt_text_val = implode(",", $xt_text);
                $xt_ids_val = implode(",", $xt_ids);
            }
			//更新流程，将办理人删除
			$up_process = Run::EditRunProcess(['id'=>$run_process],['sponsor_ids'=>$xt_ids_val,'sponsor_text'=>$xt_text_val,'updatetime'=>time()]);
			if (!$up_process) {
				return ['msg' => '更新运行步骤失败！', 'code' => '-1'];
			}
			//等于空说明协同步骤已经办理完成，需要把原办理流程人还回去
			if($xt_ids_val == '') {
				$up_process = Run::EditRunProcess(['id'=>$run_process],['sponsor_ids'=>$data['auto_xt_ids'],'sponsor_text'=>$data['auto_xt_text'],'updatetime'=>time()]);
				if (!$up_process) {
					return ['msg' => '更新运行步骤失败！', 'code' => '-1'];
				}
			}
			//如果协同字段等于空，说明已经办理完成，并且下一步骤的人员也是空直接结束该业务
			if($xt_ids_val =='' && $npid == ''){
                //补充日志记录
                $run_log = Log::AddrunLog($uid, $config['run_id'], $config, 'ok');
				if (!$run_log) {
					return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
				}
				Flow::end_flow($run_id);
				$end = Flow::end_process($run_process, $check_con);
				$bill_update = Bill::updatebill($config['wf_type'], $config['wf_fid'], 2);
				if (!$bill_update) {
					return ['msg' => '流程步骤操作记录失败，数据库错误！！！', 'code' => '-1'];
				}
				return ['msg' => 'success!', 'code' => '0'];
			}
			/*如果不等于空，则返回继续办理*/
			if($xt_ids_val != '') {
				//日志记录
				$run_log = Log::AddrunLog($uid, $config['run_id'], $config, 'ok');
				if (!$run_log) {
					return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
				}
				return ['msg' => 'success!', 'code' => '0'];
			}
		}
		if ($config['wf_mode'] == 2) {
			$info_list = Process::Getnorunprocess($config['run_id'], $config['run_process']);
			if (count($info_list) > 0) {
				foreach ($info_list as $k => $v) {
					$npids[] = $v['run_flow_process'];
				}
				$npid = implode(",", $npids);
			}
		}
		if ($npid != '') {//判断是否为最后
			//结束流程
			$end = Flow::end_process($run_process, $check_con);
			if (!$end) {
				return ['msg' => '结束流程错误！！！', 'code' => '-1'];
			}
			/*
			 * 2019年1月27日
			 * 同步模式下，只写入记录
			 */
			if ($config['wf_mode'] != 2) {
				/*加入判断是否是终止步骤*/
				$EndFlow = EndFlow::doTask($npid,$run_id);
				if($EndFlow==1){
					Flow::end_flow($run_id);//终止步骤
					$run_log = Log::AddrunLog($uid, $config['run_id'], $config, 'ok');//写入日志
					if (is_array($run_log) && $run_log['code']==-1) {
						return $run_log;
					}
					Bill::updatebill($config['wf_type'], $config['wf_fid'], 2);
                    Msg::find([['run_id','=',$run_id],['process_id','=',$config['flow_process']]]);//执行消息节点步骤信息
					return ['msg' => '审批完成，流程结束!', 'code' => '0'];
				}
				//更新单据信息
				Flow::up($run_id, $npid);
				//记录下一个流程->消息记录
				$this->Run($config, $uid, $todo);
			} else {
				//日志记录
				$run_log = Log::AddrunLog($uid, $config['run_id'], $config, 'ok');
				if (!$run_log) {
					return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
				}
			}
		} else {
			//结束该流程
			Flow::end_flow($run_id);
			$end = Flow::end_process($run_process, $check_con);
            //更新单据状态
            $bill_update = Bill::updatebill($config['wf_type'], $config['wf_fid'], 2);
            if (!$bill_update) {
                return ['msg' => '流程步骤操作记录失败，数据库错误！！！', 'code' => '-1'];
            }
			$run_log = Log::AddrunLog($uid, $run_id, $config, 'ok');
            if (is_array($run_log) && $run_log['code']==-1) {
                return $run_log;
            }
			if (!$end) {
				return ['msg' => '结束流程错误！！！', 'code' => '-1'];
			}
			//消息通知发起人
		}
        Msg::find([['run_id','=',$run_id],['process_id','=',$config['flow_process']]]);//执行消息节点步骤信息
		return ['msg' => 'success!', 'code' => '0'];
	}
	
	public function Run($config, $uid, $todo)
	{
        //日志记录
        $run_log = Log::AddrunLog($uid, $config['run_id'], $config, 'ok');
        if (!$run_log) {
            return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
        }
		$nex_pid = explode(",", $config['npid']);
		foreach ($nex_pid as $v) {
			$wf_process = Process::GetProcessInfo($v, $config['run_id']);
			//添加流程步骤日志
			$wf_process_log = Info::addWorkflowProcess($config['flow_id'], $wf_process, $config['run_id'], $uid, $todo);
		}
		if (!$wf_process_log) {
			return ['msg' => '流程步骤操作记录失败，数据库错误！！！', 'code' => '-1'];
		}

	}
}