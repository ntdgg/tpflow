<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Home\Model;
use Common\Model\CommonModel;


// run 的扩展缓存表
class WorkRunCacheModel extends CommonModel {

    
    //全部
    public function run_cache($run_id)
    {
        if($run_id<=0)
            return array();

        $list = S($this->name.$run_id.'_cache');
         
        if(!$list)
        {
            $map = array(
                'run_id'=>$run_id,
            );
            $temparr = $this->where($map)->select();
            
            $list = array(); 
            foreach($temparr as $key=>$value)
            {
                if($value['is_tmp']==1)
                {
                    $value['work_tmp'] = unserialize($value['work_tmp']);
                    $list['work_tmp'][$value['tmp_id']] = $value['work_tmp'];
                }else if($value['is_flow']==1)
                {
                    $value['work_flow'] =  unserialize($value['work_flow']);
                    $list['work_flow'][$value['flow_id']] = $value['work_flow'];
                }else if($value['is_step']==1)
                {
                    $value['work_flow_step'] =  unserialize($value['work_flow_step']);
                    $value['work_flow_step'] = list_to_list($value['work_flow_step'],'step');
                    $list['work_flow_step'][$value['flow_id']] = $value['work_flow_step'];
                }
                
            }
            S($this->name.$run_id.'_cache',$list,604800);
        }
        return $list; 
    }    
    //获取一个工作，模板
    //$run_id  工作id
    //$id  数据id
    public function get_run_tmp($run_id,$id)
    {
        $run_cache = self::run_cache($run_id);
        
        return $run_cache['work_tmp'][$id];
    }
    
    //获取一个工作，模板
    //$run_id  工作id
    //$id  数据id
    public function get_run_flow($run_id,$id)
    {
        $run_cache = self::run_cache($run_id);
        return $run_cache['work_flow'][$id];
    }
    //获取一个工作，模板
    //$run_id  工作id
    // $flow_id 流程 
    //$id  数据id  小于0就获取全部 步骤
    public function get_run_flow_step($run_id,$flow_id,$id=0)
    {
        $run_cache = self::run_cache($run_id);

        if($id>0)
        {
            return $run_cache['work_flow_step'][$flow_id][$id];
        }else
        {
            return $run_cache['work_flow_step'][$flow_id];
        }
    }
    //和上面一样，但传入参数是用 step
    public function get_run_flow_step2($run_id,$flow_id,$step=0)
    {
        $run_cache = self::run_cache($run_id);

        if($step>0)
        {
            $step_list = $run_cache['work_flow_step'][$flow_id];
            if($step_list)
            {
                foreach($step_list as $value)
                {
                    
                    if($value['step'] ==$step)
                    {
                        return $value;
                    }
                }
            }
            return array();
        }else
        {
            return $run_cache['work_flow_step'][$flow_id];
        }
    }
    
    
    // 获取下一步骤列表
    public function get_next_step_list($run_id,$flow_id,$step_to)
    {
        if(!$step_to) return array();
        $step_to = explode(',',$step_to);
        $temparr = array();
        sort($step_to);
        foreach($step_to as $value)
        {
            $step_one = self::get_run_flow_step($run_id,$flow_id,$value);
            if($step_one)
                $temparr[$value] = $step_one;
        }
        return $temparr;
        
    }
    
    
}
