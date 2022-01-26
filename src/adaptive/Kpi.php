<?php
/**
 *+------------------
 * Tpflow 绩效信息
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Kpi
{

    protected $mode;

    public function __construct()
    {
        if (unit::gconfig('wf_db_mode') == 1) {
            $className = '\\tpflow\\custom\\think\\AdapteeKpi';
        } else {
            $className = unit::gconfig('wf_db_namespace') . 'AdapteeKpi';
        }
        $this->mode = new $className();
    }

    /**
     * Kpi运行
     *
     */
    public static function Run($rpid)
    {
        $rpdata = Run::FindRunProcessId($rpid); //运行步骤时间
        $rrdata = Run::FindRunId($rpdata['run_id']);//运行主表
        $timediff =  intval(((time()-$rpdata['js_time'])%86400)/60);
        if(unit::gconfig('kpi_out')>$timediff){
            $isout = 0;
            $kpi_base = unit::gconfig('kpi_base');
        }else{
            $isout = 1;
            $kpi_base = 0;
        }
        $data = [
            'k_node'=>'node-flow',
            'k_uid'=>unit::getuserinfo('uid'),
            'k_role'=>unit::getuserinfo('role'),
            'k_type'=>$rrdata['from_table'],
            'k_type_id'=>$rrdata['from_id'],
            'k_mark'=>$timediff,//办理分钟
            'k_base'=>$kpi_base,
            'k_describe'=>Bill::billtablename($rrdata['from_table']),
            'k_isout'=>$isout,
            'k_year'=>date('Y'),
            'k_month'=>date('m'),
            'k_date'=>date('Y-m-d'),
            'k_create_time'=>time(),
        ];
        return (new Kpi())->mode->addKpi($data);
    }
    /**
     * Kpi运行
     *
     */
    public static function Add($table,$id)
    {
        $data = [
            'k_node'=>'node-start',
            'k_uid'=>unit::getuserinfo('uid'),
            'k_role'=>unit::getuserinfo('role'),
            'k_type'=>$table,
            'k_type_id'=>$id,
            'k_mark'=>0,//办理分钟
            'k_base'=>unit::gconfig('kpi_base'),
            'k_describe'=>Bill::billtablename($table),
            'k_isout'=>0,
            'k_year'=>date('Y'),
            'k_month'=>date('m'),
            'k_date'=>date('Y-m-d'),
            'k_create_time'=>time(),
        ];
        return (new Kpi())->mode->addKpi($data);
    }
}