<?php
/**
*+------------------
* 工作流任务服务
*+------------------ 
*/

namespace workflow;

class TaskService{
	/**
	 * 普通流程通过
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doTask($config,$uid){
		require_once BEASE_URL . '/class/command/TaskFlow.php';
		$command = new TaskFlow();
		$command->doTask($config,$uid);
	}
	/**
	 * 流程驳回
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doBack($config,$uid){
		require_once BEASE_URL . '/class/command/BackFlow.php';
		$command = new BackFlow();
		$command->doTask($config,$uid);
	}
	/**
	 * 会签操作
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doSing($config,$uid){
		require_once BEASE_URL . '/class/command/SingFlow.php';
		$command = new SingFlow();
		$command->doTask($config,$uid);
	}
	
	/**
	 * 普通流程通过
	 * 
	 * @param  $config 参数信息
	 * @param  $uid  用户ID
	 */
	public function doSingEnt($config,$uid,$wf_actionid){
		require_once BEASE_URL . '/class/command/SingFlow.php';
		$command = new SingFlow();
		$command->doSingEnt($config,$uid,$wf_actionid);
	}
}