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
namespace tpflow\service;

use tpflow\lib\unit;


Class Control{
	
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('view_return')==1){
			$className = '\\tpflow\\service\\method\\Tpl';
		}else{
			$className = '\\tpflow\\service\\method\\Jwt';
		}
		$this->mode = new $className();
    }
	/**
	  * 工作流程统一接口
	  *
      * @access static
      * @param string $act 调用接口方法
	  * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	  * Info    获取流程信息
	  * start   发起审批流
	  * endflow 审批流终止
	  *
	  */
	static function WfCenter($act,$wf_fid='',$wf_type='',$data='',$post=''){
		return (new Control())->mode->WfCenter($act,$wf_fid,$wf_type,$data,$post);
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
	static function WfFlowCenter($act,$data=''){
		return (new Control())->mode->WfFlowCenter($act,$data);
	}
	/**
	 * Tpflow 5.0 工作流代理接口
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * index 列表调用
	 * add   添加代理授权
	 */
	static function WfEntrustCenter($act,$data=''){
		return (new Control())->mode->WfEntrustCenter($act,$data);
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
	static function WfDescCenter($act,$flow_id='',$data=''){
		return (new Control())->mode->WfDescCenter($act,$flow_id,$data);
		
	}
	/**
	 * Tpflow 5.0统一接口
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	 * log  历史日志消息
	 * btn  权限判断
	 * status  状态判断
	 */
	static function WfAccess($act,$data=''){
		return (new Control())->mode->WfAccess($act,$data);
	}
	
}