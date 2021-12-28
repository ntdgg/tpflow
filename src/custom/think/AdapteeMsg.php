<?php

/**
 *+------------------
 * Tpflow 工作流日志消息
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeMsg
{
    /**
     * 工作流审批日志记录
     *
     **/
    function add($data)
    {
        $ret = Db::name('wf_run_process_msg')->insertGetId($data);
        if (!$ret) {
            return false;
        }
        return $ret;
    }
    function update($map)
    {
        return Db::name('wf_run_process_msg')->where($map)->update(['status'=>2,'uptime'=>time()]);
    }
    function findWhere($map)
    {
        return Db::name('wf_run_process_msg')->where($map)->find();
    }


}