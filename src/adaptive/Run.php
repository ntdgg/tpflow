<?php
/**
 *+------------------
 * Tpflow 统一标准接口------代理模式数据库操作统一接口
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Run
{
	
	protected $mode;
	
	public function __construct()
	{
		if (unit::gconfig('wf_db_mode') == 1) {
			$className = '\\tpflow\\custom\\think\\AdapteeRun';
		} else {
			$className = unit::gconfig('wf_db_namespace') . 'AdapteeRun';
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
	static function FindRunId($id, $field = '*')
	{
		return (new Run())->mode->FindRunId($id, $field);
	}
	
	/**
	 * 编辑run流程信息
	 *
	 * @param int $id 主键
	 * @param array $data 流程信息
	 */
	static function EditRun($id, $data)
	{
		return (new Run())->mode->EditRun($id, $data);
	}
	
	/**
	 * 查询run流程信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	static function FindRun($where = [], $field = '*')
	{
		return (new Run())->mode->FindRun($where, $field);
	}
	
	/**
	 * 查询run流程信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	static function SearchRun($where = [], $field = '*')
	{
		return (new Run())->mode->SearchRun($where, $field);
	}
	
	/**
	 * 查询run流程信息
	 *
	 * @param int $id
	 */
	static function FindRunProcessId($id, $field = '*')
	{
		return (new Run())->mode->FindRunProcessId($id, $field);
	}
	
	/**
	 * 查询run步骤流程信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	static function FindRunProcess($where = [], $field = '*')
	{
		return (new Run())->mode->FindRunProcess($where, $field);
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
	static function SearchRunProcess($where = [], $field = '*')
	{
		return (new Run())->mode->SearchRunProcess($where, $field);
	}
	
	/**
	 * 编辑run步骤流程信息
	 *
	 * @param array $where 查询条件
	 * @param array $data 数据信息
	 */
	static function EditRunProcess($where, $data)
	{
		return (new Run())->mode->EditRunProcess($where, $data);
	}
	
	/**
	 * 数据处理
	 *
	 * @param array $where 查询条件
	 * @param array $data 数据信息
	 */
    static function dataRunProcess($map,$mapRaw, $field, $order, $group,$page,$limit)
    {
        if ($group != '') {
            return (new Run())->mode->dataRunProcessGroup($map, $field, $order, $group);
        } else {
            return (new Run())->mode->dataRunProcess($map,$mapRaw, $field, $order,$page,$limit);
        }
    }

    /**
     * 会签数据
     *
     * @param array $where 查询条件
     * @param array $data 数据信息
     */
    static function dataRunSing($map,$mapRaw, $field, $order, $group,$page,$limit)
    {
        if ($group != '') {
            //return (new Run())->mode->dataRunProcessGroup($map, $field, $order, $group);
        } else {
            return (new Run())->mode->dataRunSing($map,$mapRaw, $field, $order,$page,$limit);
        }
    }

    static function dataRunCc($page,$limit,$map)
    {
        return (new Run())->mode->dataRunCc($page, $limit,$map);
    }

    static function dataRunMy($uid,$page,$limit,$map)
    {
        return (new Run())->mode->dataRunMy($uid,$page, $limit,$map);
    }

	
	/**
	 * 查询运行中的会签信息
	 *
	 * @param array $where 查询条件
	 * @param string $field 查询字段
	 */
	static function FindRunSign($where = [], $field = '*')
	{
        $FindRunSign = (new Run())->mode->FindRunSign($where, $field);
        $FindRunSign['username'] = User::GetUserInfos($FindRunSign['uid']);
		return $FindRunSign;
	}
	
	/**
	 * 添加会签信息
	 *
	 * @param array $config 会签数据
	 */
	static function AddRunSing($config)
	{
		$data = [
			'run_id' => $config['run_id'],
			'run_flow' => $config['flow_id'],
			'run_flow_process' => $config['run_process'],
			'uid' => $config['wf_singflow'],
            'sign_uids' => $config['wf_singflow'],
			'dateline' => time()
		];
		$run_sign = (new Run())->mode->AddRunSing($data);
		if (!$run_sign) {
			return false;
		}
        //加入消息接口Api
        $msg_api = unit::gconfig('msg_api') ?? '';
        if (class_exists($msg_api)) {
            (new $msg_api())->sing_msg($config['wf_singflow'],$config['run_id']);
        }
		return $run_sign;
	}
	
	/**
	 * 结束会签信息
	 *
	 * @param array $sing_sign 会签数据
	 * @param string $check_con 提交意见
	 */
	static function EndRunSing($sing_sign, $check_con,$xt_ids_val='')
	{
		return (new Run())->mode->EndRunSing($sing_sign, $check_con,$xt_ids_val);
	}
	
	/**
	 * 获取步骤消息
	 *
	 * @param int $pid 运行步骤
	 * @param int $run_id 运行ID
	 */
	static function getprocessinfo($pid, $run_id)
	{
		
		$wf_process = (new Run())->mode->FindRunProcess([['run_id', '=', $run_id], ['run_flow_process', '=', $pid], ['status', '=', 0]]);
		
		if ($wf_process != NULL && $wf_process['auto_person'] == 3) {
			$todo = $wf_process['sponsor_ids'] . '*%*' . $wf_process['sponsor_text'];
		} else {
			$todo = '';
		}
		return $todo;
	}
	
}
