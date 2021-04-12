<?php
/**
 *+------------------
 * Tpflow 5.0 系统默认模板接口调用类
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types = 1);

namespace tpflow;

define('BEASE_URL', realpath ( dirname ( __FILE__ ) ) );

define('Tpflow_Ver', '5.0.4' );
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
	  * Tpflow 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	  */
	 public function WfDo($act='index'){
		if($act=='start'){
			if (unit::is_post()) {
				$data = input('post.');
				echo Control::WfCenter($act,input('wf_fid'),input('wf_type'),$data);
			 }else{
				 return Control::WfCenter($act,input('wf_fid'),input('wf_type'));
			 }
		}
		if($act=='endflow'||$act=='cancelflow'){
			echo Control::WfCenter($act,'','',['bill_table'=>input('bill_table'),'bill_id'=>input('bill_id')]);
		}
		if($act=='do'){
			$wf_op = input('wf_op') ?? 'check';
			$ssing = input('ssing') ?? 'sing';
			$submit = input('submit') ?? 'ok';
			if (unit::is_post()) {
				$post = input('post.');
				echo Control::WfCenter($act,input('wf_fid'),input('wf_type'),['wf_op'=>$wf_op,'ssing'=>$ssing,'submit'=>$submit],$post);
			 }else{
				 echo Control::WfCenter($act,input('wf_fid'),input('wf_type'),['wf_op'=>$wf_op,'ssing'=>$ssing,'submit'=>$submit]);
			 }
		}
	}
	/**
	 * Tpflow 5.0统一接口设计器
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public function designapi($act){
		if($act=='welcome' ||$act=='check' || $act=='add' || $act=='delAll' || $act=='wfdesc'){
			echo Control::WfDescCenter($act,input('flow_id'));
		}
		if($act=='save'){
			echo Control::WfDescCenter($act,input('flow_id'),input('process_info'));
		}
		if($act=='del' ||$act=='att'){
			echo Control::WfDescCenter($act,input('flow_id'),input('id'));
		}
		if($act=='saveatt'){
			echo Control::WfDescCenter($act,'',input('post.'));
		}
		if($act=='super_user'){
			return Control::WfDescCenter($act,'',['kid'=>input('kid'),'type_mode'=>input('type_mode'),'key'=>input('key'),'type'=>input('type')]);
		}
	}
	/**
	 * Tpflow 5.0统一接口 流程管理
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
				echo Control::WfFlowCenter($act,$data);
			 }else{
                $data = input('id') ?? -1;
				 echo Control::WfFlowCenter($act,$data);
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
	 * Tpflow 5.0统一接口 数据接口
	 * @param string $act 调用接口方法
	 * @param int    $uid 用户id
	 * @param array  $map 查询方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	 public static function wfUserData($act='userFlow',$map=[],$field='',$order='',$group=''){
		return Control::wfUserData($act,$map,$field,$order,$group);
	}
	 
	 
	/**
	 * Tpflow 5.0统一接口 前端权限控制中心
	 * @param string $act 调用接口方法
     * @param string $data 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public static function wfAccess($act='log',$data=''){
		return Control::wfAccess($act,$data);
	}
}
	