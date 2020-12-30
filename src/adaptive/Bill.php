<?php
/**
 *+------------------
 * Tpflow 统一标准接口------单据接口
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace tpflow\adaptive;

use tpflow\lib\unit;

Class Bill{
    
	protected $mode ; 
    public function  __construct($type='Bill'){
		if(unit::gconfig('wf_bill_mode')==1){
			$className = '\\tpflow\\custom\\think\\Adaptee'.$type;
		}else{
			$className = unit::gconfig('wf_bill_namespace').$type;
		}
		$this->mode = new $className();
    }
	/**
	 * 定义获取单据详细信息
	 * @param string $bill_table 表名称
	 * @param int $bill_id id
	 */
	static function getbill($bill_table,$bill_id){
		return (new Bill())->mode->getbill($bill_table,$bill_id);
	}
	/**
	 * 定义获取单据单个字段值
	 * @param string  $bill_table 表名称
	 * @param int $bill_id id
	 * @param string $bill_field 查询参数
	 */
    static function getbillvalue($bill_table,$bill_id,$bill_field){
		return (new Bill())->mode->getbillvalue($bill_table,$bill_id,$bill_field);
	}
	/**
	 * 更新单据信息
	 * @param string $bill_table 表名称
	 * @param int $bill_id id
	 * @param mixed $updata 更新数据
	 */
    static function updatebill($bill_table,$bill_id,$updata){
		return (new Bill())->mode->updatebill($bill_table,$bill_id,$updata);
	}
	/**
	 * 判断单据信息
	 * @param string $bill_table 表名称
	 * @param int $bill_id id
	 * @param array $where 判断条件
	 */
	static function checkbill($bill_table,$bill_id,$where){
		return (new Bill())->mode->checkbill($bill_table,$bill_id,$where);
	}
   
}