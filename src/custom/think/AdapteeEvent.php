<?php

/**
 *+------------------
 * Tpflow 工作流日志消息
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeEvent
{
	
	/**
	 * 工作流审批日志记录
	 *
	 **/
	function add($data)
	{
		$ret = Db::name('wf_event')->insertGetId($data);
		if (!$ret) {
			return false;
		}
		return $ret;
	}
	function find($where=[])
	{
		return Db::name('wf_event')->where($where)->find();
	}
	function select($where=[])
	{
		return Db::name('wf_event')->where($where)->select()->toArray();
	}
	function save($data,$uid)
	{
		$find = Db::name('wf_event')->where('type',$data['type'])->where('act',$data['fun'])->find();
		if($find){
			$post = [
				'code'=>$data['code'],
				'uptime'=>time()
			];
			$ret = Db::name('wf_event')->where('id',$find['id'])->update($post);
			if(!$ret){
				return ['code'=>1,'msg'=>'更新失败！'];
			}
		}else{
			$post = [
				'type'=>$data['type'],
				'act'=>$data['fun'],
				'uid'=>$uid,
				'code'=>$data['code'],
				'uptime'=>time()
			];
			$ret = Db::name('wf_event')->insertGetId($post);
			if(!$ret){
				return ['code'=>1,'msg'=>'更新失败！'];
			}
		}
		return ['code'=>0,'msg'=>'更新失败！'];
	}
	
	
}