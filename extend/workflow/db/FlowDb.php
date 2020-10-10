<?php
/**
 *+------------------
 * Tpflow 流信息处理
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace workflow;

use think\Db;
use think\facade\Session;

class FlowDb
{
    /**
     * 获取类别工作流
     *
     * @param $wf_type
     */
    public static function getWorkflowByType($wf_type)
    {
        $workflow = array();
        if ($wf_type == '') {
            return $workflow;
        }
        $info = Db::name('flow')->where('is_del', 'eq', 0)->where('status', 'eq', 0)->where('type', 'eq', $wf_type)->select();
        return $info;
    }

    /**
     * 获取流程信息
     *
     * @param $fid
     */
    public static function GetFlowInfo($fid)
    {
        if ($fid == '') {
            return false;
        }
        $info = Db::name('flow')->find($fid);
        if ($info) {
            return $info['flow_name'];
        } else {
            return false;
        }
    }

    /**
     * 判断工作流是否存在
     *
     * @param $wf_id
     */
    public static function getWorkflow($wf_id)
    {
        if ($wf_id == '') {
            return false;
        }
        $info = Db::name('flow')->find($wf_id);
        if ($info) {
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 获取步骤信息
     *
     * @param $id
     */
    public static function getflowprocess($id)
    {
        if ($id == '') {
            return false;
        }
        $info = Db::name('flow_process')->field('*')->find($id);
        if ($info) {
            return $info;
        } else {
            return false;
        }
    }

    /**
     * API获取工作流列表
     * API接口调用
     */
    public static function GetFlow($info = '')
    {
        if ($info == '') {
            $list = Db::name('flow')->order('id desc')->where('is_del', '0')->paginate('10');
            $list->each(function ($item, $key) {
                $item['edit'] = Db::name('run')->where('flow_id', $item['id'])->where('status', '0')->value('id');
                return $item;
            });
        } else {
            $list = Db::name('flow')->find($info);
        }
        return $list;
    }

    /**
     * API 新增工作流
     * @param $data POST提交的数据
     */
    public static function AddFlow($data)
    {
        $id = Db::name('flow')->insertGetId($data);
        if ($id) {
            return ['code' => 0, 'data' => $id];
        } else {
            return ['code' => 1, 'data' => 'Db0001-写入数据库出错！'];
        }
    }

    /**
     * API 编辑工作流
     * @param $data POST提交的数据
     */
    public static function EditFlow($data)
    {
        $id = Db::name('flow')->update($data);
        if ($id) {
            return ['code' => 0, 'data' => $id];
        } else {
            return ['code' => 1, 'data' => 'Db0001-写入数据库出错！'];
        }
    }
	 /**
     * 获取所有步骤信息
     * @param $flow_id 
     */
    public static function ProcessAll($flow_id)
    {
        $list = Db::name('flow_process')->where('flow_id', $flow_id)->order('id asc')->select();
        $process_data = [];
        $process_total = 0;
        foreach ($list as $value) {
            $process_total += 1;
            $style = json_decode($value['style'], true);
			$mode = '<font color=red>未设置</font>';
			$name = '<font color=red>未设置</font>';
			if($value['auto_person']==3){ 
				$mode = '办理人员';
				$name = $value['range_user_text'];
			}
			if($value['auto_person']==4){ //
				$mode = '办理人员';
				$name = $value['auto_sponsor_text'];
			}
			if($value['auto_person']==5){ //
				$mode = '办理角色';
				$name = $value['auto_role_text'];
			}
			if($value['auto_person']==6){ //
				$work = ['1'=>'制单人员','2'=>'制单人员领导'];
				$mode = '<font color=blue>事务处理</font>';
				$name = $work[$value['work_ids']];
			}
			if($value['process_type']=='is_one'){ //
				$name_att = '<font color=blue>[开始]</font>';
				
			}else{
				if($value['wf_mode']==0){ //
					$name_att = '[直线]';
				}elseif($value['wf_mode']==1){
					$name_att = '<font color=green>[转出]</font>';	
				}else{
					$name_att = '<font color=red>[同步]</font>';	
				}
			}
			
			
            $process_data[] = [
                'id' => $value['id'],
				'mode' => $mode,
				'name' => $name,
                'flow_id' => $value['flow_id'],
                'process_name' => $name_att.$value['process_name'],
                'process_to' => $value['process_to'],
                'style' => 'width:' . $style['width'] . 'px;height:' . $style['height'] . 'px;line-height:30px;color:#0e76a8;left:' . $value['setleft'] . 'px;top:' . $value['settop'] . 'px;',
            ];
        }
        return json_encode(['total' => $process_total, 'list' => $process_data]);
    }
	/**
     * 删除步骤信息
     * @param $flow_id 
	 * @param $process_id 
     */
    public static function ProcessDel($flow_id, $process_id)
    {
        if ($process_id <= 0 or $flow_id <= 0) {
            return ['status' => 0, 'msg' => '操作不正确'];
        }
        $map = ['id' => $process_id, 'flow_id' => $flow_id, 'is_del' => 0];
        $process_model = Db::name('flow_process');
        $process_model->startTrans();
        $trans = $process_model->where($map)->delete();
        if (!$trans) {
            $process_model->rollback();
            return ['status' => 0, 'msg' => '删除失败', 'info' => ''];
        }
        $list = Db::name('flow_process')->field('id,process_to')->where('flow_id', $flow_id)->where('is_del', 0)->where('', 'exp', "FIND_IN_SET(" . $process_id . ",process_to)")->select();
        if (is_array($list)) {
            foreach ($list as $value) {
                $arr = explode(',', $value['process_to']);
                $k = array_search($process_id, $arr);
                unset($arr[$k]);
                $process_to = '';
                if (!empty($arr)) {
                    $process_to = implode(',', $arr);
                }
                $data = ['process_to' => $process_to, 'updatetime' => time()];
                $trans = Db::name('flow_process')->where('id', $value['id'])->update($data);
                if (!$trans) {//有错误，跳出
                    break;
                }
            }
        }
        if (!$trans) {
            $process_model->rollback();
            return ['status' => 0, 'msg' => '删除失败，请重试', 'info' => ''];
        }
        $process_model->commit();
        return ['status' => 1, 'msg' => '删除成功', 'info' => ''];
    }
	/**
     * 删除步骤信息
     * @param $flow_id 
     */
    public static function ProcessDelAll($flow_id)
    {
        $res = Db::name('flow_process')->where('flow_id', $flow_id)->delete();
        if ($res) {
            return ['status' => 1, 'data' => $res, 'msg' => '操作成功！'];
        } else {
            return ['status' => 0, 'msg' => '操作错误！'];
        }
    }
	/**
     * 新增步骤信息
     * @param $flow_id 
     */
    public static function ProcessAdd($flow_id)
    {
        $process_count = Db::name('flow_process')->where('flow_id', $flow_id)->count();
        $process_type = 'is_step';
        if ($process_count <= 0){
            $process_type = 'is_one';
			$process_setleft = '100';
			$process_settop = '100';			
		}else{
			//新建步骤显示在上一个步骤下方 2019年1月28日14:32:45
			$style = Db::name('flow_process')->order('id desc')->where('flow_id',$flow_id)->limit(1)->find();
			$process_type = 'is_step';
			$process_setleft = $style['setleft']+30;
			$process_settop = $style['settop']+30;
		}
        $data = [
            'flow_id' => $flow_id,'setleft' => $process_setleft,'settop' => $process_settop,
            'process_type' => $process_type, 'style' => json_encode(['width' => '120', 'height' => 'auto', 'color' => '#0e76a8'])
        ];
        $processid = Db::name('flow_process')->insertGetId($data);
        if ($processid <= 0) {
            return ['status' => 0, 'msg' => '添加失败！', 'info' => ''];
        } else {
            return ['status' => 1, 'msg' => '添加成功！', 'info' => ''];
        }
    }
	/**
     * 步骤连接
     * @param $flow_id 
	 * @param $process_info 
     */
    public static function ProcessLink($flow_id, $process_info)
    {
        $one = self::GetFlow($flow_id);;
        if (!$one) {
            return ['status' => 0, 'msg' => '未找到流程数据', 'info' => ''];
        }
        $process_info = json_decode(htmlspecialchars_decode(trim($process_info)), true);
        if ($flow_id <= 0 or !$process_info) {
            return ['status' => 0, 'msg' => '参数有误，请重试', 'info' => ''];
        }
        foreach ($process_info as $process_id => $value) {
            $datas = [
                'setleft' => (int)$value['left'],
                'settop' => (int)$value['top'],
                'process_to' => self::ids_parse($value['process_to']),
                'updatetime' => time()
            ];
            $ret = Db::name('flow_process')->where('id', 'eq', $process_id)->where('flow_id', 'eq', $flow_id)->update($datas);
        }
        return ['status' => 1, 'msg' => '保存成功！', 'info' => ''];
    }
	/**
     * 属性保存
     * @param $process_id 
	 * @param $datas 
     */
    public static function ProcessAttSave($process_id, $datas)
    {
        $process_condition = trim($datas['process_condition'], ',');//process_to
        $process_condition = explode(',', $process_condition);
        $out_condition = array();
		if(count($process_condition)>1 and  $datas['wf_mode']==1){
			foreach ($process_condition as $value) {
				$value = intval($value);
				if ($value > 0) {
					$condition = trim($datas['process_in_set_' . $value], "@wf@");
					 if ($condition=='') {
						return ['code' => 1, 'msg' => '转出条件必须设置！!', 'info' => ''];
					}
					$condition = $condition ? explode("@wf@", $condition) : array();
					$out_condition[$value] = ['condition' => $condition];
				}
			}
		}

        $data = [
            'process_name' => $datas['process_name'],
            'process_type' => $datas['process_type'],
            'auto_person' => $datas['auto_person'],
			'wf_mode' => $datas['wf_mode'],
			'wf_action' => $datas['wf_action'],
            'auto_sponsor_ids' => $datas['auto_sponsor_ids'],
            'auto_sponsor_text' => $datas['auto_sponsor_text'],
            'auto_role_ids' => $datas['auto_role_ids'],
            'auto_role_text' => $datas['auto_role_text'],
            'range_user_ids' => $datas['range_user_ids'],
            'range_user_text' => $datas['range_user_text'],
			 'work_text' => $datas['work_text'],//新增事务功能
            'work_ids' => $datas['work_ids'],  //新增事务功能
			 'work_msg' => $datas['work_msg'],  //新增事务MSG
			  'work_sql' => $datas['work_sql'],  //新增事务SQL
            'is_sing' => $datas['is_sing'],
            'is_back' => $datas['is_back'],
            'out_condition' => json_encode($out_condition),
            'style' => json_encode(['width' => $datas['style_width'], 'height' => $datas['style_height'], 'color' => '#0e76a8'])
        ];
        //在没有下一步骤的时候保存属性
        if (isset($datas["process_to"])) {
            $data['process_to'] = self::ids_parse($datas['process_to']);
        }
        $ret = Db::name('flow_process')->where('id', $process_id)->setField($data);
        if ($ret!==false) {
            return ['code' => 0, 'msg' => '保存成功！', 'info' => ''];
        } else {
            return ['code' => 1, 'msg' => '保存失败！', 'info' => ''];
        }

    }
	/**
     * 属性查看
	 * @param $process_id
     */
    public static function ProcessAttView($process_id)
    {
        //连接数据表用的。表 model 
        $flow_model =  Db::name('flow');
        $process_model =  Db::name('flow_process');
        $one = self::getflowprocess($process_id);
        if (!$one) {
            return ['status' => 0, 'msg' => '未找到步骤信息!', 'info' => ''];
        }
        $flow_one = self::GetFlow($one['flow_id']);
        if (!$flow_one) {
            return ['status' => 0, 'msg' => '未找到流程信息!', 'info' => ''];
        }
		$one['process_tos'] = $one['process_to'];
        $one['process_to'] = $one['process_to'] == '' ? array() : explode(',', $one['process_to']);
        $one['style'] = json_decode($one['style'], true);
        $one['out_condition'] = self::parse_out_condition($one['out_condition'], '');//json
        $process_to_list =  Db::name('flow_process')->field('id,process_name,process_type')->where('id','in' ,$one['process_tos'])->where('is_del', 0)->select();
		foreach($process_to_list as $k=>$v){
			if((count((array)$one['out_condition'])>1)){
				//修复设计完成后，新增转出条件报错问题
				if(isset($one['out_condition'][$v['id']])){
					$process_to_list[$k]['condition'] = $one['out_condition'][$v['id']]['condition'];
				}else{
					$process_to_list[$k]['condition'] = '';
				}
			}else{
				$process_to_list[$k]['condition'] = '';
			}
		}
        $child_flow_list =  Db::name('flow')->field('id,flow_name')->where('is_del', 0)->select();
        return ['show' => 'basic', 'info' => $one, 'process_to_list' => $process_to_list, 'child_flow_list' => $child_flow_list, 'from' => self::get_db_column_comment($flow_one['type'])];
    }
	/**
     * 步骤逻辑检查
     * @param $wfid 
     */
	public  static function CheckFlow($wfid)
	{
		$flow = Db::name('flow')->find($wfid);
		if (!$wfid) {
            return ['status' => 0, 'msg' => '参数出错!', 'info' => ''];
        }
		$pinfo =  Db::name('flow_process')->where('flow_id',$wfid)->select();
		if (count($pinfo)<1) {
            return ['status' => 0, 'msg' => '没有找到步骤信息!', 'info' => ''];
        }
		$one_pinfo =Db::name('flow_process')->where('flow_id',$wfid)->where('process_type','is_one')->count();
		if ($one_pinfo<1) {
            return ['status' => 0, 'msg' => '没有设置第一步骤,请修改!', 'info' => ''];
        }
		if ($one_pinfo>1) {
            return ['status' => 0, 'msg' => '有两个起始步骤，请注意哦！', 'info' => ''];
        }
		return ['status' => 1, 'msg' => '简单逻辑检查通过，请自行检查转出条件！', 'info' => ''];
	}
	
	/**
	 *结束工作流主状态
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public static function end_flow($run_id)
	{
		return Db::name('run')->where('id','eq',$run_id)->update(['status'=>1,'endtime'=>time()]);
	}
	/**
	 *结束工作流步骤信息
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public static function end_process($run_process,$check_con)
	{
		return Db::name('run_process')->where('id','in',$run_process)->update(['status'=>2,'remark'=>$check_con,'bl_time'=>time()]);
	}
	/**
	 *更新流程主信息
	 *
	 *@param $run_flow_process 工作流ID
	 **/
	public static function up($run_id,$flow_process)
	{
		return Db::name('run')->where('id','eq',$run_id)->update(['run_flow_process'=>$flow_process]);	
	}
    /**
     * JSON 转换处理
     * @param $flow_id 
	 * @param $process_info 
     */
    public static function parse_out_condition($json_data, $field_data)
    {
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
                $val_text = '';
                foreach ($temparr[0] as $k => $v) {
                    $field_name = self::get_field_name($temparr[1][$k], $field_data);
                    if ($field_name)
                        $val_text = str_replace($v, "'" . $field_name . "'", $val);
                    else
                        $val_text = $val;
                }
                $condition .= '<option value="' . $val . '">' . $val . '</option>';
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
            if ($value['leipiplugins'] == 'checkboxs' && $value['parse_name'] == $field) {
                $title = $value['title'];
                break;
            } else if ($value['name'] == $field) {
                $title = $value['title'];
                break;
            }
        }
        return $title;
    }
	/**
     * IDS数组转换
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
     * 获取表字段信息
     *
     */
    public static function get_db_column_comment($table_name = '', $field = true, $table_schema = '')
    {
        $database = config('database.');
        $table_schema = empty($table_schema) ? $database['database'] : $table_schema;
        $table_name = $database['prefix'] . $table_name;
        $fieldName = $field === true ? 'allField' : $field;
        $cacheKeyName = 'db_' . $table_schema . '_' . $table_name . '_' . $fieldName;
        $param = [
            $table_name,
            $table_schema
        ];
        $columeName = '';
        if ($field !== true) {
            $param[] = $field;
            $columeName = "AND COLUMN_NAME = ?";
        }
        $res = Db::query("SELECT COLUMN_NAME as field,column_comment as comment FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND table_schema = ? $columeName", $param);
        $result = array();
        foreach ($res as $k => $value) {
            foreach ($value as $key => $v) {
                if ($value['comment'] != '') {
                    $result[$value['field']] = $value['comment'];
                }
            }
        }
        return count($result) == 1 ? reset($result) : $result;
    }
	

}