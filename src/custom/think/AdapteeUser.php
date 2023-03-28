<?php
/**
 *+------------------
 * Tpflow 用户信息
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

class AdapteeUser
{
	public static function config($type = 'user')
	{
		return unit::gconfig($type);
	}
	
	/**
	 * 获取用户列表
	 *
	 */
	function GetUser()
	{
		$config = self::config();
		return Db::name($config['db'])->field($config['field'])->select();
	}

    /**
     * 获取用户列表
     *
     */
    function searchRoleIds($role)
    {
        $config = self::config();
        return Db::name($config['db'])->where('role','in',$role)->column('id');
    }
	
	/**
	 * 获取角色列表
	 *
	 */
	function GetRole()
	{
		$config = self::config('role');
		return Db::name($config['db'])->field($config['field'])->select();
	}

    /**
     * 查询用户消息
     *
     */
    function GetRoleInfo($id)
    {
        $config = self::config('role');
        return Db::name($config['db'])->where($config['key'], $id)->field($config['field'])->find();
    }
	
	/**
	 * 获取AJAX信息
	 *
	 */
	function AjaxGet($type, $keyword)
	{
		
		if ($type == 'user') {
			$config = self::config();
			$map[] = [$config['searchwhere'], 'like', '%' . $keyword . '%'];
			return Db::name($config['db'])->where($map)->field($config['field'])->select();
		} else {
			$config = self::config('role');
			$map[] = [$config['searchwhere'], 'like', '%' . $keyword . '%'];
			return Db::name($config['db'])->where($map)->field($config['field'])->select();
		}
	}
	
	/**
	 * 查询用户消息
	 *
	 */
	function GetUserInfo($id)
	{
		$config = self::config();
		return Db::name($config['db'])->where($config['key'], $id)->field($config['field'])->find();
	}
	
	/**
	 * 查询用户名称
	 *
	 */
	function GetUserName($uid)
	{
		$config = self::config();
		return Db::name($config['db'])->where($config['key'], $uid)->value($config['getfield']);
	}
	
}