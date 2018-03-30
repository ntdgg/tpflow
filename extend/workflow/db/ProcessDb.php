<?php
/**
 * 流程进度处理
 */

namespace workflow;

use think\Db;
use think\facade\Session;

class ProcessDb{
	/**
	 * 根据ID获取流程信息
	 * @param $pid
	 */
	public static function GetProcessInfo($pid)
	{
		$info = Db::name('flow_process')
				->field('id,process_name,process_type,process_to,auto_person,auto_sponsor_ids,auto_role_ids,auto_sponsor_text,auto_role_text,is_sing,sign_look,is_back')
				->find($pid);
		if($info['auto_person']==4){ //办理人员
			$info['todo'] = $info['auto_sponsor_text'];
		}
		if($info['auto_person']==5){ //办理角色
			$info['todo'] = $info['auto_role_text'];
		}
		return $info;
	}
	/**
	 * 获取下个审批流信息
	 * @param $wf_type 单据表
	 * @param $wf_fid  单据id
	 * @param $pid   流程id
	 **/
	public static function GetNexProcessInfo($wf_type,$wf_fid,$pid)
	{
		$info = Db::name($wf_type)->find($wf_fid);
		$nex = Db::name('flow_process')->find($pid);
		if($nex['process_to'] !=''){
		$nex_pid = explode(",",$nex['process_to']);
		$out_condition = json_decode($nex['out_condition'],true);
			if(count($nex_pid)>=2){
			//多个审批流
				foreach($out_condition as $key=>$val){
					$where =implode(",",$val['condition']);
					//根据条件寻找匹配符合的工作流id
					$info = Db::name($wf_type)->where($where)->where('id',$wf_fid)->find();
					if($info){
						$nexprocessid = $key; //获得下一个流程的id
						break;	
					}
				}
				$process = self::GetProcessInfo($nexprocessid);
			}else{
				$process = self::GetProcessInfo($nex_pid);	
			}
		}else{
			$process = ['id'=>'','process_name'=>'END','todo'=>'结束'];
		}
		return $process;
	}
	/**
	 * 获取前步骤的流程信息
	 * @param $runid
	 */
	public static function GetPreProcessInfo($runid)
	{
		$pre_n = Db::name('run')->find($runid);
		$pre = Db::name('flow_process')->where('flow_id',$pre_n['flow_id'])->where('id','lt',$pre_n['run_flow_process'])->select();
		$prearray = [];
		if(count($pre)>=1){
			$prearray[0] = '退回制单人修改';
			foreach($pre as $k => $v){
				if($v['auto_person']==4){ //办理人员
					$todo = $v['auto_sponsor_text'];
				}
				if($v['auto_person']==5){ //办理角色
					$todo = $v['auto_role_text'];
				}
				$prearray[$v['id']] = $v['process_name'].'('.$todo.')';
			}
			}else{
			$prearray[0] = '退回制单人修改';	
		}
		return $prearray;
	}
	
	/**
	 * 获取第一个流程
	 * @param $wf_id
	 */
	public static function getWorkflowProcess($wf_id) 
	{
		$flow_process = Db::name('flow_process')->where('is_del',0)->where('flow_id',$wf_id)->select();
		//找到 流程第一步
        $flow_process_first = array();
        foreach($flow_process as $value)
        {
            if($value['process_type'] == 'is_one')
            {
                $flow_process_first = $value;
                break;
            }
        }
		if(!$flow_process_first)
        {
            return  false;
        }
		return $flow_process_first;
	}
	/**
	 * 流程日志
	 * @param $wf_fid
	 * @param $wf_type
	 */
	public static function RunLog($wf_fid,$wf_type) 
	{
		$run_log = Db::name('run_log')->where('from_id',$wf_fid)->where('from_table',$wf_type)->select();
		foreach($run_log as $k=>$v)
        {
           $run_log[$k]['user'] =Db::name('user')->where('id',$v['uid'])->value('username');
        }
		return $run_log;
	}
	
}