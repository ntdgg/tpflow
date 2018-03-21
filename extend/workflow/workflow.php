<?php
/**
* 工作流类库
*/
namespace workflow;

define ( 'BEASE_URL', realpath ( dirname ( __FILE__ ) ) );

//配置文件
require_once BEASE_URL . '/config/config.php';
//数据库操作
require_once BEASE_URL . '/db/InfoDB.php';
require_once BEASE_URL . '/db/FlowDb.php';
require_once BEASE_URL . '/db/ProcessDb.php';
//类库

require_once BEASE_URL . '/class/ConfigContext.php';
require_once BEASE_URL . '/class/InterfaceNotice.php';
require_once BEASE_URL . '/class/TaskService.php';

//配置全局类
$configContext = ConfigContext::getInstance();
$configContext->setEmailObj(@$email);

	/**
	 * 根据单据ID获取流程信息
	 */
	class workflow{
		/**
		 * 根据业务类别获取工作流
		 * @param  $etuid 实例id
		 * @param  $ssn 工号
		 */
		function getWorkFlow($type)
		{
			return FlowDb::getWorkflowByType($type);
		}
		/**
		 *流程发起
		 *
		 **/
		function startworkflow($wf_id,$wf_fid,$wf_type)
		{
			//判断流程是否存在
			$wf = FlowDb::getWorkflow($wf_id);
			if(!$wf){
				return ['msg'=>'未找到工作流！','code'=>'-1'];
			}
			//判断单据是否存在
			$wf = InfoDB::getbill($wf_fid,$wf_type);
			if(!$wf){
				return ['msg'=>'单据不存在！','code'=>'-1'];
			}
			
			//根据流程获取流程第一个步骤
			$wf_process = ProcessDb::getWorkflowProcess($wf_id);
			if(!$wf_process){
				return ['msg'=>'流程设计出错，未找到第一步流程，请联系管理员！','code'=>'-1'];
			}
			//满足要求，发起流程
			$wf_run = InfoDB::addWorkflowRun($wf_id,$wf_process['id'],$wf_fid,$wf_type);
			if(!$wf_run){
				return ['msg'=>'流程发起失败，数据库操作错误！！','code'=>'-1'];
			}
			//添加流程步骤日志
			$wf_process_log = InfoDB::addWorkflowProcess($wf_id,$wf_process['id'],$wf_run);
			if(!$wf_process_log){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
			//添加流程日志
			$run_cache = InfoDB::addWorkflowCache($wf_run,$wf,$wf_process,$wf_fid);
			if(!$run_cache){
				return ['msg'=>'流程步骤操作记录失败，数据库错误！！！','code'=>'-1'];
			}
			
			$run_log = InfoDB::AddrunLog(1,$wf_run[0]['id'],$wf_fid,$wf_type,'工作流发起');
			
			$configContext = ConfigContext::getInstance();
			//发起消息通知
			$email = $configContext->getEmailObj('default');
			if($email){
				$email->noticeNextUser();
			}
			return ['run_id'=>$wf_run,'msg'=>'success','code'=>'1'];
		}
		/**
		  * 流程状态查询
		  * @$wf_fid 单据编号
		  * @$wf_type 单据表 
		  **/
		function workflowInfo($wf_fid,$wf_type)
		{
			$workflowInfo = array ();
			if ($wf_fid == '' || $wf_type == '') {
				return ['msg'=>'单据编号，单据表不可为空！','code'=>'-1'];
			}
			$wf = InfoDB::workflowInfo($wf_fid,$wf_type);
			return $wf;
		}
		/*
		 * 获取下一步骤信息
		 *
		 *
		 *
		 **/
		function workdoaction($config)
		{
			if( @$config['run_id']=='' || @$config['run_flow_process']==''){
		       	throw new \Exception ( "config参数信息不全！" );
			}
			$taskService = new TaskService();//工作流服务
			$wf_actionid = $config['submit_to_save'];
			if ($wf_actionid == "ok") {//提交处理
				$taskService->doTask($config);
			} else if ($wf_actionid == "back") {//退回处理
				$taskService->reject();
			} else if ($wf_actionid == "sing") {//会签
				$taskService->freeRoute();
			} else { //通过
				throw new \Exception ( "参数出错！" );
			}
			return $config;
		}
		
}