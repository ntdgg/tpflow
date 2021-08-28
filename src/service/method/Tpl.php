<?php
/**
 *+------------------
 * Tpflow 核心控制器
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\service\method;

use tpflow\adaptive\Event;
use tpflow\lib\unit;
use tpflow\lib\lib;

use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Process;
use tpflow\adaptive\Run;
use tpflow\adaptive\Log;
use tpflow\adaptive\Entrust;
use tpflow\adaptive\User;
use tpflow\adaptive\Bill;

use tpflow\service\TaskService;


class Tpl
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
			$userinfo = ['uid' => unit::getuserinfo('uid'), 'role' => unit::getuserinfo('role')];
			return Info::workflowInfo($wf_fid, $wf_type, $userinfo);
		}
		//流程发起
		if ($act == 'start') {
			if ($data != '') {
				$flow = (new TaskService())->StartTask($data['wf_id'], $data['wf_fid'], $data['check_con'], unit::getuserinfo('uid'));
				if ($flow['code'] == 1) {
					return unit::msg_return('Success!');
				} else {
					return unit::msg_return($flow['msg'], 1);
				}
			}
			$flow = Flow::getWorkflowByType($wf_type);
			//20210508 新增权限过滤
			foreach($flow as $k=>$v){
			    if($v['is_field']==1){
                    $field_value = Bill::getbillvalue($wf_type,$wf_fid,$v['field_name']);
                    if($field_value != $v['field_value']){
                        unset($flow[$k]);
                    }
                }
            }
			$op = '';
			foreach ($flow as $k => $v) {
				$op .= '<option value="' . $v['id'] . '">' . $v['flow_name'] . '</option>';
			}
			return lib::tmp_wfstart(['wf_type' => $wf_type, 'wf_fid' => $wf_fid], $op);
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
				'tpflow_log' => $urls['wfdo'] . '?act=do&wf_op=log&wf_type=' . $wf_type . '&wf_fid=' . $wf_fid . '&sup=' . $sup,
				'tpflow_upload' => unit::gconfig('wf_upload_file')
			];
			if ($wf_op == 'check') {
				return lib::tmp_check($info, self::WfCenter('Info', $wf_fid, $wf_type));
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
				return lib::tmp_wfok($info, self::WfCenter('Info', $wf_fid, $wf_type));
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
				return lib::tmp_wfback($info, self::WfCenter('Info', $wf_fid, $wf_type));
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
				return lib::tmp_wfsign($info, self::WfCenter('Info', $wf_fid, $wf_type), $data['ssing']);
			}
			//调用当前审批流的审批流程图
			if ($wf_op == 'flow') {
				$flowinfo = self::WfCenter('Info', $wf_fid, $wf_type);
				$run_info = Run::FindRunId($flowinfo['run_id']);
				$flow_id = intval($run_info['flow_id']);
				if ($flow_id <= 0) {
					return unit::msg_return('参数有误，请返回重试!', 1);
				}
				$one = Flow::getWorkflow($flow_id);
				if (!$one) {
					return unit::msg_return('参数有误，请返回重试!', 1);
				}
				return lib::tmp_wfflow(Flow::ProcessAll($flow_id));
			}
			if ($wf_op == 'log') {
				return Log::FlowLog($wf_fid, $wf_type);
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
	 * Tpflow 5.0统一接口 流程管理中心
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
		$urls = unit::gconfig('wf_url');
		if ($act == 'index') {
			$type = [];
			foreach (Info::get_wftype() as $k => $v) {
				$type[$v['name']] = str_replace('[work]', '', $v['title']);;
			}
			$html = '';
			foreach ($type as $k => $v) {
				$html .= '<li>┣' . $k . '-' . $v . '</li>';
			}
			$data = Flow::GetFlow();
			$tr = '';
			foreach ($data['rows'] as $k => $v) {
				$status = ['正常', '禁用'];
				if ($v['edit'] == '') {
					$url_edit = $urls['wfapi'] . '?act=add&id=' . $v['id'];
					$url_desc = $urls['designapi'] . '?act=wfdesc&flow_id=' . $v['id'];
					$btn = "<a class='button' onclick=Tpflow.lopen('修改','" . $url_edit . "','55','60')> 修改</a> <a class='button' onclick=Tpflow.lopen('设计','" . $url_desc . "',100,100)> 设计</a> ";
				} else {
					$btn = "<a class='btn  radius size-S'> 运行中....</a>";
				}
				if ($v['status'] == 0) {
					$btn .= "<a class='button' onclick=Tpflow.wfconfirm('" . $urls['wfapi'] . '?act=add' . "',{'id':" . $v['id'] . ",'status':1},'您确定要禁用该工作流吗？')> 禁用</a>";
				} else {
					$btn .= "<a class='button' onclick=Tpflow.wfconfirm('" . $urls['wfapi'] . '?act=add' . "',{'id':" . $v['id'] . ",'status':0},'您确定要启用该工作流吗？')> 启用</a>";
				}
				$tr .= '<tr><td>' . $v['id'] . '</td><td>' . $v['flow_name'] . '</td><td>' .($type[$v['type']] ?? 'Err') . '</td><td>' . date('Y/m/d H:i', $v['add_time']) . '</td><td>' . $status[$v['status']] . '</td><td>' . $btn . '</td></tr>';
			}
			return lib::tmp_index($urls['wfapi'] . '?act=add', $tr, $html);
		}
		if ($act == 'wfjk') {
			$data = Info::worklist();
			$html = '';
			foreach ($data as $k => $v) {
				$status = ['未审核', '已审核'];
				
				$html .= '<tr class="text-c"><td>' . $v['id'] . '</td><td>' . $v['from_table'] . '</td><td>' . $v['flow_name'] . '</td><td>' . $status[$v['status']] . '</td><td>' . $v['flow_name'] . '</td><td>' . date("Y-m-d H:i", $v['dateline']) . '</td><td><a  onclick=Tpflow.wfconfirm("' . $urls['wfapi'] . '?act=wfend",{"id":' . $v['id'] . '},"您确定要终止该工作流吗？");>终止</a>  |  ' . lib::tpflow_btn($v['from_id'], $v['from_table'], 100, self::WfCenter('Info', $v['from_id'], $v['from_table'])) . '</td></tr>';
			}
			return lib::tmp_wfjk($html);
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
			$info = Flow::getWorkflow($data); //获取工作流详情
			$type = '';
			foreach (Info::get_wftype() as $k => $v) {
				$type .= '<option value="' . $v['name'] . '">' . $v['title'] . '</option>';
			}
			return lib::tmp_add($urls['wfapi'] . '?act=add', $info, $type);
		}
		if ($act == 'event') {
			if ($data != '' && !is_numeric($data)) {
				if(isset($data['info'])){
					return Event::getFun($data['fun'],$data['type']);
				}
				if(isset($data['code'])){
					$ret =  Event::save($data);
					if ($ret['code'] == 0) {
						return unit::msg_return('操作成功！');
					} else {
						return unit::msg_return($ret['data'], 1);
					}
				}
			}
			$info = Flow::getWorkflow($data); //获取工作流详情
			$type = '';
			foreach (Info::get_wftype() as $k => $v) {
				$type .= '<option value="' . $v['name'] . '">' . $v['title'] . '</option>';
			}
			return lib::tmp_event($urls['wfapi'] . '?act=event', $info, $type);
		}
        if ($act == 'del') {
            if ($data != '' && !is_numeric($data)) {
                //判断当前是否有运行流程
                $ret = Run::FindRun(['flow_id'=>$data['id'],'status'=>0],'status');
                if($ret && $ret['status']==0){
                    return unit::msg_return('流程运行中，无法删除!', 1);
                }
                $del_flow = Flow::del($data['id']);
                if (!$del_flow) {
                    return unit::msg_return('删除流程信息失败！!', 1);
                }
                $find_pro = Process::SearchFlowProcess(['flow_id'=>$data['id']]);
                if(count($find_pro)>0){
                    //删除步骤
                    $del_pro = Process::DelFlowProcess(['flow_id'=>$data['id']]);
                    if (!$del_pro) {
                        return unit::msg_return('删除步骤信息失败，请手动删除！!', 1);
                    }
                }
                return unit::msg_return('操作成功！');

            }
        }
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 5.0 工作流代理接口
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * index 列表调用
	 * add   添加代理授权
	 */
	function WfEntrustCenter($act, $data = '')
	{
		$urls = unit::gconfig('wf_url');
		if ($act == 'index') {
			$list = Entrust::lists();
			$html = '';
			foreach ($list as $k => $v) {
				$btn = "<a class='button' onclick=Tpflow.lopen('修改','" . $urls['wfapi'] . '?act=dladd&id=' . $v['id'] . "','65','60')> 修改</a> ";
				$sq = "步骤授权";
				if ($v['flow_id'] == 0) {
					$sq = "全局授权";
				}
				$html .= '<tr><td>' . $v['id'] . '</td><td>' . $v['entrust_title'] . '</td><td>' . $sq . '</td><td>' . $v['old_name'] . '=>' . $v['entrust_name'] . '</td><td>' . date('Y/m/d H:i', $v['entrust_stime']) . '~' . date('Y/m/d H:i', $v['entrust_etime']) . '</td><td>' . $v['entrust_con'] . '</td><td>' . $btn . '</td></tr>';
			}
			return lib::tmp_wfgl($html);
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
			$info = Entrust::find($data);
			//获取全部跟自己相关的步骤
			$data = Process::get_userprocess(unit::getuserinfo('uid'), unit::getuserinfo('role'));
			$type = '';
			foreach ($data as $k => $v) {
				$type .= '<option value="' . $v['id'] . '@' . $v['flow_id'] . '">[' . $v['flow_name'] . ']' . $v['process_name'] . '</option>';
			}
			$user = User::GetUser();
			foreach ($user as $k => $v) {
				$user .= '<option value="' . $v['id'] . '@' . $v['username'] . '">' . $v['username'] . '</option>';
			}
			return lib::tmp_entrust($info, $type, $user);
		}
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 5.0统一接口设计器
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
			return '<br/><br/><style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; }h1{ font-size: 40px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 26px }</style><div style="padding: 24px 48px;"> <h1>\﻿ (•◡•) / </h1><p> TpFlow v5.0正式版<br/><span style="font-size:16px;">PHP优秀的开源工作流引擎</span></p><span style="font-size:13px;">[ ©2018-2022 Guoguo <a href="https://www.cojz8.com/">TpFlow</a>  ]</span></div>';
		}
		if ($act == 'wfdesc') {
			$one = Flow::getWorkflow($flow_id);
			if (!$one) {
				return '未找到数据，请返回重试!';
			}
			return lib::tmp_wfdesc($one['id'], Flow::ProcessAll($flow_id), $urls['designapi']);
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
			return Flow::ProcessAdd($flow_id);
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
			return lib::tmp_wfatt($info['info'], $info['from'], $info['process_to_list']);
		}
		if ($act == 'super_user') {
			if ($data['type_mode'] == 'user') {
				$info = User::GetUser();
				$user = '';
				foreach ($info as $k => $v) {
					$user .= '<option value="' . $v['id'] . '">' . $v['username'] . '</option>';
				}
				return lib::tmp_suser($urls['designapi'] . '?act=super_user&type_mode=super_get', $data['kid'], $user);
			} elseif ($data['type_mode'] == 'role') {
				$info = User::GetRole();
				$user = '';
				foreach ($info as $k => $v) {
					$user .= '<option value="' . $v['id'] . '">' . $v['username'] . '</option>';
				}
				return lib::tmp_suser($urls['designapi'] . '?act=super_user&type_mode=super_get', 'auto_role', $user, 'role');
			} else {
				return ['data' => User::AjaxGet(trim($data['type']), $data['key']), 'code' => 1, 'msg' => '查询成功！'];
			}
		}
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 5.0统一接口
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * log  历史日志消息
	 * btn  权限判断
	 * status  状态判断
	 */
	function wfAccess($act, $data = '')
	{
		if ($act == 'log') {
			echo Log::FlowLog($data['id'], $data['type']);
			exit;
		}
		if ($act == 'btn') {
			return (new lib())::tpflow_btn($data['id'], $data['type'], $data['status'], self::WfCenter('Info', $data['id'], $data['type'], $data['status']));
		}
		if ($act == 'status') {
			return (new lib())::tpflow_status($data['status']);
		}
		return $act . '参数出错';
	}
	
	/**
	 * Tpflow 5.0统一接口
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * userFlow  用户流程数据
	 * userData  用户数据分组查询
	 */
	function wfUserData($act, $map, $field, $order, $group)
	{
		if ($act == 'userData') {
			$data = Run::dataRunProcess($map, $field, $order, $group);
		}
		if ($act == 'userFlow') {
			$map[] = ['f.sponsor_ids', 'find in set', unit::getuserinfo('uid')];
			$data = Run::dataRunProcess($map, $field, $order, $group);
		}
		return ['code' => 1, 'msg' => '查询成功', 'data' => $data];
	}
	
}