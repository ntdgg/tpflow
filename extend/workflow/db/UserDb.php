<?php
/**
*+------------------
* Tpflow 用户信息
*+------------------
* Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
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
		require ( BEASE_URL . '/config/config.php');// 
		return  Db::name($user_table['user'][0])->field($user_table['user'][3])->select();
	}
	public static function GetRole() 
	{
		require ( BEASE_URL . '/config/config.php');// 
		return  Db::name($user_table['role'][0])->field($user_table['role'][3])->select();
	}
	public static function AjaxGet($type,$keyword){
		require ( BEASE_URL . '/config/config.php');// 
		if($type=='user'){
			$map[$user_table['user'][4]]  = array('like','%'.$keyword.'%');
			return Db::name($user_table['user'][0])->where($map)->field($user_table['user'][3])->select();
		 }else{
			$map[$user_table['role'][4]]  = array('like','%'.$keyword.'%');
			return Db::name($user_table['role'][0])->where($map)->field($user_table['role'][3])->select();
		 }
	}
	public static function GetUserInfo($id) 
	{
		require ( BEASE_URL . '/config/config.php');// 
		return  Db::name($user_table['user'][0])->where($user_table['user'][1],'eq',$id)->field($user_table['user'][3])->find();
	}
}