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

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Flow
{
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('wf_db_mode')==1){
			$className = '\\tpflow\\custom\\think\\AdapteeFlow';
		}else{
			$className = unit::gconfig('wf_db_namespace').'AdapteeFlow';
		}
		$this->mode = new $className();
    }
	 /**
     * 获取流程信息
     *
     * @param $fid
     */
   static function find($id)
    {
        return (new Flow())->mode->find($id);
    }
    /**
     * 获取类别工作流
     *
     * @param $wf_type
     */
   static function getWorkflowByType($wf_type)
    {
		$workflow = [];
        if ($wf_type == '') {
            return $workflow;
        }
		$map[] = ['is_del','=',0];
		$map[] = ['status','=',0];
		$map[] = ['type','=',$wf_type];
       return (new Flow())->mode->SearchFlow($map);
    }

    /**
     * 获取流程信息
     *
     * @param $fid
     */
   static function GetFlowInfo($fid)
    {
		if ($fid == '') {
            return false;
        }
        $info = (new Flow())->mode->find($fid);
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
   static function getWorkflow($wf_id)
    {
		if ($wf_id == '') {
            return false;
        }
        $info =(new Flow())->mode->find($wf_id);
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
   static function getflowprocess($id)
    {
       return Process::find($id);
    }

    /**
     * API获取工作流列表
     * API接口调用
     */
   static function GetFlow($map=[],$page=1,$rows=10,$order='id desc')
    {
		$data = (new Flow())->mode->ListFlow($map,$page,$rows,$order);
		foreach($data['rows'] as $k=>$v){
			$run = Run::FindRun([['flow_id','=',$v['id']],['status','=',0]]);
			$data['rows'][$k]['edit'] =$run['id'];
		}
        return $data['rows'];
    }

    /**
     * API 新增工作流
     * @param array $data POST提交的数据
     */
   static function AddFlow($data)
    {
		 $id = (new Flow())->mode->AddFlow($data);
        if ($id) {
            return ['code' => 0, 'data' => $id];
        } else {
            return ['code' => 1, 'data' => 'Db0001-写入数据库出错！'];
        }
    }

    /**
     * API 编辑工作流
     * @param array $data POST提交的数据
     */
   static function EditFlow($data)
    {
		$id =(new Flow())->mode->EditFlow($data);
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
   static function ProcessAll($flow_id)
    {
		$map[] = ['flow_id','=',$flow_id];
		$list = Process::SearchFlowProcess($map);
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
   static function ProcessDel($flow_id, $process_id)
    {
		if ($process_id <= 0 or $flow_id <= 0) {
			return ['status' => 0, 'msg' => '操作不正确'];
        }
		$map2[] = ['flow_id','=',$flow_id];
		$map2[] = ['is_del','=',0];
		$map2[] = ['process_to','find in set',$process_id];
		$list =Process::SearchFlowProcess($map2,'id,process_to');
        $trans =  Process::DelFlowProcess([['id','=',$process_id],['flow_id','=', $flow_id],['is_del','=' ,0]]);
        if (!$trans) {
            return ['status' => 0, 'msg' => '删除失败', 'info' => ''];
        }
        if (is_array($list)) {
            foreach ($list as $value) {
                $arr = explode(',', $value['process_to']);
                $k = array_search($process_id, $arr);
                unset($arr[$k]);
                $process_to = '';
                if (!empty($arr)) {
                    $process_to = implode(',', $arr);
                }
                $data = ['process_to' => $process_to, 'uptime' => time()];
				$trans = Process::EditFlowProcess([['id','=',$value['id']]],$data);
                if (!$trans) {//有错误，跳出
                    break;
                }
            }
        }
        if (!$trans) {
            return ['status' => 0, 'msg' => '删除失败，请重试', 'info' => ''];
        }
        return ['status' => 1, 'msg' => '删除成功', 'info' => ''];
    }
	/**
     * 删除步骤信息
     * @param $flow_id 
     */
   static function ProcessDelAll($flow_id)
    {
		$res = Process::DelFlowProcess([['flow_id','=',$flow_id]]);
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
   static function ProcessAdd($flow_id)
    {
		$process_count =  Process::SearchFlowProcess([['flow_id','=',$flow_id]]);
        if (count($process_count) <= 0){
            $process_type = 'is_one';
			$process_setleft = '100';
			$process_settop = '100';			
		}else{
			//新建步骤显示在上一个步骤下方 2019年1月28日14:32:45
			$style = Process::SearchFlowProcess([['flow_id','=',$flow_id]],'*','id desc',1);
			$process_type = 'is_step';
			$process_setleft = $style[0]['setleft']+30;
			$process_settop = $style[0]['settop']+30;
		}
        $data = [
            'flow_id' => $flow_id,'setleft' => $process_setleft,'settop' => $process_settop,
            'process_type' => $process_type, 'style' => json_encode(['width' => '120', 'height' => 'auto', 'color' => '#0e76a8'])
        ];
        $processid = Process::AddFlowProcess($data);
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
   static function ProcessLink($flow_id, $process_info)
    {
		$one = (new Flow())->mode->find($flow_id);
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
                'process_to' => unit::ids_parse($value['process_to']),
                'uptime' => time()
            ];
			Process::EditFlowProcess([['id','=',$process_id],['flow_id','=',$flow_id]],$datas);
        }
        return ['status' => 1, 'msg' => '保存步骤成功~', 'info' => ''];
    }
	/**
     * 属性保存
	 * @param $datas 
     */
   static function ProcessAttSave($datas)
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
			'work_text' => $datas['work_text'],//新增事务功能  style_height
            'work_ids' => $datas['work_ids'],  //新增事务功能
			'work_msg' => $datas['work_msg'],  //新增事务MSG
			'work_sql' => $datas['work_sql'],  //新增事务SQL
            'is_sing' => $datas['is_sing'],
            'is_back' => $datas['is_back'],
            'out_condition' => json_encode($out_condition),
            'style' => json_encode(['width' => $datas['style_width'], 'height' => 'auto', 'color' => '#0e76a8'])
        ];
        if (isset($datas["process_to"])) {
            $data['process_to'] = unit::ids_parse($datas['process_to']);
        }
		$ret = Process::EditFlowProcess([['id','=',$datas['process_id']]],$data);
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
   static function ProcessAttView($process_id)
    {
		$mode = (new Flow())->mode;
		$one = Process::find($process_id);
        if (!$one) {
            return ['status' => 0, 'msg' => '未找到步骤信息!', 'info' => ''];
        }
        $flow_one = $mode->find($one['flow_id']);
        if (!$flow_one) {
            return ['status' => 0, 'msg' => '未找到流程信息!', 'info' => ''];
        }
		$one['process_tos'] = $one['process_to'];
        $one['process_to'] = $one['process_to'] == '' ? array() : explode(',', $one['process_to']);
        $one['style'] = json_decode($one['style'], true);
        $one['out_condition'] = unit::parse_out_condition($one['out_condition'], '');//json
		$process_to_list = Process::SearchFlowProcess([['id','in',$one['process_tos']],['is_del','=',0]],'id,process_name,process_type');

		foreach($process_to_list as $k=>$v){
			if((count($one['out_condition'])>1)){
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
		$child_flow_list = $mode->SearchFlow([['is_del','=',0]],'id,flow_name');
        return ['show' => 'basic', 'info' => $one, 'process_to_list' => $process_to_list, 'child_flow_list' => $child_flow_list, 'from' => $mode->get_db_column_comment($flow_one['type'])];
    }
	/**
     * 步骤逻辑检查
     * @param $wfid 
     */
	public  static function CheckFlow($wfid)
	{
		if (!$wfid) {
            return ['status' => 0, 'msg' => '参数出错!', 'info' => ''];
        }
		$pinfo =  Process::SearchFlowProcess([['flow_id','=',$wfid]]);
		if (count($pinfo)<1) {
            return ['status' => 0, 'msg' => '没有找到步骤信息!', 'info' => ''];
        }
		$one_pinfo =Process::SearchFlowProcess([['flow_id','=',$wfid],['process_type','=','is_one']]);
		if (count($one_pinfo)<1) {
            return ['status' => 0, 'msg' => '没有设置第一步骤,请修改!', 'info' => ''];
        }
		if (count($one_pinfo)>1) {
            return ['status' => 0, 'msg' => '有两个起始步骤，请注意哦！', 'info' => ''];
        }
		return ['status' => 1, 'msg' => '简单逻辑检查通过，请自行检查转出条件！', 'info' => ''];
	}
	
	/**
	 *结束工作流主状态
	 *
	 **/
	public static function end_flow($run_id)
	{
		return Run::EditRun($run_id,['status'=>1,'endtime'=>time()]);
	}
	/**
	 *结束工作流步骤信息
	 *
	 **/
	public static function end_process($run_process,$check_con)
	{
		return Run::EditRunProcess([['id','in',$run_process]],['status'=>2,'remark'=>$check_con,'bl_time'=>time()]);
	}
	/**
	 *更新流程主信息
	 *
	 **/
	public static function up($run_id,$flow_process)
	{
		return Run::EditRun($run_id,['run_flow_process'=>$flow_process]);
	}
}