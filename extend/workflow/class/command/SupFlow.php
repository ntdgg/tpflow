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
namespace workflow;

use think\Db;

class SupFlow{
	/**
	 * 任务执行
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doSupEnd($wfid,$uid) {
		//读取工作流信息
		$wfinfo = InfoDB::workrunInfo($wfid);
		if(!$wfinfo){
				return ['msg'=>'流程信息有误！','code'=>'-1'];
			} 
		$config = [
				'wf_fid'=>$wfinfo['from_id'],
				'wf_type'=>$wfinfo['from_table'],
                'check_con'=>'编号：'.$uid.'的超级管理员终止了本流程！',
            ];
		//结束当前run 工作流
		$end = FlowDb::end_flow($wfid);
		$end = FlowDb::end_process($wfinfo,'编号：'.$uid.'的超级管理员终止了本流程！');
		$run_log = LogDb::AddrunLog($uid,$wfid,$config,'SupEnd');
		
		if(!$end){
				return ['msg'=>'结束流程错误！！！','code'=>'-1'];
			} 
		//更新单据状态
		$bill_update = InfoDB::UpdateBill($config['wf_fid'],$config['wf_type'],2);
		if(!$bill_update){
			return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
		}
	}
}