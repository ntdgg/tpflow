<?php
/**
 *+------------------
 * Tpflow 系统默认模板接口调用类
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace tpflow;

define('BEASE_URL', realpath ( dirname ( __FILE__ ) ) );
//引用适配器核心控制
use tpflow\service\Control;
//引用工具类
use tpflow\lib\unit;

	class Api{
	public function  __construct(){
		if(unit::getuserinfo()==-1){
			echo 'Access Error!';exit;
		}
    }
	 /**
	  * Tpflow 4.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	  */
	 public function WfDo($act='index'){
		if($act=='start'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::WfCenter($act,input('wf_fid'),input('wf_type'),$data);
			 }else{
				 return Control::WfCenter($act,input('wf_fid'),input('wf_type'));
			 }
		}
		if($act=='endflow'){
			return Control::WfCenter($act,'','',['bill_table'=>input('bill_table'),'bill_id'=>input('bill_id')]);
		}
		if($act=='do'){
			$wf_op = input('wf_op') ?? 'check';
			$ssing = input('ssing') ?? 'sing';
			$submit = input('submit') ?? 'ok';
			if (unit::is_post()) {
				$post = input('post.');
				return Control::WfCenter($act,input('wf_fid'),input('wf_type'),['wf_op'=>$wf_op,'ssing'=>$ssing,'submit'=>$submit],$post);
			 }else{
				 return Control::WfCenter($act,input('wf_fid'),input('wf_type'),['wf_op'=>$wf_op,'ssing'=>$ssing,'submit'=>$submit]);
			 }
		}
	}
	/**
	 * Tpflow 4.0统一接口设计器
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public function designapi($act){
		if($act=='welcome' ||$act=='check' || $act=='add' || $act=='delAll' || $act=='wfdesc'){
			return Control::WfDescCenter($act,input('flow_id'));
		}
		if($act=='save'){
			return Control::WfDescCenter($act,input('flow_id'),input('process_info'));
		}
		if($act=='del' ||$act=='att'){
			return Control::WfDescCenter($act,input('flow_id'),input('id'));
		}
		if($act=='saveatt'){
			return Control::WfDescCenter($act,'',input('post.'));
		}
		if($act=='super_user'){
			return Control::WfDescCenter($act,'',['kid'=>input('kid'),'type_mode'=>input('type_mode'),'key'=>input('key'),'type'=>input('type')]);
		}
	}
	/**
	 * Tpflow 4.0统一接口 流程管理
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public function wfapi($act='index'){
		if($act=='index'||$act=='wfjk'){
			return Control::WfFlowCenter($act);
		}
		if($act=='wfdl'){
			return Control::WfEntrustCenter('index');
		}
		if($act=='add'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::WfFlowCenter($act,$data);
			 }else{
                $data = input('id') ?? -1;
				 return Control::WfFlowCenter($act,$data);
			 }
		}
		if($act=='wfend'){
			 return Control::WfFlowCenter($act,input('id'));
		}
		if($act=='dladd'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::WfEntrustCenter('add',$data);
			 }else{
				 return Control::WfEntrustCenter('add',input('id'));
			 }
		}
	}
	/**
	 * Tpflow 4.0统一接口 前端权限控制中心
	 * @param string $act 调用接口方法
     * @param string $data 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public static function wfaccess($act='log',$data=''){
		return Control::WfAccess($act,$data);
	}
}
	