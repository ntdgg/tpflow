<?php
/**
 *+------------------
 * TPFLOW 工作流回退
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
declare (strict_types=1);

namespace tpflow\service\command;

//数据库操作
use tpflow\adaptive\Info;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Process;
use tpflow\adaptive\Log;
use tpflow\adaptive\Bill;
use tpflow\adaptive\Run;

class EndFlow
{
	/**
	 * @param $pid
	 * @param $run_id
	 * @return bool|void
	 */
	public static function doTask($pid,$run_id)
	{
		if($pid==''){
			return 0;
		}
		$process = Process::GetProcessInfo($pid,$run_id);
		if($process['process_type']=='is_end'){
			return 1;
		}else{
			return 0;
		}
	}
}