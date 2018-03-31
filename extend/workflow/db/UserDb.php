<?php
/**
 * 工作流信息处理
 */
namespace workflow;

use think\Db;
use think\facade\Session;

class UserDb{
	/**
	 * 获取用户信息
	 * @param $wf_type
	 */
	public static function GetUser() 
	{
		return  Db::name('user')->where('status',0)->field('id,username,role')->select();
	}
}