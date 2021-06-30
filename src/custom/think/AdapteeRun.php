<?php
/**
 *+------------------
 * Tpflow Run
 *+------------------
 * Copyright (c) 2006~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeRun
{
	
	
	function AddRun($data)
	{
		return Db::name('wf_run')->insertGetId($data);
	}
	
	function FindRun($where = [], $field = '*')
	{
		return Db::name('wf_run')->where($where)->field($field)->find();
	}
	
	function FindRunId($id, $field = '*')
	{
		return Db::name('wf_run')->field($field)->find($id);
	}
	
	function SearchRun($where = [], $field = '*')
	{
		return Db::name('wf_run')->where($where)->field($field)->select()->toArray();
	}
	
	function EditRun($id, $data)
	{
		return Db::name('wf_run')->where('id', $id)->update($data);
	}
	
	/*run_process表操作接口代码*/
	function FindRunProcessId($id, $field = '*')
	{
		return Db::name('wf_run_process')->field($field)->find($id);
	}
	
	function FindRunProcess($where = [], $field = '*')
	{
		return Db::name('wf_run_process')->where($where)->field($field)->find();
	}
	
	function AddRunProcess($data)
	{
		return Db::name('wf_run_process')->insertGetId($data);
	}
	
	function SearchRunProcess($where = [], $field = '*')
	{
		return Db::name('wf_run_process')->where($where)->field($field)->select();
	}
	
	function EditRunProcess($where = [], $data = [])
	{
		return Db::name('wf_run_process')->where($where)->update($data);
	}
	
	/*FindRunSign表操作接口代码*/
	function FindRunSign($where = [], $field = '*')
	{
		return Db::name('wf_run_sign')->where($where)->field($field)->find();
	}
	
	function AddRunSing($data)
	{
		return Db::name('wf_run_sign')->insertGetId($data);
	}
	
	function EndRunSing($sing_sign, $check_con)
	{
		return Db::name('wf_run_sign')->where('id', $sing_sign)->update(['is_agree' => 1, 'content' => $check_con, 'dateline' => time()]);
	}
	
	function dataRunProcess($map, $field, $order)
	{
		return Db::name('wf_run_process')->alias('f')->join('wf_flow w', 'f.run_flow = w.id')->join('wf_run r', 'f.run_id = r.id')->where($map)->field($field)->order($order)->select();
	}
	
	function dataRunProcessGroup($map, $field, $order, $group)
	{
		return Db::name('wf_run_process')->alias('f')->join('wf_flow w', 'f.run_flow = w.id')->join('wf_run r', 'f.run_id = r.id')->where($map)->field($field)->order($order)->group($group)->select();
	}
	
	
}