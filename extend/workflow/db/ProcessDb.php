<?php
/**
*+------------------
* 工作流步骤
*+------------------ 
*/

namespace workflow;

use think\Db;
use think\facade\Session;

class ProcessDb{
	/**
	 * 根据ID获取流程信息
	 *
	 * @param $pid 步骤编号
	 */
	public static function GetProcessInfo($pid)
	{
		$info = Db::name('flow_process')
				->field('id,process_name,process_type,process_to,auto_person,auto_sponsor_ids,auto_role_ids,auto_sponsor_text,auto_role_text,range_user_ids,range_user_text,is_sing,sign_look,is_back')
				->find($pid);
		if($info['auto_person']==3){ //办理人员
			$ids = explode(",",$info['range_user_text']);
			$info['todo'] = ['ids'=>explode(",",$info['range_user_ids']),'text'=>explode(",",$info['range_user_text'])];
		}
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
	 *
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
					$info = Db::name($wf_type)->where($where)->where('id','eq',$wf_fid)->find();
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
			$process = ['auto_person'=>'','id'=>'','process_name'=>'END','todo'=>'结束'];
		}
		return $process;
	}
	/**
	 * 获取前步骤的流程信息
	 *
	 * @param $runid
	 */
	public static function GetPreProcessInfo($runid)
	{
		$pre = [];
		$pre_n = Db::name('run_process')->find($runid);
		//获取本流程中小于本次ID的步骤信息
		$pre_p = Db::name('run_process')
			 ->where('run_flow','eq',$pre_n['run_flow'])
			 ->where('run_id','eq',$pre_n['run_id'])
			 ->where('id','lt',$pre_n['id'])
			 ->field('run_flow_process')->select();
		//遍历获取小于本次ID中的相关步骤
		foreach($pre_p as $k=>$v){
			$pre[] = Db::name('flow_process')->where('id','eq',$v['run_flow_process'])->find();
		}
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
	 * 获取前步骤的流程信息
	 *
	 * @param $runid
	 */
	public static function Getrunprocess($pid,$run_id)
	{
		$pre_n = Db::name('run_process')->where('run_id','eq',$run_id)->where('run_flow_process','eq',$pid)->find();
		return $pre_n;
	}
	
	/**
	 * 获取第一个流程
	 *
	 * @param $wf_id
	 */
	public static function getWorkflowProcess($wf_id) 
	{
		$flow_process = Db::name('flow_process')->where('is_del','eq',0)->where('flow_id','eq',$wf_id)->select();
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
	 *
	 * @param $wf_fid
	 * @param $wf_type
	 */
	public static function RunLog($wf_fid,$wf_type) 
	{
		$run_log = Db::name('run_log')->where('from_id','eq',$wf_fid)->where('from_table','eq',$wf_type)->select();
		foreach($run_log as $k=>$v)
        {
           $run_log[$k]['user'] =Db::name('user')->where('id','eq',$v['uid'])->value('username');
        }
		return $run_log;
	}
	
	
}