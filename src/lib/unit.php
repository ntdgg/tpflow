<?php
/**
 *+------------------
 * Tpflow 公共类，模板文件
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\lib;


class unit
{
	
	/**
	 * 判断是否是POST
	 *
	 **/
	public static function is_post()
	{
		return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
	}
	
	/**
	 * return_msg 通用数据返回处理
	 * @param $data
	 * @return int|mixed|\think\response\Json
	 */
	public static function return_msg($data)
	{
		
		$className = unit::gconfig('return_msg');
		if ($className == '') {
			if ($_SERVER['REQUEST_METHOD'] == 'JSON' || $_SERVER['REQUEST_METHOD'] == 'POST') {
				return json($data);
			} else {
				return $data;
			}
			
		} else {
			if (!class_exists($className)) {
				return '404,对不起，您的自定义返回类不存在！';
			}
			return (new $className())->Msg($data);
		}
	}
	
	/**
	 * 加载自定义事务驱动文件
	 *
	 * @param string $class 类
	 * @param int $id 单据对应的ID编号
	 * @param int $run_id 运行中的流程ID
	 * @param array $data 步骤类
	 */
	public static function LoadClass($class, $id, $run_id = '', $data = '')
	{
		$className = unit::gconfig('wf_work_namespace') . str_replace('_','',$class);
		if (!class_exists($className)) {
			return -1;
		}
		return new $className($id, $run_id, $data);
	}
	
	/**
	 * 获取定义的信息
	 * @param string $key
	 **/
	public static function getuserinfo($key = '')
	{
		if (unit::gconfig('gateway_mode') == 1) {
			$user_info = ['uid' => session(self::gconfig('user_id')), 'role' => session(self::gconfig('role_id'))];
		} else {
			$className = unit::gconfig('gateway_action');
			if (!class_exists($className)) {
				return -1;
			}
			$user_info = (new $className())->GetUserInfo();;
		}
		if ($user_info['uid'] == '' || $user_info['role'] == '') {
			return -1;
		}
		if ($key == '') {
			return $user_info;
		} else {
			return $user_info[$key] ?? '';
		}
		
	}
	
	/**
	 * 根据键值加载全局配置文件
	 *
	 * @param string $key 键值
	 */
	public static function gconfig($key)
	{
		$file = dirname(dirname(__DIR__) . DIRECTORY_SEPARATOR, 4) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'tpflow.php';
		if (!file_exists($file)) {
			echo 'sorry,config no find!';
			exit;
		}
		$ret = require($file);
		return $ret[$key] ?? '';
	}
	
	/**
	 * 消息返回统一处理
	 *
	 * @param string $msg 返回消息
	 * @param string $code 返回代码 0 成功，1操作失败
	 * @param string $data 返回数据
	 */
	public static function msg_return($msg = "操作成功！", $code = 0, $data = [])
	{
		return ["code" => $code, "msg" => $msg, "data" => $data];
	}
	
	/**
	 * 步骤转换
	 *
	 */
	public static function nexnexprocessinfo($wf_mode, $npi)
	{
		if(isset($npi['process_type']) && $npi['process_type']=='node-end'){
			return '<font color="red">流程终止</font>';
		}
		if ($wf_mode != 2) {
			if ($npi['auto_person'] == 2) {
				return '[协同]'.$npi['process_name'] . '(' . $npi['auto_xt_text'] . ')';
			}
			if ($npi['auto_person'] != 3) {
				//非自由模式
				return $npi['process_name'] . '(' . $npi['todo'] . ')';
			} else {
				$todu = "<select name='todo' id='todo'  class='select'  datatype='*' ><option value=''>请指定办理人员</option>";
				$op = '';
				foreach ($npi['todo']['ids'] as $k => $v) {
					$op .= '<option value="' . $v . '*%*' . $npi['todo']['text'][$k] . '">' . $npi['todo']['text'][$k] . '</option>';
				}
				return $todu . $op . '</select>';;
			}
			$pr = '';
		} else {
			$pr = '[同步]';
			$op = '';
			foreach ($npi as $k => $v) {
				$op .= $v['process_name'] . '(' . $v['todo'] . ')';
			}
			return $pr . $op;
		}
	}
	
	/**
	 * IDS数组转换
	 *
	 * @param string $str 字符串
	 * @param string $dot_tmp 分割字符串
	 */
	public static function ids_parse($str, $dot_tmp = ',')
	{
		if (!$str) return '';
		if (is_array($str)) {
			$idarr = $str;
		} else {
			$idarr = explode(',', $str);
		}
		$idarr = array_unique($idarr);
		$dot = '';
		$idstr = '';
		foreach ($idarr as $id) {
			$id = intval($id);
			if ($id > 0) {
				$idstr .= $dot . $id;
				$dot = $dot_tmp;
			}
		}
		if (!$idstr) $idstr = 0;
		return $idstr;
	}
	
	/**
	 * JSON 转换处理
	 *
	 * @param string json_encode
	 */
	public static function parse_out_condition($json_data)
	{
		if (!$json_data) {
			return [];
		}
		$array = json_decode($json_data, true);
		if (!$array) {
			return [];
		}
		$json_data = array();//重置
		foreach ($array as $key => $value) {
			$condition = '';
			foreach ($value['condition'] as $val) {
				$preg = "/'(data_[0-9]*|checkboxs_[0-9]*)'/s";
				preg_match_all($preg, $val, $temparr);
				
				$condition .= $val;
			}
			$value['condition'] = $condition;
			$json_data[$key] = $value;
		}
		return $json_data;
	}
	
	/**
	 * 获取字段名称
	 */
	public static function get_field_name($field, $field_data)
	{
		$field = trim($field);
		if (!$field) return '';
		$title = '';
		foreach ($field_data as $value) {
			if ($value['plugins'] == 'checkboxs' && $value['parse_name'] == $field) {
				$title = $value['title'];
				break;
			} else if ($value['name'] == $field) {
				$title = $value['title'];
				break;
			}
		}
		return $title;
	}
	
}

