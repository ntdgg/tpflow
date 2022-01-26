<?php

declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeKpi
{
    function addKpi($data){

        Db::startTrans();
        try {
            Db::name('wf_kpi_data')->insertGetId($data);
            if($this->hasKpiMonth($data['k_uid'])){
                $this->incKpiMonth($data['k_uid'],$data['k_mark']);
                }else{
                $this->addKpiMonth($data);
            }
            if($this->hasKpiYear($data['k_uid'])){
                $this->incKpiYear($data['k_uid'],$data['k_mark']);
            }else{
                $this->addKpiYear($data);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
    }

    function hasKpiYear($uid){
        $has = Db::name('wf_kpi_year')->where('k_uid',$uid)->where('k_year',date('Y'))->find();
        if($has){
            return true;
        }else{
            return false;
        }
    }

    function hasKpiMonth($uid){
        $has = Db::name('wf_kpi_month')->where('k_uid',$uid)->where('k_year',date('Y'))->where('k_month',date('m'))->find();
        if($has){
            return true;
        }else{
            return false;
        }
    }

    function addKpiYear($data){
        $post = [
            'k_uid'=>$data['k_uid'],
            'k_role'=>$data['k_role'],
            'k_mark'=>$data['k_mark'],
            'k_year'=>$data['k_year'],
            'k_time'=>1,
            'k_create_time'=>time(),
        ];
        Db::name('wf_kpi_year')->insertGetId($post);
    }

    function addKpiMonth($data){
        $post = [
            'k_uid'=>$data['k_uid'],
            'k_role'=>$data['k_role'],
            'k_mark'=>$data['k_mark'],
            'k_year'=>$data['k_year'],
            'k_month'=>$data['k_month'],
            'k_time'=>1,
            'k_create_time'=>time(),
        ];
        Db::name('wf_kpi_month')->insertGetId($post);
    }

    function incKpiYear($uid,$mark){
        Db::name('wf_kpi_year')->where('k_uid',$uid)->where('k_year',date('Y'))->inc('k_mark', $mark)->inc('k_time')->update();
    }

    function incKpiMonth($uid,$mark){
        Db::name('wf_kpi_month')->where('k_uid',$uid)->where('k_year',date('Y'))->where('k_month',date('m'))->inc('k_mark', $mark)->inc('k_time')->update();
    }




}