<?php
declare (strict_types=1);

namespace tpflow\custom\think;
/**
 *+------------------
 * Tpflow 单据实例化类
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

use think\facade\Db;
use tpflow\exception\FlowException;
use tpflow\lib\unit;

class AdapteeBill
{
	
	function getbill($bill_table, $bill_id)
	{
		if ($bill_table == '' || $bill_id == '') {
			return false;
		}
		$info = Db::name($bill_table)->find($bill_id);
		if ($info) {
			return $info;
		} else {
			return false;
		}
	}
	
	function getbillvalue($bill_table, $bill_id, $bill_field)
	{
		$result = Db::name($bill_table)->where('id', $bill_id)->value($bill_field);
		if ($result =='' && $bill_table<>'wf_flow') {
            throw new FlowException('流程未找到审批人',400,[
               'bill_table'=>$bill_table,
               'bill_id'=>$bill_id,
               'bill_field'=>$bill_field
            ]);
		}
		return $result;
	}
	
	function updatebill($bill_table, $bill_id, $updata)
	{
		$result = Db::name($bill_table)->where('id', $bill_id)->update(['status' => $updata, 'uptime' => time()]);
		if (!$result) {
			return false;
		}
		return $result;
	}
	
	function checkbill($bill_table, $bill_id, $where)
	{
		return Db::name($bill_table)->whereRaw($where)->where('id', $bill_id)->find();
	}

    function tablename($table){
        if (unit::gconfig('wf_type_mode') == 0) {
            $data =  Db::query("select replace(TABLE_NAME,'" . unit::gconfig('prefix') . "','')as name,TABLE_COMMENT as title from information_schema.tables where table_schema='" . unit::gconfig('database') . "' and TABLE_COMMENT like '" . unit::gconfig('work_table') . "%' and TABLE_NAME not like '%_bak';");
        } else {
            $data =  unit::gconfig('wf_type_data');
        }
        $dataArray = [];
        foreach ($data as $k => $v) {
            $dataArray[$v['name']] = str_replace('[work]', '', $v['title']);
        }
        return $dataArray[$table] ?? '';
    }
	
	
}