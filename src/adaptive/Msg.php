<?php
/**
 *+------------------
 * Tpflow 消息节点数据
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\adaptive;

use tpflow\lib\unit;

class Msg
{

    protected $mode;

    public function __construct()
    {
        if (unit::gconfig('wf_db_mode') == 1) {
            $className = '\\tpflow\\custom\\think\\AdapteeMsg';
        } else {
            $className = unit::gconfig('wf_db_namespace') . 'AdapteeMsg';
        }
        $this->mode = new $className();
    }

    /**
     * 查找消息节点信息
     * @param $map
     * @return int|void
     */
    public static function find($map)
    {
        $info = self::findWhere($map);
        if($info){
            $msg_api = unit::gconfig('msg_api') ?? '';
            if (class_exists($msg_api)) {
                (new $msg_api())->node_msg($info['run_id'],$info['process_msgid']);
            }
            /*更
            /*更新执行消息节点*/
            return (new Msg())->mode->update($map);
        }
    }

    /**
     * 查询是否有节点消息
     * @param $map
     * @return array|\think\facade\Db|\think\Model|null
     */
    public static function findWhere($map)
    {
        return (new Msg())->mode->findWhere($map);
    }

    /**
     * 更新节点数据
     * @param $map
     * @return int
     */
    public static function update($map)
    {
        return (new Msg())->mode->update($map);
    }

    /**
     * 添加节点消息
     * @param $data
     * @return false|int|string|void
     */
    public static function add($data)
    {
        if(!self::findWhere([['run_id','=',$data['run_id']],['process_id','=',$data['process_id']],['process_msgid','=',$data['process_msgid']]]))
        {
            return (new Msg())->mode->add($data);
        }
    }


}