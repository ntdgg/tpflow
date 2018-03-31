<?php
namespace workflow;
require_once BEASE_URL . '/class/Command.php';
class TaskService{
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
	public function doBack($config,$uid){
		require_once BEASE_URL . '/class/command/BackFlow.php';
		$command = new BackFlow();
		$command->doTask($config);
	}
	/**
	 * 
	 * 会签发起
	 */
	public function doSing($config,$uid){
		require_once BEASE_URL . '/class/command/SingFlow.php';
		$command = new SingFlow();
		$command->doTask($config,$uid);
	}
	
	/**
	 * 
	 * 会签确认
	 */
	public function doSingEnt($config,$uid,$wf_actionid){
		require_once BEASE_URL . '/class/command/SingFlow.php';
		$command = new SingFlow();
		$command->doSingEnt($config,$uid,$wf_actionid);
	}
}