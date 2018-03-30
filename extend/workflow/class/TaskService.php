<?php
namespace workflow;
require_once BEASE_URL . '/class/Command.php';
class TaskService{
	function __construct(){
		$this->init();
	}
	/**
	 * 
	 * 流程初始化
	 */
	public function init(){
		require_once BEASE_URL . '/class/FlowInit.php';
		$command = new FlowInit();
		$command->init();
	}
	/**
	 * 
	 * 普通流程通过
	 */
	public function doTask($config,$uid){
		require_once BEASE_URL . '/class/command/TaskFlow.php';
		$command = new TaskFlow();
		$command->doTask($config,$uid);
	}
	/**
	 * 
	 * 驳回
	 */
	public function doBack($config){
		require_once BEASE_URL . '/class/command/BackFlow.php';
		$command = new BackFlow();
		$command->doTask($config);
	}
	/**
	 * 
	 * 收回
	 */
	public function recycle(){
		require_once BEASE_URL . '/service/command/RecycleCommand.php';
		$command = new RecycleCommand();
		$command->execute();
	}
	/**
	 * 
	 * 会签发起
	 */
	public function freeRoute(){
		require_once BEASE_URL . '/service/command/FreeRouteCommand.php';
		$command = new FreeRouteCommand();
		$command->execute();
	}
	/**
	 * 
	 * 循环会签发起
	 */
	public function otherRoute(){
		require_once BEASE_URL . '/service/command/OtherRouteCommand.php';
		$command = new OtherRouteCommand();
		$command->execute();
	}
	/**
	 * 
	 * 会签确认
	 */
	public function doFreeRoute(){
		require_once BEASE_URL . '/service/command/DoFreeRouteCommand.php';
		$command = new DoFreeRouteCommand();
		$command->execute();
	}
	/**
	 * 
	 * 循环会签确认
	 */
	public function doOtherRoute(){
		require_once BEASE_URL . '/service/command/DoOtherRouteCommand.php';
		$command = new DoOtherRouteCommand();
		$command->execute();
	}
	/**
	 * 
	 * 直接结束流程
	 */
	public function doForceFinish(){
		require_once BEASE_URL . '/service/command/DoForceFinishCommand.php';
		$command = new DoForceFinishCommand();
		$command->execute();
	}
	/**
	 * 
	 * 记录审批意见
	 */
	public function worklowLog(){
		$commandContext = CommandContext::getInstance();
		$wf_etuid = $commandContext->getEtuid();
		$comment = $commandContext->getComment();
		$router = $commandContext->getRouter();
		$wf_status = $commandContext->getFlowStatus();
		$wf_comment = $comment['comment'];
		$wf_id = $comment['stepid'];
		$wf_stepName = $comment['stepName'];
		$wf_uid = $comment['wfuid'];
		$actionid = $comment['actionid'];
		$taskDB = TaskDB::getInstance();
		$taskDB->worklowLog($wf_etuid, $wf_id,$wf_stepName,$wf_status,$wf_comment, WF_LOG_TYPE_APP, $actionid, $wf_uid);
	}
	
}