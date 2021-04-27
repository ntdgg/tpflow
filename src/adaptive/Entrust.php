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
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Entrust
{
	
	protected $mode;
	
	public function __construct()
	{
		if (unit::gconfig('wf_db_mode') == 1) {
			$className = '\\tpflow\\custom\\think\\AdapteeEntrust';
		} else {
			$className = unit::gconfig('wf_db_namespace') . 'AdapteeEntrust';
		}
		$this->mode = new $className();
	}
	
	/**
	 * get_Entrust 获取代理信息
	 * @param array $whereRaw raw查询条件
	 * @param array $map 查询条件
	 */
	static function get_Entrust($map = [], $whereRaw = '')
	{
		return (new Entrust())->mode->get_Entrust($map, $whereRaw);
	}
	
	/**
	 * lists 列表信息
	 * @param array $data POST提交的数据
	 */
	static function lists()
	{
		return (new Entrust())->mode->lists();
	}
	
	/**
	 * find 信息查询
	 * @param array $data POST提交的数据
	 */
	static function find($id)
	{
		return (new Entrust())->mode->find($id);
	}
	
	/**
	 * Add 新增
	 * @param array $data POST提交的数据
	 */
	static function Add($data)
	{
		return (new Entrust())->mode->Add($data);
	}
	
	/**
	 * save_rel 保存关系
	 * @param array $data 步骤信息
	 * @param int $run_process 运行中的步骤
	 */
	static function save_rel($data, $run_process)
	{
		return (new Entrust())->mode->save_rel($data, $run_process);
	}
	
	/**
	 * change 权限转换
	 * @param array $info 修改信息
	 */
	static function change($info)
	{
		return (new Entrust())->mode->change($info);
	}
	
}