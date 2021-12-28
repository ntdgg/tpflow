<?php
/**
 *+------------------
 * Tpflow 普通提交工作流
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\service\command;

//数据库操作
use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Log;
use tpflow\adaptive\Bill;
use tpflow\adaptive\Process;
use tpflow\adaptive\Run;
use tpflow\lib\unit;

class AutoFlow
{
    /**
     * 任务自动执行
     *
     * @param mixed $npid 运行步骤id
     * @param mixed $run_wfid 设计器id
     */
    public function doAuto($pid, $run_id,$run_process_id)
    {
        /*
         * 1、读取到运行步骤的id
         * 2、如果运行步骤是非自动执行步骤，则自动执行；
         * 3、运行步骤，先判断是否终点，如果不是终点，则继续下一步骤运行
         * 4、系统自动运行，返回新的npnid
         *
         * */
        $wfrun = Run::FindRunId($run_id);//运行步骤信息
        $wf_process = Process::GetProcessInfo($pid);
        //判断是否设置了步骤自动模式
        if($wf_process['work_val'] !='' && $wf_process['work_auto'] !='' ){
            $where = $wf_process['work_auto'].$wf_process['work_condition'].$wf_process['work_val'];
            $bill = Bill::checkbill($wfrun['from_table'], $wfrun['from_id'], $where);
            //找到符合条件的，则本步骤需要自动去执行ID
            if ($bill) {
                return $this->autoRun($run_process_id,$wfrun);
            }
            return ['msg' => '没有Auto步骤信息', 'code' => '0'];
        }
        return ['msg' => '没有Auto步骤信息', 'code' => '0'];
    }
    public function autoRun($run_process_id,$wfrun){
        $uid = unit::getuserinfo('uid');
        $userinfo = ['uid' => unit::getuserinfo('uid'), 'role' => unit::getuserinfo('role')];
        $info = Info::workflowInfo($wfrun['from_id'], $wfrun['from_table'], $userinfo);//查找查找步骤信息
        $npid = $info['nexid'];
        $run_id = $info['run_id'];
        if ($info['wf_mode'] == 2) {
            $info_list = Process::Getnorunprocess($info['run_id'], $info['run_process']);
            if (count($info_list) > 0) {
                foreach ($info_list as $k => $v) {
                    $npids[] = $v['run_flow_process'];
                }
                $npid = implode(",", $npids);
            }
        }
        $log = ['npid'=>$info['nexid'],'run_id'=>$info['run_id'],'flow_id'=>$wfrun['flow_id'],'wf_type'=>$wfrun['from_table'],'wf_fid'=>$wfrun['from_id'],'check_con'=>'系统：自动执行步骤信息！','art'=>'','run_process'=>$info['run_process']];
        if ($npid != '') {//判断是否为最后
            /*
			 * 2019年1月27日
			 * 同步模式下，只写入记录
			 */
                if ($info['wf_mode'] != 2) {
                    /*加入判断是否是终止步骤*/
                    $EndFlow = EndFlow::doTask($npid,$run_id);
                    if($EndFlow==1){
                        Flow::end_flow($run_id);//终止步骤
                        Bill::updatebill($wfrun['from_table'], $wfrun['from_id'], 2);
                        return ['msg' => '审批完成，流程结束!', 'code' => '0'];
                    }
                    //更新单据信息
                    Flow::up($run_id, $npid);
                    $end = Flow::end_process($run_process_id, '系统：自动执行步骤信息！');
                    //记录下一个流程->消息记录
                    $this->Run($log, $uid);
                } else {
                    //日志记录
                    $run_log = Log::AddrunLog($uid, $run_id, $log, 'ok');
                    if (!$run_log) {
                        return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
                    }
                }
            }else{
                //结束该流程
                $end = Flow::end_flow($run_id);
                /*自动执行该步骤*/
                $end = Flow::end_process($run_process_id, '系统：自动执行步骤信息！');
                //更新单据状态
                $bill_update = Bill::updatebill($wfrun['from_table'], $wfrun['from_id'], 2);
                if (!$bill_update) {
                    return ['msg' => '流程步骤操作记录失败，数据库错误！！！', 'code' => '-1'];
                }
                    Log::AddrunLog($uid, $run_id, $log, 'ok');
                if (!$end) {
                    return ['msg' => '结束流程错误！！！', 'code' => '-1'];
                }
        }
        return ['msg' => 'success!', 'code' => '0'];
    }
    public function Run($config, $uid, $todo='')
    {
        $nex_pid = explode(",", $config['npid'].'');
        foreach ($nex_pid as $v) {
            $wf_process = Process::GetProcessInfo($v, $config['run_id']);
            //添加流程步骤日志
            $wf_process_log = Info::addWorkflowProcess($config['flow_id'], $wf_process, $config['run_id'], $uid, $todo);
        }
        if (!$wf_process_log) {
            return ['msg' => '流程步骤操作记录失败，数据库错误！！！', 'code' => '-1'];
        }
        //日志记录
        $run_log = Log::AddrunLog($uid, $config['run_id'], $config, 'ok');
        if (!$run_log) {
            return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
        }
    }


}