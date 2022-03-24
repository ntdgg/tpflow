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

class Event
{
	
	protected $mode;
	
	public function __construct()
	{
		if (unit::gconfig('wf_db_mode') == 1) {
			$className = '\\tpflow\\custom\\think\\AdapteeEvent';
		} else {
			$className = unit::gconfig('wf_db_namespace') . 'AdapteeEvent';
		}
		$this->mode = new $className();
	}

	static function save($data)
	{
		$ret = (new Event())->mode->save($data,unit::getuserinfo('uid'));
		if($ret['code']==0){
			return self::CreatePHP($data);
		}
		return $ret;
	}
	
	/**
	 * 创建PHP类
	 * @param $sid
	 * @return array
	 */
	static function CreatePHP($data){
		
		$find = (new Event())->mode->select(['type'=>$data['type']]);
		$class = strtolower(str_replace('_','',$data['type']));
		$title =[
			'before'=>'步骤执行前动作',
			'after'=>'步骤执行后动作',
			'cancel'=>'执行取消动作'
		];
		$function = [];
		foreach ($find as $v){
			$function[$v['act']] = $v['code'];
		}
		$before = $function['before'] ?? self::tpl('before');
		$after = $function['after'] ?? self::tpl('after');
		$cancel = $function['cancel'] ?? self::tpl('cancel');
		$template = file_get_contents(BEASE_URL . "/adaptive/Event.tpl");
		$namespace = stripslashes(unit::gconfig('wf_work_namespace'));
		
		$str = str_replace(
				["[namespace]", "[class]", "[before]", "[after]",'[cancel]'],
				[$namespace, $class, $before, $after,$cancel],
				$template
		);
		if(@file_put_contents(root_path(). 'extend/'.$namespace.'/'.$class.'.php' , $str) === false)
		{
			return ['code'=>1,'data'=>'写入文件失败，请检查extend/event/目录是否有权限'];
		}
		/*尝试一下代码错误*/
		try {
			$className = unit::gconfig('wf_work_namespace') . $class;
			new $className(1,1);
		}catch (\Throwable $e) {
			return ['code'=>1,'data'=>'错误代码：'.$e->getMessage().'<br/>错误行号：'.$e->getLine().'<br/>错误文件：'.$e->getFile()];
		}
		return ['code'=>0,'data'=>'success'];
	}
	
	static function getFun($act,$type)
	{
		$find = (new Event())->mode->find(['type'=>$type,'act'=>$act]);
		if($find){
			return ['code'=>0,'data'=>$find['code']];
		}else{
			$data = self::tpl($act);
			return ['code'=>0,'data'=>$data];
		}
	}
	static function tpl($class)
	{
		return 'public function '.$class.'($action=""){



	return ["code"=>0,"msg"=>"success"];
}';
	}
}