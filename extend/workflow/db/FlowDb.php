<?php
/**
 * 工作流信息处理
 */
namespace workflow;

use think\Db;
use think\facade\Session;

class FlowDb{
	
	public static $prefix = 'leipi_';
	/**
	 * 获取类别工作流
	 * @param $wf_type
	 */
	public static function getWorkflowByType($wf_type) 
	{
		$workflow = array ();
		if ($wf_type == '') {
			return $workflow;
		}
		$wf_sql = "select flow_name,id from ".self::$prefix."flow where is_del=0  and type='".$wf_type."'";
		return  Db::query ($wf_sql );
	}
	/**
	 * 获取流程信息
	 * @param $fid
	 */
	public static function GetFlowInfo($fid)
	{
		if ($fid == '') {
			return false;
		}
		$wf_sql = "select flow_name from ".self::$prefix."flow where id='".$fid."'";
		$info = Db::query ($wf_sql );	
		if($info){
			return  $info[0]['flow_name'];
			}else{
			return  false;
		}
	}
	/**
	 * 判断工作流是否存在
	 * @param $wf_id
	 */
	public static function getWorkflow($wf_id) 
	{
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
	 * 获取步骤信息
	 * @param $id
	 */
	public static function getflowprocess($id) 
	{
		if ($id == '') {
			return false;
		}
		$wf_sql = "select * from ".self::$prefix."flow_process where  id='".$id."'";
		$data =Db::query ($wf_sql );
		if($data){
			return  $data;
			}else{
			return  false;
		}
	}
}