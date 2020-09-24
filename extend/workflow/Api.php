<?php
/**
 *+------------------
 * Tpflow 模板驱动类
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace Api;

define('ROOT_PATH',\Env::get('root_path') );
	/**
	 * 
	 */
	class Api{
		public $patch = '';
		public $topconfig = '';
		function __construct() {
			$int_config = int_config();
			$sid = input('sid') ?? 0;
			$g_uid = input('session.'.$int_config['int_user_id']) ?? '9999';
			$g_username = input('session.'.$int_config['int_user_name']) ?? '"admin"';
			$g_role = input('session.'.$int_config['int_user_role']) ?? '9999';
			$this->topconfig = 
			'<script>
			var g_uid='.$g_uid.';
			var g_role='.$g_role.';
			var g_username='.$g_username.';
			var g_sid='.$sid.';
			</script>';
			$this->patch =  ROOT_PATH . 'extend/workflow/view';
			
	   }

		
}