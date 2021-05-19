<?php
/**
 *+------------------
 * Tpflow 普通提交工作流
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\service\command;

//数据库操作
use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Process;
use tpflow\adaptive\Log;
use tpflow\adaptive\Bill;

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
			Log::AddrunLog($uid, $run_id, $config, 'ok');
			if (!$end) {
				return ['msg' => '结束流程错误！！！', 'code' => '-1'];
			}


			//消息通知发起人
		}
		return ['msg' => 'success!', 'code' => '0'];
	}
	
	public function Run($config, $uid, $todo)
	{
		$nex_pid = explode(",", $config['npid']);
		foreach ($nex_pid as $v) {
			$wf_process = Process::GetProcessInfo($v, $config['run_id']);
			//添加流程步骤日志
			$wf_process_log = Info::addWorkflowProcess($config['flow_id'], $wf_process, $config['run_id'], $uid, $todo);
		}
		if (!$wf_process_log) {
			return ['msg' => '流程步骤操作记录失败，数据库错误！！！', 'code' => '-1'];
		}
		//日志记录
		$run_log = Log::AddrunLog($uid, $config['run_id'], $config, 'ok');
		if (!$run_log) {
			return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
		}
	}
}