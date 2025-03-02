<?php
/**
 *+------------------
 * Tpflow AiFlow AI审批系统
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
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
use tpflow\lib\DoubaoAPI;

use tpflow\lib\unit;

class AiFlow
{
    /**
     * AI审批专家自动执行
     *
     * @param mixed $npid 运行步骤id
     * @param mixed $run_wfid 设计器id
     */
    public function doAuto($pid, $run_id,$run_process_id)
    {
        /*
         * 1、读取到运行步骤的id
         * 2、如果运行步骤是Ai步骤；
         * 3、调用AI接口返回数据；
         * 4、自动执行步骤！
         * */
        $wfrun = Run::FindRunId($run_id);//运行步骤信息
        $wf_process = Process::GetProcessInfo($pid,$run_id);//fix one process err
        //判断是否设置了步骤自动模式
        if($wf_process['auto_person']==8){
            return $this->autoRun($run_process_id,$wfrun,$wf_process);
        }
        return ['msg' => '没有Ai步骤信息', 'code' => 0];
    }
    public function autoRun($run_process_id,$wfrun,$wf_process){
        $uid = unit::getuserinfo('uid');
        $userinfo = ['uid' => unit::getuserinfo('uid'), 'role' => unit::getuserinfo('role')];
        $info = Info::workflowInfo($wfrun['from_id'], $wfrun['from_table'], $userinfo);//查找查找步骤信息
        $npid = $info['nexid'];
        $run_id = $info['run_id'];


        $bill = Bill::getbill($wfrun['from_table'], $wfrun['from_id']);
        //拼接法律问题
        $law_question = '';
        $fields = explode(',', $wf_process['form_set_hide']);
        $fields_text = explode(',', $wf_process['form_set_hide_text']);
        foreach ($fields as $k => $v) {
            $law_question.= $fields_text[$k]. ':'. ($bill[$v] ?? '-').'';
        }
        //调用AI服务
        $wf_ai_type = unit::gconfig('wf_ai_type');
        $config = unit::gconfig('wf_ai');
        //豆包服务
        if($wf_ai_type=='doubao'){
            $doubaoModelAPI = new DoubaoAPI($config[$wf_ai_type]['url'], $config[$wf_ai_type]['key']);
            $res =  $doubaoModelAPI->callModelAPI($config[$wf_ai_type]['model'], $law_question);
        }
        //千帆服务
        if($wf_ai_type=='qianfan'){
            $doubaoModelAPI = new QianFan($config[$wf_ai_type]['url'], $config[$wf_ai_type]['key']);
            $res =  $doubaoModelAPI->callModelAPI($config[$wf_ai_type]['model'], $law_question);
        }

        $check_con = '';
        if($res['code']==0){
            $result = [];
            foreach ($res['msg'] as $key => $value) {
                $result[] = "$key:$value";
            }
            $check_con = implode(' ', $result);
        }else{
            $check_con = $res['msg'];
        }
        $log = ['npid'=>$info['nexid'],'run_id'=>$info['run_id'],'flow_id'=>$wfrun['flow_id'],'wf_type'=>$wfrun['from_table'],'wf_fid'=>$wfrun['from_id'],'check_con'=>$check_con,'art'=>'','run_process'=>$info['run_process']];
        if ($npid != '') {//判断是否为最后
                /*加入判断是否是终止步骤*/
                $EndFlow = EndFlow::doTask($npid,$run_id);
                if($EndFlow==1){
                    Flow::end_flow($run_id);//终止步骤
                    Bill::updatebill($wfrun['from_table'], $wfrun['from_id'], 2);
                    return ['msg' => '审批完成，流程结束!', 'code' => '0'];
                }
                //更新单据信息
                Flow::up($run_id, $npid);
                $end = Flow::end_process($run_process_id, $check_con,1);
                //记录下一个流程->消息记录
                $this->Run($log, $uid);
        }else{
            //结束该流程
            $end = Flow::end_flow($run_id);
            /*自动执行该步骤*/
            $end = Flow::end_process($run_process_id, $check_con,1);
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
        $run_log = Log::AddrunLog(0, $config['run_id'], $config, 'ok');
        if (!$run_log) {
            return ['msg' => '消息记录失败，数据库错误！！！', 'code' => '-1'];
        }
    }
}
