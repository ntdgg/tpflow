<?php
/**
 *+------------------
 * Tpflow 普通提交工作流
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
use tpflow\lib\unit;

class MsgFlow
{
    /**
     * 任务自动执行
     *
     * @param mixed $npid 运行步骤id
     * @param mixed $run_wfid 设计器id
     */
    public function do()
    {

    }
}