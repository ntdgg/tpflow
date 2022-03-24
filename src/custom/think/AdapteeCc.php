<?php

/**
 *+------------------
 * Tpflow 抄送处理
 *+------------------
 * Copyright (c) 2018~2025 liuzhiyun.com All rights reserved.  本版权不可删除，侵权必究
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

declare (strict_types=1);

namespace tpflow\custom\think;

use think\facade\Db;

class AdapteeCc
{
    /**
     * 添加
     *
     **/
    function add($data)
    {
        $ret = Db::name('wf_run_process_cc')->insertGetId($data);
        if (!$ret) {
            return false;
        }
        return $ret;
    }
    /**
     * 更新
     *
     **/
    function update($data)
    {
        return Db::name('wf_run_process_cc')->update($data);
    }
    /**
     * 查询
     *
     **/
    function findWhere($map)
    {
        return Db::name('wf_run_process_cc')->where($map)->find();
    }
}