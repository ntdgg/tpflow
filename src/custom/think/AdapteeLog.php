<?php

/**
 *+------------------
 * Tpflow 工作流日志消息
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeLog
{
	
	/**
	 * 工作流审批日志记录
	 *
	 **/
	function AddrunLog($data)
	{
		$ret = Db::name('wf_run_log')->insertGetId($data);
		if (!$ret) {
			return false;
		}
		return $ret;
	}
	
	function SearchRunLog($wf_fid, $wf_type)
	{
		return Db::name('wf_run_log')->where('from_id', $wf_fid)->where('from_table', $wf_type)->order('id desc')->select()->all();
	}
	
	
}