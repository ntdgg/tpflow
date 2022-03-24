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

class User
{
	
	protected $mode;
	
	public function __construct()
	{
		if (unit::gconfig('wf_db_mode') == 1) {
			$className = '\\tpflow\\custom\\think\\AdapteeUser';
		} else {
			$className = unit::gconfig('wf_db_namespace') . 'AdapteeUser';
		}
		$this->mode = new $className();
	}
	
	/**
	 * 获取用户列表
	 *
	 */
	public static function GetUser()
	{
		return (new User())->mode->GetUser();
	}

    /**
     * 获取用户列表
     *
     */
    public static function searchRoleIds($role='')
    {
        return (new User())->mode->searchRoleIds($role);
    }
	
	/**
	 * 获取角色列表
	 *
	 */
	public static function GetRole()
	{
		return (new User())->mode->GetRole();
	}
	
	/**
	 * 获取AJAX信息
	 *
	 */
	public static function AjaxGet($type, $keyword)
	{
		return (new User())->mode->AjaxGet($type, $keyword);
	}
	
	/**
	 * 查询用户消息
	 *
	 */
	public static function GetUserInfo($id)
	{
		return (new User())->mode->GetUserInfo($id);
	}
	
	/**
	 * 查询用户名称
	 *
	 */
	public static function GetUserName($uid)
	{
		return (new User())->mode->GetUserName($uid);
	}
}