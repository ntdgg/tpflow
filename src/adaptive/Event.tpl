<?php

namespace [namespace];

use think\facade\Db;
/**
 *+------------------
 * [class] 工作流类
 *+------------------
 */
class [class] {
	
	protected $id; //对应单据编号
    protected $run_id; //运行中的流程id
    public function  __construct($id,$run_id='',$data=''){
        $this->id =$id;
        $this->run_id =$run_id;
    }
	[before]

	[after]

	[cancel]
}
?>