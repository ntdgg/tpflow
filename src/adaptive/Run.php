<?php
/**
 *+------------------
 * Tpflow 统一标准接口------代理模式数据库操作统一接口
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace tpflow\adaptive;

use tpflow\lib\unit;

Class Run{
    
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('wf_db_mode')==1){
			$className = '\\tpflow\\custom\\think\\AdapteeRun';
		}else{
			$className = unit::gconfig('wf_db_namespace').'AdapteeRun';
		}
		$this->mode = new $className();
    }
	/**
	 * 添加运行步骤
	 *
	 * @param array $data run信息
	 */
	static function AddRun($data)
    {
       return (new Run())->mode->AddRun($data);
    }
	/**
	 * 查询run流程信息
	 *
	 * @param int $id 主键
	 */
	static function FindRunId($id,$field='*')
    {
       return (new Run())->mode->FindRunId($id,$field);
    }
	/**
	 * 编辑run流程信息
	 *
	 * @param int $id 主键
	 * @param array $data 流程信息
	 */
	static function  EditRun($id,$data)
    {
       return (new Run())->mode->EditRun($id,$data);
    }
	/**
	 * 查询run流程信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
    static function FindRun($where=[],$field='*')
    {
		return (new Run())->mode->FindRun($where,$field);
    }
	/**
	 * 查询run流程信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	 static function SearchRun($where=[],$field='*')
    {
		return (new Run())->mode->SearchRun($where,$field);
    }
	/**
	 * 查询run流程信息
	 *
	 * @param int $id
	 */
	static function FindRunProcessId($id,$field='*')
    {
       return (new Run())->mode->FindRunProcessId($id,$field);
    }
	/**
	 * 查询run步骤流程信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	static function FindRunProcess($where=[],$field='*')
    {
		return (new Run())->mode->FindRunProcess($where,$field);
    }
	/**
	 * 添加run步骤流程信息
	 *
	 * @param array $data 数据信息
	 */
	static function AddRunProcess($data)
    {
       return (new Run())->mode->AddRunProcess($data);
    }
	/**
	 * 查询run步骤流程信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	static function SearchRunProcess($where=[],$field='*')
    {
       return (new Run())->mode->SearchRunProcess($where,$field);
    }
	/**
	 * 编辑run步骤流程信息
	 *
	 * @param array $where 查询条件
	 * @param array $data  数据信息
	 */
	static function EditRunProcess($where,$data)
    {
       return (new Run())->mode->EditRunProcess($where,$data);
    }
	/**
	 * 查询运行中的会签信息
	 *
	 * @param array $where 查询条件
	 * @param string $field  查询字段
	 */
	static function FindRunSign($where=[],$field='*')
    {
		return (new Run())->mode->FindRunSign($where,$field);
    }
	/**
	 * 添加会签信息
	 *
	 * @param array $config 会签数据
	 */
	static function AddRunSing($config)
    {
		$data = [
			'run_id'=>$config['run_id'],
			'run_flow'=>$config['flow_id'],
			'run_flow_process'=>$config['run_process'],
			'uid'=>$config['wf_singflow'],
			'dateline'=>time()
		];
		$run_sign = (new Run())->mode->AddRunSing($data);
		if(!$run_sign){
            return  false;
        }
        return $run_sign;
    }
	/**
	 * 结束会签信息
	 *
	 * @param array $sing_sign 会签数据
	 * @param string $check_con 提交意见
	 */
	static function EndRunSing($sing_sign,$check_con)
    {
       return (new Run())->mode->EndRunSing($sing_sign,$check_con);
    }
	/**
	 * 获取步骤消息
	 *
	 * @param int $pid 运行步骤
	 * @param int $run_id 运行ID
	 */
	static function getprocessinfo($pid,$run_id){
			
		$wf_process = (new Run())->mode->FindRunProcess([['run_id','=',$run_id],['run_flow_process','=',$pid],['status','=',0]]);
	
		if($wf_process  != NULL && $wf_process['auto_person']==3){
			$todo = $wf_process['sponsor_ids'].'*%*'.$wf_process['sponsor_text'];
			}else{
			$todo = '';
		}
		return $todo;
	}
	
}