<?php

namespace [namespace];

use think\facade\Db;
use tpflow\lib\unit;
/**
 *+------------------
 * [class] 工作流类
 *+------------------
 */
class [class] {
	
	protected $id; //对应单据编号
    protected $run_id; //运行中的流程id
    protected $userinfo; //用户信息
    public function  __construct($id,$run_id='',$data=''){
        $this->id =$id;
        $this->run_id =$run_id;
		$this->userinfo = unit::getuserinfo();
    }
	[before]

	[after]

	[cancel]
}
?>