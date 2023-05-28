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

use tpflow\adaptive\Cc;
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
			return lib::tmp_wfstart(['wf_type' => $wf_type, 'wf_fid' => $wf_fid], array_values($flow));
		}
        if ($act == 'entCc') {
            return Cc::ccCheck($wf_fid);
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
                'tpflow_view' => $urls['wfapi'] . '?act=view&id=',
				'tpflow_upload' => unit::gconfig('wf_upload_file')
			];
			if ($wf_op == 'check') {
				return lib::tmp_check($info, self::WfCenter('Info', $wf_fid, $wf_type));
			}
            /*对审批提交执行人进行权限校验*/
            if($wf_op == 'ok' || $wf_op == 'back' || $wf_op == 'sign'){
                $flowinfo = self::WfCenter('Info', $wf_fid, $wf_type);
                $thisuser = ['thisuid' => unit::getuserinfo('uid'), 'thisrole' => unit::getuserinfo('role')];
                $st = 0;
                if ($flowinfo != -1) {
                    if ($flowinfo['sing_st'] == 0) {
                        $user = explode(",", $flowinfo['status']['sponsor_ids']);
                        $user_name = $flowinfo['status']['sponsor_text'];
                        if ($flowinfo['status']['auto_person'] == 2 ||$flowinfo['status']['auto_person'] == 3 || $flowinfo['status']['auto_person'] == 4) {
                            if (in_array($thisuser['thisuid'], $user)) {
                                $st = 1;
                            }
                        }
                        /*事务增加角色判断*/
                        if ($flowinfo['status']['auto_person'] == 6) {
                            if ($flowinfo['status']['word_type']==1) {
                                if (in_array($thisuser['thisuid'], $user)) {
                                    $st = 1;
                                }
                            }else{
                                if (in_array($thisuser['thisrole'], $user)) {
                                    $st = 1;
                                }
                            }
                        }

                        if ($flowinfo['status']['auto_person'] == 5) {
                            if(!empty(array_intersect((array)$thisuser['thisrole'], $user))){// Guoke 2021/11/26 13:30 扩展多多用户组的支持
                                $st = 1;
                            }
                        }
                    } else {
                        if ($flowinfo['sing_info']['uid'] == $thisuser['thisuid']) {
                            $st = 1;
                        } else {
                            $user_name = $flowinfo['sing_info']['uid'];
                        }
                    }
                }
                if ($post != '' && $st==0) {
                    return unit::msg_return('对不起，您没有权限审核！', 1);
                }
                if($st==0){
                    return '<script>var index = parent.layer.getFrameIndex(window.name);parent.layer.msg("对不起，您没有审核权限!");setTimeout("parent.layer.close(index)",1000);</script>';
                }
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
            $findwhere = [['from_id', '=', $data['bill_id']], ['from_table', '=', $data['bill_table']]];
            $FindRun = Run::FindRun($findwhere);
            Log::AddrunLog(unit::getuserinfo('uid'),$FindRun['id'] ?? '', ['wf_fid'=>$data['bill_id'],'wf_type'=>$data['bill_table'],'check_con'=>'取消审核','art'=>''], 'cancelflow');
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
                $btn .= " <a class='btn' onclick=Tpflow.wfconfirm('" . $urls['wfapi'] . '?act=ver' . "',{'id':" . $v['id'] . "},'您确定复刻新流程吗？')> 版本+</a>";
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
				
				$html .= '<tr class="text-c"><td>' . $v['id'] . '</td><td>' . $v['from_table'] . '</td><td>' . $v['flow_name'] . '</td><td>' . $status[$v['status']] . '</td><td>' . $v['user'] . '</td><td>' . date("Y-m-d H:i", $v['dateline']) . '</td><td><a  onclick=Tpflow.wfconfirm("' . $urls['wfapi'] . '?act=wfend",{"id":' . $v['id'] . '},"您确定要终止该工作流吗？");>终止</a>  |  ' . lib::tpflow_btn($v['from_id'], $v['from_table'], 100, self::WfCenter('Info', $v['from_id'], $v['from_table'])) . '</td></tr>';
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
                    /*7.0自动添加开始节点，结束节点*/
                    $star_flow = [
                        'process_name'=>'开始',
                        'flow_id' => $ret['data'], 'setleft' => '-250', 'settop' => '250',
                        'process_type' => 'node-start', 'style' => '{"width":60,"height":45,"color":"#2d6dcc"}'
                    ];
                    $star_flow_id = Process::AddFlowProcess($star_flow);
                    $end_flow = [
                        'process_name'=>'结束',
                        'flow_id' => $ret['data'], 'setleft' => '80', 'settop' => '250',
                        'process_type' => 'node-end', 'style' => '{"width":60,"height":60,"color":"#2d6dcc"}'
                    ];
                    $end_flow_id = Process::AddFlowProcess($end_flow);
                    Process::EditFlowProcess([['id', '=', $star_flow_id], ['flow_id', '=', $ret['data']]], ['process_to' => $end_flow_id, 'uptime' => time()]);
                    /*7.0自动添加开始节点，结束节点*/
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
			return lib::tmp_event($urls['wfapi'] . '?act=event', $info);
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
        if ($act == 'ver') {
            if ($data != '' && !is_numeric($data)) {
                //当前流程
                $ret = Flow::find($data['id']);
                unset($ret['id']);
                $ret['flow_name'] = '【副本】'.$ret['flow_name'];
                $ret['add_time'] = time();
                $run_id = Flow::AddFlow($ret);
                if($run_id['code']==1){
                    return unit::msg_return('写入数据库失败！');
                }
                //处理步骤信息
                $map[] = ['flow_id', '=', $data['id']];
                $list = Process::SearchFlowProcess($map);
                $ids = [];
                foreach($list as $k=>$v){
                    $pid = $v['id'];
                    unset($v['id']);
                    $v['flow_id'] = $run_id['data'];
                    $id = Process::AddFlowProcess($v);
                    $ids[$pid] = $id;
                }
                foreach($ids as $k=>$v){
                    $pinfo = Process::find($k);
                    if($pinfo['process_to'] != ''){
                        $array = explode(',',$pinfo['process_to']);
                        if(count($array)>1){
                            foreach($array as $v2){
                                $process_to_array[] = $ids[$v2];
                            }
                            $process_to = implode(',',$process_to_array);
                            }else{
                            $process_to = $ids[$pinfo['process_to']];
                        }
                        unset($process_to_array);
                        Process::EditFlowProcess([['id', '=', $v]], ['process_to' => $process_to, 'uptime' => time()]);
                    }
                }
                return unit::msg_return('操作成功！');
            }
        }
        if ($act == 'verUpdate') {
            Flow::verUpdate();
            return json(unit::msg_return('版本更新成功！'));
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
			return '<br/><br/><style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; }h1{ font-size: 40px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 26px }</style><div style="padding: 24px 48px;"> <h1>\﻿ (•◡•) / </h1><p> TpFlow v7.0正式版<br/><span style="font-size:16px;">PHP优秀的开源工作流引擎</span></p><span style="font-size:13px;">[ ©2018-2025 Guoguo <a href="https://www.cojz8.com/">TpFlow</a>  ]</span></div>';
		}
		if ($act == 'wfdesc') {
			$one = Flow::getWorkflow($flow_id);
			if (!$one) {
				return '未找到数据，请返回重试!';
			}
			return lib::tmp_wfdesc($one['id'], Flow::ProcessAll($flow_id), $urls['designapi']);
		}
        if ($act == 'nodejson') {
            $one = Flow::getWorkflow($flow_id);
            if (!$one) {
                return '未找到数据，请返回重试!';
            }
            $process_data = Flow::ProcessAll($flow_id);
            $data = json_decode($process_data,true);
            return ['status' => 0, 'x6' => $data['x6']];
        }
        if($act == 'view') {
            $one = Flow::getWorkflow($flow_id);
            if (!$one) {
                return '未找到数据，请返回重试!';
            }
            return lib::tmp_flowview($one['id'], Flow::ProcessAll($flow_id), $urls['designapi']);
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
			return Flow::ProcessAdd($flow_id,$data);
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
            $one = Flow::getWorkflow($info['info']['flow_id']);
			return lib::tmp_wfatt($info['info'], $info['from'], $info['process_to_list'],$one['type']);
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

        if($act=='quilklink'){
            //$flow_id
            $process_id = $data['process_id'];
            $process_info = Process::find($process_id);
            /*添加一个下级节点信息*/
            if($data['fun']=='node'){
                $data = [
                    'process_name'=>'步骤',
                    'flow_id' => $flow_id, 'setleft' => $process_info['setleft']+150, 'settop' => $process_info['settop'],
                    'process_type' => 'node-flow','process_to'=>$process_info['process_to'], 'style' => '{"width":65,"height":45,"color":"#2d6dcc"}'
                ];
                $processid = Process::AddFlowProcess($data);
                $datas = [
                    'process_to' => $processid,
                    'uptime' => time()
                ];
                Process::EditFlowProcess([['id', '=', $process_id], ['flow_id', '=', $flow_id]], $datas);
                return ['code' => 0, 'msg' => '添加成功！', 'info' => ''];
            }
            if($data['fun']=='gateway'){
                $data1 = [
                    'process_name'=>'步骤',
                    'flow_id' => $flow_id, 'setleft' => $process_info['setleft']+150, 'settop' => $process_info['settop']+150,
                    'process_type' => 'node-flow','process_to'=>$process_info['process_to'], 'style' => '{"width":65,"height":45,"color":"#2d6dcc"}'
                ];
                $processid1 = Process::AddFlowProcess($data1);
                $data2 = [
                    'process_name'=>'步骤',
                    'flow_id' => $flow_id, 'setleft' => $process_info['setleft']+150, 'settop' => $process_info['settop']-150,
                    'process_type' => 'node-flow','process_to'=>$process_info['process_to'], 'style' => '{"width":65,"height":45,"color":"#2d6dcc"}'
                ];
                $processid2 = Process::AddFlowProcess($data2);
                $datas = [
                    'process_to' => $processid1.','.$processid2,
                    'uptime' => time()
                ];
                Process::EditFlowProcess([['id', '=', $process_id], ['flow_id', '=', $flow_id]], $datas);
                return ['code' => 0, 'msg' => '添加成功！', 'info' => ''];
            }
            if($data['fun']=='msg'){
                $data = [
                    'process_name'=>'消息',
                    'flow_id' => $flow_id, 'setleft' => $process_info['setleft'], 'settop' => $process_info['settop']+150,
                    'process_type' => 'node-msg','process_to'=>'', 'style' => '{"width":65,"height":45,"color":"#2d6dcc"}'
                ];
                $processid = Process::AddFlowProcess($data);
                if($process_info['process_to']==''){
                    $process_to = $processid;
                }else{
                    $process_to = $process_info['process_to'].','.$processid;
                }
                $datas = [
                    'process_to' => $process_to,
                    'uptime' => time()
                ];
                Process::EditFlowProcess([['id', '=', $process_id], ['flow_id', '=', $flow_id]], $datas);
                return ['code' => 0, 'msg' => '添加成功！', 'info' => ''];
            }
            if($data['fun']=='cc'){
                $data = [
                    'process_name'=>'抄送',
                    'flow_id' => $flow_id, 'setleft' => $process_info['setleft'], 'settop' => $process_info['settop']+150,
                    'process_type' => 'node-cc','process_to'=>'', 'style' => '{"width":65,"height":45,"color":"#2d6dcc"}'
                ];
                $processid = Process::AddFlowProcess($data);
                if($process_info['process_to']==''){
                    $process_to = $processid;
                }else{
                    $process_to = $process_info['process_to'].','.$processid;
                }
                $datas = [
                    'process_to' => $process_to,
                    'uptime' => time()
                ];
                Process::EditFlowProcess([['id', '=', $process_id], ['flow_id', '=', $flow_id]], $datas);
                return ['code' => 0, 'msg' => '添加成功！', 'info' => ''];
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
			return Log::FlowLog($data['id'], $data['type']);
		}
		if ($act == 'btn') {
            $info = [];
            if($data['status']==1){
                $info = self::WfCenter('Info', $data['id'], $data['type'], $data['status']);
            }
			return (new lib())::tpflow_btn($data['id'], $data['type'], $data['status'], $info);
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
	function wfUserData($act, $map, $field, $order, $group,$page,$limit)
	{
		if ($act == 'userData') {
			$data = Run::dataRunProcess($map, $field, $order, $group);
		}
		if ($act == 'userFlow') {
			// Guoke 2021/11/26 15:40 扩展多用户组的支持
			$roles=unit::getuserinfo('role');
			$tmpRaw=$p='';
			foreach((array)$roles as $v){
				$tmpRaw .= "$p FIND_IN_SET('$v',f.sponsor_ids)";
				$p=' or';
			}
            $mapRaw = '(f.auto_person != 5 and FIND_IN_SET(' . unit::getuserinfo('uid') . ",f.sponsor_ids)) or (f.auto_person=5 and ($tmpRaw))";
			$data = Run::dataRunProcess($map,$mapRaw, $field, $order, $group,$page,$limit);

		}
		return ['code' => 1, 'msg' => '查询成功', 'data' => $data];
	}

    function wfMysend($page,$limit){
        $data = Run::dataRunMy(unit::getuserinfo('uid'),$page, $limit);
        return ['code' => 1, 'msg' => '查询成功', 'data' => $data['data'], 'count' => $data['count']];
    }
	
}