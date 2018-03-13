<?php
namespace workflow;

use think\Db;
use think\facade\Session;

class InfoDB{
	
	public static $prefix = 'leipi_';
	/**
	 * 获取类别工作流
	 * @param $wf_type
	 */
	public static function getWorkflowByType($wf_type) {
		$workflow = array ();
		if ($wf_type == '') {
			return $workflow;
		}
		$wf_sql = "select flow_name,id from ".self::$prefix."flow where is_del=0  and type='".$wf_type."'";
		return  Db::query ($wf_sql );
	}
	/**
	 * 获取类别工作流
	 * @param $wf_type
	 */
	public static function getbill($wf_fid,$wf_type) {
		if ($wf_fid == '' || $wf_type == '' ) {
			return false;
		}
		$wf_sql = "select id from ".self::$prefix.$wf_type." where id='".$wf_fid."'";
		$data =Db::query ($wf_sql );
		if($data){
			return  $data;
			}else{
			return  false;
		}
	}
	/**
	 * 获取类别工作流
	 * @param $wf_type
	 */
	public static function getWorkflow($wf_id) {
		if ($wf_id == '') {
			return false;
		}
		$wf_sql = "select flow_name,id from ".self::$prefix."flow where is_del=0  and id='".$wf_id."'";
		$data =Db::query ($wf_sql );
		if($data){
			return  $data;
			}else{
			return  false;
		}
	}
	/**
	 * 获取类别工作流
	 * @param $wf_type
	 */
	public static function getWorkflowProcess($wf_id) {
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
	
	public static function addWorkflowRun($wf_id,$wf_process,$wf_fid,$wf_type)
	{
		$data = array(
            'pid'=>0,
            'uid'=>session('uid'),
            'flow_id'=>$wf_id,
			'from_table'=>$wf_type,
            'from_id'=>$wf_fid,
            'run_name'=>$wf_fid,
            'run_flow_id'=>$wf_id,
            'run_flow_process'=>$wf_process,
            'dateline'=>time(),
        );
        $run_id = db('run')->insertGetId($data);
		if(!$run_id)
        {
            return  false;
        }
        return $run_id;
	}
	public static function addWorkflowProcess($wf_id,$wf_process,$run_id)
	{
		$data = array(
            'uid'=>session('uid'),
            'run_id'=>$run_id,
            'run_flow'=>$wf_id,
            'run_flow_process'=>$wf_process,
            'parent_flow'=>0,
            'parent_flow_process'=>0,
            'run_child'=>0,//未处理，第一步不能进入子流程
            'remark'=>'',
            'is_sponsor'=>1,
            'status'=>1,
            'js_time'=>time(),
            'bl_time'=>time(),
            'dateline'=>time(),
        );
        $process_id = db('run_process')->insertGetId($data);
		if(!$process_id)
        {
            return  false;
        }
        return $process_id;
	}	
	public static function addWorkflowCache($run_id,$wf,$flow_process,$wf_fid)
	{
	$run_cache = array(
                'run_id'=>$run_id,
                'form_id'=>$wf_fid,
                'flow_id'=>$wf[0]['id'],
                'run_form'=>'',//从 serialize 改用  json_encode 兼容其它语言
                'run_flow'=>json_encode($wf),
                'run_flow_process'=>json_encode($flow_process), //这里未缓存 子流程 数据是不完善的， 后期会完善
                'dateline'=>time()
            );
     $run_cache = db('run_cache')->insertGetId($run_cache);
	 if(!$run_cache)
        {
            return  false;
        }
        return $run_cache;
	}
	/**
	 * 根据单据ID获取流程信息
	 *
	 * @param string $etuid	实例id
	 * @param string $ssn	工资号
	 * @return array() 流程信息
	 */
	public static function workflowInfo($wf_fid,$wf_type) {
		$workflow = [];
		$sql = "select * from  ".self::$prefix."run where from_id='$wf_fid' and from_table='$wf_type' and is_del=0 limit 1";
		$result = Db::query($sql);
		require ( BEASE_URL . '/config/config.php');
		if ($result) {
				$workflow ['bill_st'] = $result[0]['status'];
				$workflow ['bill_state'] = $flowstatus[$result[0]['status']];
				$workflow ['bill_check'] = '审批人';
				$workflow ['bill_time'] = 'time';
			} else {
				$workflow ['bill_st'] = -1;
				$workflow ['bill_state'] =$flowstatus[-1];
				$workflow ['bill_check'] = '';
				$workflow ['bill_time'] = '';
			}
		return $workflow;
	}
	
	
}