<?php
/**
 *+------------------
 * Tpflow 流信息处理
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Flow
{
	protected $mode;
	
	public function __construct()
	{
		if (unit::gconfig('wf_db_mode') == 1) {
			$className = '\\tpflow\\custom\\think\\AdapteeFlow';
		} else {
			$className = unit::gconfig('wf_db_namespace') . 'AdapteeFlow';
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
     * 删除流程
     *
     * @param $fid
     */
    static function del($id)
    {
        return (new Flow())->mode->del($id);
    }
	
	/**
	 * 获取类别工作流
	 *
	 * @param $wf_type
	 */
	static function getWorkflowByType($wf_type)
	{
		if ($wf_type == '') {
			return [];
		}
		$map[] = ['is_del', '=', 0];
		$map[] = ['status', '=', 0];
		$map[] = ['type', '=', $wf_type];
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
			return $info;
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
		if(empty($wf_id) || $wf_id<=0){
			return false;
		}
		$info = (new Flow())->mode->find($wf_id);
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
	static function GetFlow($map = [], $page = 1, $rows = 10, $order = 'id desc')
	{
		$data = (new Flow())->mode->ListFlow($map, $page, $rows, $order);
		foreach ($data['rows'] as $k => $v) {
			$run = Run::FindRun([['flow_id', '=', $v['id']], ['status', '=', 0]]);
			$data['rows'][$k]['edit'] = $run['id'] ?? '';
		}
		return $data;
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
		$id = (new Flow())->mode->EditFlow($data);
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
		$map[] = ['flow_id', '=', $flow_id];
		$list = Process::SearchFlowProcess($map);
        $x6 = [];
        $x62 = [];
		foreach ($list as $value) {
			$style = json_decode($value['style'], true);
            /*模式转换*/
            $process_type = $value['process_type'] ?? 'node-flow';
            /*网关模式*/
            if(count(explode(",",$value['process_to']))>1  && $process_type !='node-start'){
                $process_type ='node-gateway';
            }
            $x6[] = [
                'position'=>['x'=>(int)$value['settop'],'y'=>(int)$value['setleft']],
                'size'=>['width'=>(int)$style['width'],'height'=>(int)$style['height']],
                'attrs'=>['text'=>['text'=>$value['process_name']]],
                'shape'=>$process_type,
                'id'=>'Tpflow-'.$value['id'],
                'data'=>$value['id'],
            ];
            if($value['process_to']!=''){
                $process_to = explode(",",$value['process_to']);
                foreach ($process_to as $kk=>$vv){
                    $x62[] = [
                        'shape'=>'link_node',
                        'router'=>['name'=>'manhattan'],
                        'source'=>['cell'=>'Tpflow-'.$value['id'],'port'=>'b1'],
                        'target'=>['cell'=>'Tpflow-'.$vv,'port'=>'t1'],
                        'labels'=>[['attrs'=>['label'=>['text'=>$value['id'].'-'.$vv]]]],
                        'data'=>$value['id']
                    ];
                }
            }
		}
       $x6_data =  array_merge($x6,$x62);
		return json_encode(['x6'=>['cells'=>$x6_data]]);
	}
	
	/**
	 * 删除步骤信息
	 * @param $flow_id
	 * @param $process_id
	 */
	static function ProcessDel($flow_id, $process_id)
	{
		if ($process_id <= 0 or $flow_id <= 0) {
			return ['code' => 1, 'msg' => '操作不正确'];
		}
		$map2[] = ['flow_id', '=', $flow_id];
		$map2[] = ['is_del', '=', 0];
		$map2[] = ['process_to', 'find in set', $process_id];
		$list = Process::SearchFlowProcess($map2, 'id,process_to');
		$trans = Process::DelFlowProcess([['id', '=', $process_id], ['flow_id', '=', $flow_id], ['is_del', '=', 0]]);
		if (!$trans) {
			return ['code' => 1, 'msg' => '删除失败', 'info' => ''];
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
				$trans = Process::EditFlowProcess([['id', '=', $value['id']]], $data);
				if (!$trans) {//有错误，跳出
					break;
				}
			}
		}
		if (!$trans) {
			return ['code' => 1, 'msg' => '删除失败，请重试', 'info' => ''];
		}
		return ['code' => 0, 'msg' => '删除成功', 'info' => ''];
	}
	
	/**
	 * 删除步骤信息
	 * @param $flow_id
	 */
	static function ProcessDelAll($flow_id)
	{
		$res = Process::DelFlowProcess([['flow_id', '=', $flow_id]]);
		if ($res) {
			return ['code' => 0, 'data' => $res, 'msg' => '操作成功！'];
		} else {
			return ['code' => 1, 'msg' => '操作错误！'];
		}
	}
	
	/**
	 * 新增步骤信息
	 * @param $flow_id
	 */
	static function ProcessAdd($flow_id,$data)
	{
		$process_count = Process::SearchFlowProcess([['flow_id', '=', $flow_id],['process_type', '=','node-start']]);
		if (count($process_count) > 1) {
            return ['code' => 0, 'msg' => '对不起，只能有一个开始节点！', 'info' => ''];
		}
		$data = [
            'process_name'=>$data['process_name'],
			'flow_id' => $flow_id, 'setleft' => $data['setleft'], 'settop' => $data['settop'],
			'process_type' => $data['process_type'], 'style' => $data['style']
		];
		$processid = Process::AddFlowProcess($data);
		if ($processid <= 0) {
			return ['code' => 1, 'msg' => '添加失败！', 'info' => ''];
		} else {
			return ['code' => 0, 'msg' => '添加成功！', 'info' => ''];
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
			return ['code' => 1, 'msg' => '未找到流程数据', 'info' => ''];
		}
		$process_info = json_decode(htmlspecialchars_decode(trim($process_info)), true);
        if (!$process_info) {
            return ['code' => 0, 'msg' => '保存步骤成功~', 'info' => ''];
        }
		if ($flow_id <= 0 or !$process_info) {
			return ['code' => 1, 'msg' => '参数有误，请重试', 'info' => ''];
		}
        $new = [];
		foreach ($process_info as $process_id => $value) {
            if($value['shape']!='link_node' && $value['shape']!='edge'){
                $p_id = $value['data'];
                $process_to = self::search($value['data'],$process_info);
                $datas = [
                    'settop' => (int)$value['position']['x'],
                    'setleft' => (int)$value['position']['y'],
                    'process_to' => $process_to,
                    'uptime' => time()
                ];
				if(is_numeric($p_id)){
					Process::EditFlowProcess([['id', '=', $p_id], ['flow_id', '=', $flow_id]], $datas);
				}
            }
		}
		return ['code' => 0, 'msg' => '保存步骤成功~', 'info' => ''];
	}
	static function search($id,$data){
        $ids ='';
        $iis = [];
        foreach($data as $k=>$v){
            if(isset($v['target']['cell'])){
                if($v['shape']=='link_node' || $v['shape']=='edge'){
                    if($id==str_replace("Tpflow-","",$v['source']['cell'] ?? '')){
                        $iis[] = str_replace("Tpflow-","",$v['target']['cell']);
                    }
                }
            }
        }
        $ids = implode(',',$iis);
        return $ids;
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
		if (count($process_condition) > 1 and $datas['wf_mode'] == 1) {
			foreach ($process_condition as $value) {
				$value = intval($value);
				if ($value > 0) {
					$condition = trim($datas['process_in_set_' . $value], "@wf@");
					if ($condition == '') {
						return ['code' => 1, 'msg' => '转出条件必须设置！!', 'info' => ''];
					}
					$condition = $condition ? explode("@wf@", $condition) : array();
					$out_condition[$value] = ['condition' => $condition];
				}
			}
		}
        if(!isset($datas['wf_action_select']) || $datas['wf_action_select']==1){
            $wf_action = $datas['wf_action'];
        }else{
            $wf_action = $datas['wf_action_select'];
        }

		$data = [
			'process_name' => $datas['process_name'],
			//'process_type' => $datas['process_type'],
			'auto_person' => $datas['auto_person'],
			'wf_mode' => $datas['wf_mode'],
			'wf_action' => $wf_action,
			'auto_sponsor_ids' => $datas['auto_sponsor_ids'],
			'auto_sponsor_text' => $datas['auto_sponsor_text'],
			'auto_role_ids' => $datas['auto_role_ids'],
			'auto_role_text' => $datas['auto_role_text'],
			'range_user_ids' => $datas['range_user_ids'],
			'range_user_text' => $datas['range_user_text'],
			'work_text' => $datas['work_text'],//新增事务功能  style_height
			'work_ids' => $datas['work_ids'],  //新增事务功能
            'work_val' => $datas['work_val'],  //新增事务功能
            'work_auto' => $datas['work_auto'],  //新增事务功能
            'work_condition' => $datas['work_condition'],  //新增事务功能
			//'work_msg' => $datas['work_msg'] ?? '',  //新增事务MSG
			//'work_sql' => $datas['work_sql'] ?? '',  //新增事务SQL
			'auto_xt_text' => $datas['auto_xt_text'],  //20210526 新增协同模式
			'auto_xt_ids' => $datas['auto_xt_ids'],  //20210526 新增协同模式
			'is_sing' => $datas['is_sing'],
			'is_back' => $datas['is_back'],
			'out_condition' => json_encode($out_condition)
		];
		if (isset($datas["process_to"])) {
			$data['process_to'] = unit::ids_parse($datas['process_to']);
		}
		$ret = Process::EditFlowProcess([['id', '=', $datas['process_id']]], $data);
		if ($ret !== false) {
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
			return ['code' => 1, 'msg' => '未找到步骤信息!', 'info' => ''];
		}
		$flow_one = $mode->find($one['flow_id']);
		if (!$flow_one) {
			return ['code' => 1, 'msg' => '未找到流程信息!', 'info' => ''];
		}
		$one['process_tos'] = $one['process_to'];
		$one['process_to'] = $one['process_to'] == '' ? array() : explode(',', $one['process_to']);
		$one['style'] = json_decode($one['style'], true);
		$one['out_condition'] = unit::parse_out_condition($one['out_condition'], '');//json
		$process_to_list = Process::SearchFlowProcess([['id', 'in', $one['process_tos']], ['is_del', '=', 0], ['process_type', 'not in', ['node-msg','node-cc']]], 'id,process_name,process_type');
		foreach ($process_to_list as $k => $v) {
			if ((count($one['out_condition']) > 1)) {
				//修复设计完成后，新增转出条件报错问题
				if (isset($one['out_condition'][$v['id']])) {
					$process_to_list[$k]['condition'] = $one['out_condition'][$v['id']]['condition'];
				} else {
					$process_to_list[$k]['condition'] = '';
				}
			}else{
				$process_to_list[$k]['condition'] = '';
			}
		}
		$child_flow_list = $mode->SearchFlow([['is_del', '=', 0]], 'id,flow_name');
		return ['show' => 'basic', 'info' => $one, 'process_to_list' => $process_to_list, 'child_flow_list' => $child_flow_list, 'from' => $mode->get_db_column_comment($flow_one['type'])];
	}
	
	/**
	 * 步骤逻辑检查
	 * @param $wfid
	 */
	public static function CheckFlow($wfid)
	{
		if (!$wfid) {
			return ['code' => 1, 'msg' => '参数出错!', 'info' => ''];
		}
		$pinfo = Process::SearchFlowProcess([['flow_id', '=', $wfid]]);
		
		if (count($pinfo) < 1) {
			return ['code' => 1, 'msg' => '没有找到步骤信息!', 'info' => ''];
		}
		$one_pinfo = Process::SearchFlowProcess([['flow_id', '=', $wfid], ['process_type', '=', 'node-start']]);
		if (count($one_pinfo) < 1) {
			return ['code' => 1, 'msg' => '没有设置第一步骤,请修改!', 'info' => ''];
		}
		if (count($one_pinfo) > 1) {
			return ['code' => 1, 'msg' => '有两个起始步骤，请注意哦！', 'info' => ''];
		}
		return ['code' => 0, 'msg' => '简单逻辑检查通过，请自行检查转出条件！', 'info' => ''];
	}
	
	/**
	 *结束工作流主状态
	 *
	 **/
	public static function end_flow($run_id)
	{
		return Run::EditRun($run_id, ['status' => 1, 'endtime' => time()]);
	}
	
	/**
	 *结束工作流步骤信息
	 *
	 **/
	public static function end_process($run_process, $check_con)
	{
        //Kpi绩效数据 结束步骤写入Kpi
        Kpi::Run($run_process);
		return Run::EditRunProcess([['id', 'in', $run_process]], ['status' => 2, 'remark' => $check_con, 'bl_time' => time()]);
	}
	
	/**
	 *更新流程主信息
	 *
	 **/
	public static function up($run_id, $flow_process)
	{
		return Run::EditRun($run_id, ['run_flow_process' => $flow_process]);
	}
    public static function verUpdate(){
        $data = Process::SearchFlowProcess();
        foreach ($data as $k=>$v){
            $node = ['is_one'=>'node-start','is_step'=>'node-flow','is_end'=>'node-end'];
            $checked =  $node[$v['process_type']] ?? 1;
            if($checked !=1){
                $str = str_replace("auto",'42',$v['style']);
                Process::EditFlowProcess([['id', '=', $v['id']]],['style'=>$str,'process_type'=>$checked]);
            }
        }
        return true;
    }

}