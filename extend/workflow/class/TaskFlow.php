<?php
namespace workflow;
class TaskFlow{
	/**
	 * 执行任务
	 *
	 */
	public function doTask($config) {
		//任务全局类
		$wf_title = $config['wf_title'];
		$wf_fid = $config['wf_fid'];
		$wf_type = $config['wf_type'];
		$flow_id = $config['flow_id'];
		$run_flow_process = $config['run_flow_process'];
		$run_id = $config['run_id'];
		$check_con = $config['check_con'];
		$submit_to_save = $config['submit_to_save'];
		$action = FlowDb::getflowprocess($run_flow_process);//获取当前任务
		if(!empty($action['process_to'])){//判断是否为最后
			//结束该流程
			$result = Db::execute('update sb_ad set status = "1" where id = 1 ');
			//        dump($result);       
			
			
			//获取下一个流程信息
			
			//记录下一个流程
			
			//消息通知
			
			//日志记录
		
			}else{ //结束流程
			//结束该流程
			
			//消息通知发起人
		}
	}
	public function 

}