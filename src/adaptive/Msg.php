<?php
/**
 *+------------------
 * Tpflow 统一标准接口------代理模式数据库操作统一接口
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
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
     * 获取用户列表
     *
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
     * 获取用户列表
     *
     */
    public static function findWhere($map)
    {
        return (new Msg())->mode->findWhere($map);
    }
    /**
     * 获取用户列表
     *
     */
    public static function update($map)
    {
        return (new Msg())->mode->update($map);
    }

    /**
     * 获取角色列表
     *
     */
    public static function add($data)
    {
        if(!self::findWhere([['run_id','=',$data['run_id']],['process_id','=',$data['process_id']],['process_msgid','=',$data['process_msgid']]]))
        {
            return (new Msg())->mode->add($data);
        }
    }


}