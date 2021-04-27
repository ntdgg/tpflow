<?php
/**
 *+------------------
 * Tpflow 工作流步骤
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\custom\think;
;

use think\facade\Db;

class AdapteeProcess
{
	
	function find($id, $field = '*')
	{
		return Db::name('wf_flow_process')->field($field)->find($id);
	}
	
	function finds($ids, $field = '*')
	{
		return Db::name('wf_flow_process')->field($field)->where('id', 'in', $ids)->select()->all();
	}
	
	function SearchFlowProcess($where = [], $field = '*', $order = '', $limit = 0)
	{
		if ($limit > 0) {
			return Db::name('wf_flow_process')->where($where)->field($field)->order($order)->limit($limit)->select();
		} else {
			return Db::name('wf_flow_process')->where($where)->field($field)->order($order)->select()->all();
		}
	}
	
	function EditFlowProcess($where, $data)
	{
		return Db::name('wf_flow_process')->where($where)->update($data);
	}
	
	function DelFlowProcess($where)
	{
		return Db::name('wf_flow_process')->where($where)->delete();
	}
	
	function AddFlowProcess($data)
	{
		return Db::name('wf_flow_process')->insertGetId($data);
	}
	
	function get_userprocess($uid, $role)
	{
		return Db::name('wf_flow_process')->alias('f')
			->join('wf_flow w', 'f.flow_id = w.id')
			->where('find_in_set(:asi,f.auto_sponsor_ids)', ['asi' => $uid])
			->whereOr('find_in_set(:rui,f.range_user_ids)', ['rui' => $uid])
			->whereOr('find_in_set(:ari,f.auto_role_ids)', ['ari' => $role])
			->field('f.id,f.process_name,f.flow_id,w.flow_name')
			->select();;
	}
}