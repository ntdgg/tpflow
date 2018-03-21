<?php
/**
 * 流程进度处理
 */

namespace workflow;

use think\Db;
use think\facade\Session;

class ProcessDb{
	
	public static $prefix = 'leipi_';
	/**
	 * 根据ID获取流程信息
	 * @param $pid
	 */
	public static function GetProcessInfo($pid)
	{
		$wf_sql = "select process_name,process_type,process_to,auto_person,auto_sponsor_ids,auto_sponsor_text,is_sing,sign_look,is_back from ".self::$prefix."flow_process where id='".$pid."'";
		$info = Db::query ($wf_sql );
		return $info;
	}
	/**
	 * 获取第一个流程
	 * @param $wf_id
	 */
	public static function getWorkflowProcess($wf_id) 
	{
		$wf_sql = "select * from ".self::$prefix."flow_process where is_del=0  and flow_id='".$wf_id."'";
		$flow_process = Db::query ($wf_sql );
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
	public static function RunLog($wf_fid,$wf_type) 
	{
		$wf_sql = "select * from ".self::$prefix."run_log where from_id='".$wf_fid."'  and from_table='".$wf_type."'";
		$run_log = Db::query ($wf_sql );
		foreach($run_log as $k=>$v)
        {
           $run_log[$k]['user'] ='admin';
        }
		
		return $run_log;
	}
	
}