<?php
/**
 *+------------------
 * Tpflow 核心控制器
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\service\method;

use tpflow\adaptive\Bill;
use tpflow\lib\unit;
use tpflow\lib\lib;
use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Process;
use tpflow\adaptive\Run;
use tpflow\adaptive\Log;
use tpflow\adaptive\Entrust;
use tpflow\adaptive\User;

use tpflow\service\TaskService;

class Jwt
{
	/**
	 * 工作流程统一接口
	 *
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * Info    获取流程信息
	 * start   发起审批流
	 * endflow 审批流终止
	 *
	 */
	function WfCenter($act, $wf_fid = '', $wf_type = '', $data = '', $post = '')
	{
		//获取流程信息
		if ($act == 'Info') {
			if ($wf_fid == '' || $wf_type == '') {
				return ['msg' => '单据编号，单据表不可为空！', 'code' => '-1'];
			}
			if ($data == 2) {
				return '';
			}
			$sup = $_GET['sup'] ?? '';
			$userinfo = ['uid' => unit::getuserinfo('uid'), 'role' => unit::getuserinfo('role')];
			return Info::workflowInfo($wf_fid, $wf_type, $userinfo,$sup);
		}
		//流程发起
		if ($act == 'start') {
			if ($data != '') {
				$flow = (new TaskService())->StartTask($data['wf_id'], $data['wf_fid'], $data['check_con'], unit::getuserinfo('uid'));
				if ($flow['code'] == 1) {
					return unit::msg_return('Success!');
				} else {
					return unit::msg_return($flow['msg'], -1);
				}
			}
            $flow = Flow::getWorkflowByType($wf_type);
            foreach($flow as $k=>$v){
                if($v['is_field']==1){
                    $field_value = Bill::getbillvalue($wf_type,$wf_fid,$v['field_name']);
                    if($field_value != $v['field_value']){
                        unset($flow[$k]);
                    }
                }
            }

			return ['wf_type' => $wf_type, 'wf_fid' => $wf_fid, 'Flow' => $flow];
		}
		//流程审批
		if ($act == 'do') {
			$urls = unit::gconfig('wf_url');
			$sup = $_GET['sup'] ?? '';
			$wf_op = $data['wf_op'];
			$info = [
				'wf_fid' => $wf_fid,
				'wf_type' => $wf_type,
				'wf_submit' => $data['submit'],
				'tpflow_ok' => $urls['wfdo'] . '?act=do&wf_op=ok&wf_type=' . $wf_type . '&wf_fid=' . $wf_fid . '&sup=' . $sup,
				'tpflow_back' => $urls['wfdo'] . '?act=do&wf_op=back&wf_type=' . $wf_type . '&wf_fid=' . $wf_fid . '&sup=' . $sup,
				'tpflow_sign' => $urls['wfdo'] . '?act=do&wf_op=sign&wf_type=' . $wf_type . '&wf_fid=' . $wf_fid . '&sup=' . $sup,
				'tpflow_flow' => $urls['wfdo'] . '?act=do&wf_op=flow&wf_type=' . $wf_type . '&wf_fid=' . $wf_fid . '&sup=' . $sup,
				'tpflow_upload' => unit::gconfig('wf_upload_file')
			];
			if ($wf_op == 'check') {
				return ['info' => $info, 'Flow' => self::WfCenter('Info', $wf_fid, $wf_type)];
			}
			if ($wf_op == 'ok') {
				if ($post != '') {
					$flowinfo = (new TaskService())->Runing($post, unit::getuserinfo('uid'));
					if ($flowinfo['code'] == '0') {
						return unit::msg_return('Success!');
					} else {
						return unit::msg_return($flowinfo['msg'], 1);
					}
				}
				return ['info' => $info, 'Flow' => self::WfCenter('Info', $wf_fid, $wf_type)];
			}
			if ($wf_op == 'back') {
				if ($post != '') {
					$post['btodo'] = Run::getprocessinfo($post['wf_backflow'], $post['run_id']);
					$flowinfo = (new TaskService())->Runing($post, unit::getuserinfo('uid'));
					if ($flowinfo['code'] == '0') {
						return unit::msg_return('Success!');
					} else {
						return unit::msg_return($flowinfo['msg'], 1);
					}
				}
				return ['info' => $info, 'Flow' => self::WfCenter('Info', $wf_fid, $wf_type)];
			}
			if ($wf_op == 'sign') {
				if ($post != '') {
					$flowinfo = (new TaskService())->Runing($post, unit::getuserinfo('uid'));
					if ($flowinfo['code'] == '0') {
						return unit::msg_return('Success!');
					} else {
						return unit::msg_return($flowinfo['msg'], 1);
					}
				}
				return ['info' => $info, 'Flow' => self::WfCenter('Info', $wf_fid, $wf_type), 'submit' => $data['ssing']];
			}
			//调用当前审批流的审批流程图
			if ($wf_op == 'flow') {
				$flowinfo = self::WfCenter('Info', $wf_fid, $wf_type);
				$run_info = Run::FindRunId($flowinfo['run_id']);
				$flow_id = intval($run_info['flow_id']);
				if ($flow_id <= 0) {
					
					return '参数有误，请返回重试!';
				}
				$one = Flow::getWorkflow($flow_id);
				if (!$one) {
					return '未找到数据，请返回重试!';
				}
				return ['Flow' => Flow::ProcessAll($flow_id)];
			}
		}
		//超级接口
		if ($act == 'endflow') {
			$data = (new TaskService())->EndTask(unit::getuserinfo('uid'), $data['bill_table'], $data['bill_id']);
			if ($data['code'] == '-1') {
				return unit::msg_return($data['msg'], 1);
			}
			return unit::msg_return('Success!');
		}
		if ($act == 'cancelflow') {
            if (is_object(unit::LoadClass($data['bill_table'], $data['bill_id']))) {
                $BillWork = (unit::LoadClass($data['bill_table'], $data['bill_id']))->cancel();
                if ($BillWork['code'] == -1) {
                    return $BillWork;
                }
            }
			$bill_update = Bill::updatebill($data['bill_table'], $data['bill_id'], 0);
			if (!$bill_update) {
				return unit::msg_return($data['msg'], 1);
			}
			return unit::msg_return('Success!');
		}
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 4.0统一接口 流程管理中心
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * welcome 调用版权声明接口
	 * check   调用逻辑检查接口
	 * add     新增步骤接口
	 * wfdesc  设计界面接口
	 * save    保存数据接口
	 * del     删除数据接口
	 * delAll  删除所有步骤接口
	 * att     调用步骤属性接口
	 * saveatt 保存步骤属性接口
	 */
	function WfFlowCenter($act, $data = '')
	{
		if ($act == 'index') {
			return ['Url' => unit::gconfig('wf_url'), 'Type' => Info::get_wftype(), 'List' => Flow::GetFlow()];
		}
		if ($act == 'wfjk') {
			$data = Info::worklist();
			foreach ($data as $k => $v) {
				$data[$k]['btn'] = lib::tpflow_btn($v['from_id'], $v['from_table'], 100, self::WfCenter('Info', $v['from_id'], $v['from_table']));
			}
			return ['Url' => unit::gconfig('wf_url'), 'List' => $data];
		}
		if ($act == 'wfend') {
			return (new TaskService())->doSupEnd($data, unit::getuserinfo('uid'));
		}
		if ($act == 'add') {
			if ($data != '' && !is_numeric($data)) {
				if ($data['id'] == '') {
					$data['uid'] = unit::getuserinfo('uid');
					$data['add_time'] = time();
					unset($data['id']);
					$ret = Flow::AddFlow($data);
				} else {
					$ret = Flow::EditFlow($data);
				}
				if ($ret['code'] == 0) {
					return unit::msg_return('操作成功！');
				} else {
					return unit::msg_return($ret['data'], 1);
				}
			}
			return ['Url' => unit::gconfig('wf_url'), 'Type' => Info::get_wftype(), 'Info' => Flow::getWorkflow($data)];
		}
        if ($act == 'verUpdate') {
            Flow::verUpdate();
            return unit::msg_return('版本更新成功！');
        }
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 4.0 工作流代理接口
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * index 列表调用
	 * add   添加代理授权
	 */
	function WfEntrustCenter($act, $data = '')
	{
		if ($act == 'index') {
			return ['List' => Entrust::lists()];
		}
		if ($act == 'add') {
			if ($data != '' && !is_numeric($data)) {
				$ret = Entrust::Add($data);
				if ($ret['code'] == 0) {
					return unit::msg_return('发布成功！');
				} else {
					return unit::msg_return($ret['data'], 1);
				}
			}
			return ['Info' => Entrust::find($data), 'Type' => Process::get_userprocess(unit::getuserinfo('uid'), unit::getuserinfo('role')), 'user' => User::GetUser()];
		}
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 4.0统一接口设计器
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * welcome 调用版权声明接口
	 * check   调用逻辑检查接口
	 * add     新增步骤接口
	 * wfdesc  设计界面接口
	 * save    保存数据接口
	 * del     删除数据接口
	 * delAll  删除所有步骤接口
	 * att     调用步骤属性接口
	 * saveatt 保存步骤属性接口
	 * super_user 用户选择控件
	 */
	function WfDescCenter($act, $flow_id = '', $data = '')
	{
		$urls = unit::gconfig('wf_url');
		//流程添加，编辑，查看，删除
		if ($act == 'welcome') {
			return '<br/><br/><style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; }h1{ font-size: 40px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 35px }</style><div style="padding: 24px 48px;"> <h1>\﻿ (•◡•) / </h1><p> TpFlow V5.0正式版<br/><span style="font-size:19px;">PHP开源工作流引擎系统</span></p><span style="font-size:15px;">[ ©2018-2020 Guoguo <a href="https://www.cojz8.com/">TpFlow</a> 本版权不可删除！ ]</span></div>';
		}
		if ($act == 'wfdesc') {
			$one = Flow::getWorkflow($flow_id);
			if (!$one) {
				return '未找到数据，请返回重试!';
			}
			return ['id' => $one['id'], 'FlowInfo' => Flow::ProcessAll($flow_id), 'Url' => $urls['designapi']];
		}
		if ($act == 'save') {
			return Flow::ProcessLink($flow_id, $data);
		}
		if ($act == 'check') {
			return Flow::CheckFlow($flow_id);
		}
		if ($act == 'add') {
			$one = Flow::getWorkflow($flow_id);
			if (!$one) {
				return ['status' => 0, 'msg' => '添加失败,未找到流程', 'info' => ''];
			}
			return Flow::ProcessAdd($flow_id, $data);
		}
		if ($act == 'delAll') {
			return Flow::ProcessDelAll($flow_id);
		}
		if ($act == 'del') {
			return Flow::ProcessDel($flow_id, $data);
		}
		if ($act == 'saveatt') {
			return Flow::ProcessAttSave($data);
		}
		if ($act == 'att') {
			$info = Flow::ProcessAttView($data);
			return ['info' => $info];
		}
		if ($act == 'super_user') {
			if ($data['type_mode'] == 'user') {
				return ['Url' => $urls['designapi'], 'kid' => $data['kid'], 'User' => User::GetUser()];
				
			} elseif ($data['type_mode'] == 'role') {
				$info = User::GetRole();
				$user = '';
				foreach ($info as $k => $v) {
					$user .= '<option value="' . $v['id'] . '">' . $v['username'] . '</option>';
				}
				return ['Url' => $urls['designapi'], 'kid' => 'auto_role', 'User' => User::GetRole()];
			} else {
				return ['data' => User::AjaxGet(trim($data['type']), $data['key']), 'code' => 1, 'msg' => '查询成功！'];
			}
		}
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 4.0统一接口
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * log  历史日志消息
	 * btn  权限判断
	 * status  状态判断
	 */
	function wfAccess($act, $data = '')
	{
		if ($act == 'log') {
			return Log::FlowLog($data['id'], $data['type'], 'Json');
		}
		if ($act == 'btn') {
			$btn = Lib::tpflow_btn($data['id'], $data['type'], $data['status'], self::WfCenter('Info', $data['id'], $data['type'], $data['status']), 1);
			return $btn;
		}
		if ($act == 'status') {
			return Lib::tpflow_status($data['status'], 1);
		}
		return $act . '参数出错';
	}
	
}