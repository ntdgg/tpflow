<?php
/**
 * 工作流信息处理
 */
namespace workflow;

use think\Db;
use think\facade\Session;

class FlowDb{
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
		$info = Db::name('flow')->where('is_del',0)->where('status',0)->where('type',$wf_type)->select();
		return  $info;
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
		$info = Db::name('flow')->find($fid);		
		if($info){
			return  $info['flow_name'];
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
		$info = Db::name('flow')->find($wf_id);
		if($info){
			return  $info;
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
		$info = Db::name('flow_process')->field('*')->find($id);
		if($data){
			return  $data;
			}else{
			return  false;
		}
	}
}