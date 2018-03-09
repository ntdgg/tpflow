<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use workflow\workflow;
use think\facade\Session;

class Flow extends Controller {
    
	public function start()
	{
		$wf_type = input('wf_type');
		$info = ['wf_title'=>input('wf_title'),'wf_fid'=>input('wf_fid')];
		$workflow = new workflow();
		$flow = $workflow->getWorkFlow($wf_type);
		$this->assign('flow',$flow);
		$this->assign('info',$info);
		return $this->fetch();
	}
	public function statr_save()
	{
		 Session::set('uid',1);
		$wf_type = input('wf_type');
		$wf_id = input('wf_id');
		$wf_fid = input('wf_fid');
		$workflow = new workflow();
		$flow = $workflow->startworkflow($wf_id,$wf_fid,$wf_type);
		dump($flow);
		
	}
}