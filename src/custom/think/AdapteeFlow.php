<?php
/**
 *+------------------
 * Tpflow 流信息处理
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;
use tpflow\lib\unit;

class AdapteeFlow
{
	
	function find($id, $field = '*')
	{
		return Db::name('wf_flow')->field($field)->find($id);
	}

    function del($id)
    {
        return Db::name('wf_flow')->delete($id);
    }
	
	function AddFlow($data)
	{
		return Db::name('wf_flow')->insertGetId($data);
	}
	
	function EditFlow($data)
	{
        $data['add_time'] = time();
		return Db::name('wf_flow')->update($data);
	}
	
	function SearchFlow($where = [], $field = '*')
	{
		return Db::name('wf_flow')->where($where)->field($field)->select();
	}
	
	function ListFlow($map, $page, $rows, $order)
	{
		$offset = ($page - 1) * $rows;
		$list = Db::name('wf_flow')->where($map)->order($order)->limit($offset, $rows)->select()->all();
		$count = Db::name('wf_flow')->where($map)->count();
		return ['total' => $count, 'rows' => $list];
	}
	
	function get_db_column_comment($table_name = '', $field = true, $table_schema = '')
	{
		$table_schema = empty($table_schema) ? unit::gconfig('database') : $table_schema;
		$table_name = unit::gconfig('prefix') . $table_name;
		$fieldName = $field === true ? 'allField' : $field;
		$cacheKeyName = 'db_' . $table_schema . '_' . $table_name . '_' . $fieldName;
		$param = [
			$table_name,
			$table_schema
		];
		$columeName = '';
		if ($field !== true) {
			$param[] = $field;
			$columeName = "AND COLUMN_NAME = ?";
		}
		$res = Db::query("SELECT COLUMN_NAME as field,column_comment as comment FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND table_schema = ? $columeName", $param);
		$result = array();
		foreach ($res as $k => $value) {
			foreach ($value as $key => $v) {
				if ($value['comment'] != '') {
					$result[$value['field']] = $value['comment'];
				}
			}
		}
		return count($result) == 1 ? reset($result) : $result;
	}
}