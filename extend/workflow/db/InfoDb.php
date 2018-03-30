<?php
namespace workflow;
/**
 * 信息处理
 */
use think\Db;
use think\facade\Session;

class InfoDB{
	
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
		$info = Db::name($wf_type)->find($wf_fid);
		if($info){
			return  $info;
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
        $run_id = Db::name('run')->insertGetId($data);
		if(!$run_id){
            return  false;
        }else{
			 return $run_id;
		}
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
        $process_id = Db::name('run_process')->insertGetId($data);
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
                'flow_id'=>$wf['id'],
                'run_form'=>'',//从 serialize 改用  json_encode 兼容其它语言
                'run_flow'=>json_encode($wf),
                'run_flow_process'=>json_encode($flow_process), //这里未缓存 子流程 数据是不完善的， 后期会完善
                'dateline'=>time()
            );
     $run_cache = Db::name('run_cache')->insertGetId($run_cache);
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
			 $run_log = Db::name('run_log')->insertGetId($run_log);
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
		$count = Db::name('run')->where('from_id',$wf_fid)->where('from_table',$wf_type)->where('is_del',0)->count();
		if($count > '0'){
			$result = Db::name('run')->where('from_id',$wf_fid)->where('from_table',$wf_type)->where('is_del',0)->where('status',0)->find();
			if ($result) {
				$workflow ['bill_st'] = $result['status'];
				$workflow ['flow_id'] = $result['flow_id'];
				$workflow ['run_id'] = $result['id'];
				$workflow ['run_flow_process'] = $result['run_flow_process'];
				$workflow ['bill_state'] = $flowstatus[$result['status']];
				$workflow ['flow_name'] = FlowDb::GetFlowInfo($result['flow_id']);
				
				$workflow ['process'] = ProcessDb::GetProcessInfo($result['run_flow_process']);
				
				$workflow ['nexprocess'] = ProcessDb::GetNexProcessInfo($wf_type,$wf_fid,$result['run_flow_process']);
				$workflow ['preprocess'] = ProcessDb::GetPreProcessInfo($result['id']);
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
		$result = Db::name('run')->find($run_id);
		return $result;
	}
	/**
	 * 更新单据信息
	 *
	 * @param $wf_fid  运行的id
	 * @param $wf_type 业务表名
	 * @param $status  单据状态
	 */
	public static function UpdateBill($wf_fid,$wf_type,$status = 1)
	{
		$result = Db::name($wf_type)->where('id',$wf_fid)->update(['status'=>$status,'uptime'=>time()]);
		 if(!$result){
            return  false;
        }
        return $result;
		
	} 
	
	public static function worklist()
	{
		$result = Db::name('run')->where('status',0)->select();
		foreach($result as $k=>$v)
		{
			$result[$k]['flow_name'] = Db::name('flow')->where('id',$v['flow_id'])->value('flow_name');
			$process = Db::name('flow_process')->where('id',$v['run_flow_process'])->find();
			if($process['auto_person'] == 4){
				$result[$k]['user'] =$process['auto_sponsor_text'];
				}else{
				$result[$k]['user'] =$process['auto_role_text'];
			}
		}
        return $result;
		
	}
	
	
}