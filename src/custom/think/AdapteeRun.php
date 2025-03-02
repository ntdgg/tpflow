<?php
/**
 *+------------------
 * Tpflow Run
 *+------------------
 * Copyright (c) 2006~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeRun
{
	
	
	function AddRun($data)
	{
		return Db::name('wf_run')->insertGetId($data);
	}
	
	function FindRun($where = [], $field = '*')
	{
		return Db::name('wf_run')->where($where)->field($field)->order('id desc')->find();
	}
	
	function FindRunId($id, $field = '*')
	{
		return Db::name('wf_run')->field($field)->find($id);
	}
	
	function SearchRun($where = [], $field = '*')
	{
		return Db::name('wf_run')->where($where)->field($field)->select()->toArray();
	}
	
	function EditRun($id, $data)
	{
		return Db::name('wf_run')->where('id', $id)->update($data);
	}
	
	/*run_process表操作接口代码*/
	function FindRunProcessId($id, $field = '*')
	{
		return Db::name('wf_run_process')->field($field)->find($id);
	}
	
	function FindRunProcess($where = [], $field = '*')
	{
		return Db::name('wf_run_process')->where($where)->field($field)->find();
	}
	
	function AddRunProcess($data)
	{
		return Db::name('wf_run_process')->insertGetId($data);
	}
	
	function SearchRunProcess($where = [], $field = '*')
	{
		return Db::name('wf_run_process')->where($where)->field($field)->select()->toArray();
	}
	
	function EditRunProcess($where = [], $data = [])
	{
		return Db::name('wf_run_process')->where($where)->update($data);
	}
	
	/*FindRunSign表操作接口代码*/
	function FindRunSign($where = [], $field = '*')
	{
		return Db::name('wf_run_sign')->where($where)->field($field)->find();
	}
	
	function AddRunSing($data)
	{
		return Db::name('wf_run_sign')->insertGetId($data);
	}
	/*增加协同的uids*/
	function EndRunSing($sing_sign, $check_con,$xt_ids_val)
	{
        $is_agree = 0;
        if($xt_ids_val==''){
            $is_agree = 1;
            $xt_ids_val = Db::name('wf_run_sign')->where('id', $sing_sign)->value('sign_uids');
        }
		return Db::name('wf_run_sign')->where('id', $sing_sign)->update(['uid'=>$xt_ids_val,'is_agree' => $is_agree, 'content' => $check_con, 'dateline' => time()]);
	}

    function dataRunCc($page,$limit,$map,$field='f.*')
    {
        $offset = ($page-1)*$limit;
        $data = Db::name('wf_run_process_cc')->alias('f')->join('wf_run r', 'f.run_id = r.id')->where($map)->field($field)->limit($offset,(int)$limit)->group('r.id')->order('r.id desc')->select()->toArray();
        $count = Db::name('wf_run_process_cc')->alias('f')->join('wf_run r', 'f.run_id = r.id')->where($map)->field($field)->group('r.id')->count();
        return ['data'=>$data,'count'=>$count];
    }

    function dataRunProcess($map, $mapRaw,$field, $order,$page,$limit)
    {
        $offset = ($page-1)*$limit;
        return Db::name('wf_run_process')->alias('f')->join('wf_flow w', 'f.run_flow = w.id')->join('wf_run r', 'f.run_id = r.id')->join('wf_flow_process fr', 'f.run_flow_process = fr.id')->where($map)->whereRaw($mapRaw)->field($field)->limit($offset,(int)$limit)->order($order)->select();
    }

    function dataRunMy($uid,$page,$limit,$map)
    {
        $offset = ($page-1)*$limit;
        $data = Db::name('wf_run_process')->alias('f')->join('wf_flow w', 'f.run_flow = w.id')->join('wf_run r', 'f.run_id = r.id')->where('r.uid',$uid)->where($map)->limit($offset,(int)$limit)->group('r.id')->order('r.id desc')->select()->toArray();
        $count = Db::name('wf_run_process')->alias('f')->join('wf_flow w', 'f.run_flow = w.id')->join('wf_run r', 'f.run_id = r.id')->where('r.uid',$uid)->where($map)->group('r.id')->count();
        return ['data'=>$data,'count'=>$count];
    }
	
	function dataRunProcessGroup($map, $field, $order, $group)
	{
		return Db::name('wf_run_process')->alias('f')->join('wf_flow w', 'f.run_flow = w.id')->join('wf_run r', 'f.run_id = r.id')->where($map)->field($field)->order($order)->group($group)->select();
	}
    /*获取会签数据信息*/
    function dataRunSing($map, $mapRaw,$field, $order,$page,$limit){
        $offset = ($page-1)*$limit;
        $data =  Db::name('wf_run_sign')->alias('f')->join('wf_run_process f2', 'f2.id = f.run_flow_process')->join('wf_run r', 'f.run_id = r.id')->join('wf_flow w', 'r.flow_id = w.id')->where($map)->whereRaw($mapRaw)->field($field)->limit($offset,(int)$limit)->order($order)->select()->toArray();
        $count =  Db::name('wf_run_sign')->alias('f')->join('wf_run_process f2', 'f2.id = f.run_flow_process')->join('wf_run r', 'f.run_id = r.id')->join('wf_flow w', 'r.flow_id = w.id')->where($map)->whereRaw($mapRaw)->field($field)->limit($offset,(int)$limit)->order($order)->count();
        return ['data'=>$data,'count'=>$count];
    }
	
	
}
