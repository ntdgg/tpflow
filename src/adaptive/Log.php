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

class Log
{
	
	protected $mode;
	
	public function __construct()
	{
		if (unit::gconfig('wf_db_mode') == 1) {
			$className = '\\tpflow\\custom\\think\\AdapteeLog';
		} else {
			$className = unit::gconfig('wf_db_namespace') . 'AdapteeLog';
		}
		$this->mode = new $className();
	}
	
	static function FlowLog($wf_fid, $wf_type, $type = 'Html')
	{
		$info = self::RunLog($wf_fid, $wf_type);
		if ($type == "Html") {
			$html = '
					 <style type="text/css">
						.new_table{border-collapse: collapse;margin: 0 auto;text-align: center;width: 100%;}
						.new_table td, table th{border: 1px solid #cad9ea;color: #666;height: 30px;}
						.new_table thead th{background-color: #CCE8EB;width: 100px;}
						.new_table tr:nth-child(odd){background: #fff;}
						.new_table tr:nth-child(even){background: #F5FAFA;}
					</style>
					<table class="new_table" style="margin-top:5px"><tr><tr><td>审批人</td><td>审批意见</td><td>审批操作</td><td>审批时间</td></tr></tr>';
			foreach ($info as $k => $v) {
				$down = '';
				if ($v['art'] <> '') {
					$down = '附件：<a class="btn btn-success" href="/uploads/' . $v['art'] . '" target="download">下载</a>';
				}
                $img = '';
                if($v['signature']!=''){
                    $img = '<img width="100px"  src="'.$v['signature'].'" alt="签章">';
                }
				$html .= '<tr><td>' . $v['user'] . $img .'</td><td>' . $v['content'] . $down . '</td><td>' . $v['btn'] . '</td><td>' . date('m-d H:i', $v['dateline']) . '</td></tr>';
			}
			$html .= '</table>';
			return $html;
		}
		if ($type == "Json") {
			return json_encode($info);
		}
	}
	
	/**
	 * 流程日志
	 *
	 * @param $wf_fid
	 * @param $wf_type
	 */
	static function RunLog($wf_fid, $wf_type)
	{
		$type = ['Send' => '流程发起', 'ok' => '同意提交', 'Back' => '退回修改', 'SupEnd' => '终止流程', 'Sing' => '会签提交', 'sok' => '会签同意', 'SingBack' => '会签退回', 'SingSing' => '会签再会签','CC' => '签阅','endflow' => '终止流程','cancelflow' => '去除审批'];
		$run_log = (new Log())->mode->SearchRunLog($wf_fid, $wf_type);
		foreach ($run_log as $k => $v) {
			$run_log[$k]['btn'] = $type[$v['btn']] ?? '按钮错误';
            if($v['uid']==0){
                $run_log[$k]['user'] = '系统';
            }else{
                $run_log[$k]['user'] = User::GetUserName($v['uid']);
            }

		}
		return $run_log;
	}
	
	/**
	 * 工作流审批日志记录
	 *
	 * @param mixed $uid 实例id
	 * @param mixed $run_id 运行的工作流id
	 * @param string $content 审批意见
	 * @param mixed $from_id 单据id
	 * @param string $from_table 单据表
	 * @param string $btn 操作按钮 ok 提交 back 回退 sing 会签  Send 发起
	 **/
	static function AddrunLog($uid, $run_id, $config, $btn)
	{
		$work_return = '';
		if ($btn <> 'Send' && $btn <> 'SupEnd' && $btn <> 'endflow'&& $btn <> 'cancelflow') {
			$work_return = Work::WorkApi($config);//在日志记录前加载节点钩子
		}
		if (!isset($config['art'])) {
			$config['art'] = '';
		}
		//用户审批完成后的校验
        if ($btn <> 'cancelflow') {
            if (is_object(unit::LoadClass($config['wf_type'], $config['wf_fid']))) {
                $BillWork = (unit::LoadClass($config['wf_type'], $config['wf_fid'], $run_id))->after($btn);
                if ($BillWork['code'] == -1) {
                    return $BillWork;
                }
            }
        }
		$run_log_data = array(
			'uid' => $uid,
			'from_id' => $config['wf_fid'],
			'from_table' => $config['wf_type'],
			'run_id' => $run_id,
			'content' => $config['check_con'],
			'work_info' => $work_return,
			'art' => $config['art'],
			'btn' => $btn,
            'signature'=>$config['signature'] ?? '',
			'dateline' => time()
		);
		return (new Log())->mode->AddrunLog($run_log_data);
	}
    /**
     * 工作流审批日志记录
     *
     * @param Array $from 核心数组[wf_fid,wf_type,run_id]
     * @param string $con 审批意见
     * @param mixed $file 单据id
     **/
    static function AddLog($from,$con,$file='')
    {
        $run_log_data = array(
            'uid' => unit::getuserinfo('uid'),
            'from_id' => $from['wf_fid'],
            'from_table' => $from['wf_type'],
            'run_id' => $from['run_id'],
            'content' => $con,
            'work_info' => '',
            'art' => $file,
            'btn' => 'CC',
            'dateline' => time()
        );
        return (new Log())->mode->AddrunLog($run_log_data);
    }
}