<?php
/**
*+------------------
* Tpflow 流信息处理
*+------------------
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------ 
*/
namespace tpflow\custom\think;

use think\facade\Db;
use tpflow\lib\unit;


class AdapteeInfo{
	
	/**
	 * 接入工作流的类别
	 *
	 */
	function get_wftype()
	{
		if(unit::gconfig('wf_type_mode')==0){
			return Db::query("select replace(TABLE_NAME,'".unit::gconfig('prefix')."','')as name,TABLE_COMMENT as title from information_schema.tables where table_schema='".unit::gconfig('database')."' and TABLE_COMMENT like '".unit::gconfig('work_table')."%';");
		}else{
			return unit::gconfig('wf_type_data');
		}
		
	}
}