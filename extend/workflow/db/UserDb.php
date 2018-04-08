<?php
/**
*+------------------
* 用户信息
*+------------------ 
*/
namespace workflow;

use think\Db;

class UserDb{
	/**
	 * 获取用户信息
	 *
	 * @param $wf_type
	 */
	public static function GetUser() 
	{
		return  Db::name('user')->where('status','eq',0)->field('id,username,role')->select();
	}
}