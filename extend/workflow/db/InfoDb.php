<?php
namespace workflow;
/**
 * 信息处理
 */
use think\Db;
use think\facade\Session;

class InfoDB{
	
	public static $prefix = 'leipi_';
	
	/**
	 * 判断业务是否存在，避免已经删除导致错误
	 * @param $wf_fid  业务id
	 * @param $wf_type 业务表名
	 */
	public static function getbill($wf_fid,$wf_type) 
	{
		if ($wf_fid == '' || $wf_type == '' ) {
			return false;
		}
		$wf_sql = "select * from ".self::$prefix.$wf_type." where id='".$wf_fid."'";
		$data =Db::query ($wf_sql );
		if($data){
			return  $data;
			}else{
			return  false;
		}
	}
	/**
	 * 添加工作流
	 * @param $wf_id  流程主ID
	 * @param $wf_process 流程信息
	 * @param $wf_fid  业务id
	 * @param $wf_type 业务表名
	 */
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
	/**
	 * 添加运行步骤信息
	 * @param $wf_id  流程主ID
	 * @param $wf_process 流程信息
	 * @param $run_id  运行的id
	 * @param $wf_type 业务表名
	 */
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
	/**
	 * 缓存信息
	 */
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
	public static function AddrunLog($uid,$run_id,$content,$from_id,$from_table,$run_flow=0)
	{
		$run_log = array(
                'uid'=>$uid,
				'from_id'=>$from_id,
				'from_table'=>$from_table,
                'run_id'=>$run_id,
                'content'=>$content,
                'run_flow'=>$run_flow,//从 serialize 改用  json_encode 兼容其它语言
                'dateline'=>time()
            );
			 $run_log = db('run_log')->insertGetId($run_log);
			 if(!$run_log)
				{
					return  false;
				}
				return $run_log;
	}
	
	/**
	 * 根据单据ID，单据表 获取流程信息
	 *
	 * @param $run_id  运行的id
	 * @param $wf_type 业务表名
	 */
	public static function workflowInfo($wf_fid,$wf_type) {
		$workflow = [];
		require ( BEASE_URL . '/config/config.php');//  
		$sql = "select * from  ".self::$prefix."run where from_id='$wf_fid' and from_table='$wf_type' and is_del=0 and status=0";
		
		$sql2 = "select * from  ".self::$prefix."run where from_id='$wf_fid' and from_table='$wf_type' and is_del=0 ";
		if(count(Db::query($sql2)) > '0'){
			$result = Db::query($sql);	
			if ($result) {
				$workflow ['bill_st'] = $result[0]['status'];
				$workflow ['flow_id'] = $result[0]['flow_id'];
				$workflow ['run_id'] = $result[0]['id'];
				$workflow ['run_flow_process'] = $result[0]['run_flow_process'];
				$workflow ['bill_state'] = $flowstatus[$result[0]['status']];
				$workflow ['flow_name'] = FlowDb::GetFlowInfo($result[0]['flow_id']);
				$workflow ['process'] = ProcessDb::GetProcessInfo($result[0]['run_flow_process']);
				$workflow ['nexprocess'] = ProcessDb::GetNexProcessInfo($wf_type,$wf_fid,$result[0]['run_flow_process']);
				$workflow ['log'] = ProcessDb::RunLog($wf_fid,$wf_type);
			} else {
				$workflow ['bill_st'] = 1;
				$workflow ['bill_state'] =$flowstatus[1];
				$workflow ['bill_check'] = '';
				$workflow ['bill_time'] = '';
			}
			
		}else{
			$workflow ['bill_st'] = -1;
			$workflow ['bill_state'] =$flowstatus[-1];
			$workflow ['bill_check'] = '';
			$workflow ['bill_time'] = '';
		}
		
		
		return $workflow;
	}
	/**
	 * 根据单据ID，单据表 获取流程信息
	 *
	 * @param $run_id  运行的id
	 * @param $wf_type 业务表名
	 */
	public static function workrunInfo($run_id) {
		$sql = "select * from  ".self::$prefix."run where id='$run_id'";
		$result = Db::query($sql);
		return $result;
	}
	
	
}