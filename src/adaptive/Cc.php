<?php
/**
 *+------------------
 * Tpflow 工作流引擎抄送类
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Cc
{

    protected $mode;

    public function __construct()
    {
        if (unit::gconfig('wf_db_mode') == 1) {
            $className = '\\tpflow\\custom\\think\\AdapteeCc';
        } else {
            $className = unit::gconfig('wf_db_namespace') . 'AdapteeCc';
        }
        $this->mode = new $className();
    }

    /**
     * 处理签阅
     * @param $id
     * @return array
     */
    public static function ccCheck($id){
        $info = self::findWhere([['status','=',0],['id','=',$id],['auto_ids','find in set',unit::getuserinfo('uid')]]);//查找需要确认的
        if(!$info){
            return unit::msg_return('对不起，找不到需要签阅的单据', 1);
        }
        $thisuid = unit::getuserinfo('uid');

        $ids = explode(',',$info['auto_ids']);//确认人员数组

        if (!in_array($thisuid, $ids))
        {
            return unit::msg_return('对不起，您已经确认了！', 1);
        }
        $newids = array_diff($ids,(array)$thisuid);
        if(count($newids)>=1){
            $update = [
                'id'=>$id,'auto_ids'=>implode(',',$newids),'uptime'=>time()
            ];
        }else{
            $update = [
                'id'=>$id,'auto_ids'=>'','uptime'=>time(),'status'=>2
            ];
        }
        if(!self::update($update)){
            return unit::msg_return('对不起，签阅失败，您可以联系管理员确认原因！！', 1);
        }
        if(!Log::AddLog(['wf_fid'=>$info['from_id'],'wf_type'=>$info['from_table'],'run_id'=>$info['run_id']],'签阅成功')) {
            return unit::msg_return('对不起，日志记录失败，您可以联系管理员确认原因！！', 1);
        }
        return unit::msg_return('操作成功');
    }
    /**
     * 查询抄送信息
     * @param $map
     * @return int|void
     */
    public static function ccStatus($table,$id)
    {
        $find = self::findWhere([['status','=',0],['from_id','=',$id],['from_table','=',$table],['auto_ids','find in set',unit::getuserinfo('uid')]]);
        if(!$find){
            return '';
        }
        return '<span style="color: #f66f6a;" class="btn" onclick=Tpflow.cc('.$find['id'].')>签阅</span>';
    }
    /**
     * 查找是否有抄送数据
     * @param $map
     * @return array|\think\facade\Db|\think\Model|null
     */
    public static function findWhere($map)
    {
        return (new Cc())->mode->findWhere($map);
    }

    /**
     * 更新抄送数据
     * @param $map
     * @return int
     */
    public static function update($map)
    {
        return (new Cc())->mode->update($map);
    }

    /**
     * 添加抄送数据
     * @param $data
     * @return false|int|string|void
     */
    public static function add($data)
    {
        if(!self::findWhere([['run_id','=',$data['run_id']],['process_id','=',$data['process_id']],['process_ccid','=',$data['process_ccid']]]))
        {
            return (new Cc())->mode->add($data);
        }
    }
}