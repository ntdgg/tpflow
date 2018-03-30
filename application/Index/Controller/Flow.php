<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use workflow\workflow;
use think\facade\Session;

class Flow extends Controller {
	
	protected function initialize()
    {
       $this->uid = session('uid');
    }
	/*流程监控*/
	public function index($map = [])
	{
		$workflow = new workflow();
		$flow = $workflow->worklist();
		$this->assign('list', $flow);
		return $this->fetch();
		
	}
	
    /*发起流程，选择工作流*/
	public function start()
	{
		$wf_type = input('wf_type');
		$info = ['wf_type'=>input('wf_type'),'wf_title'=>input('wf_title'),'wf_fid'=>input('wf_fid')];
		$workflow = new workflow();
		$flow = $workflow->getWorkFlow($wf_type);
		$this->assign('flow',$flow);
		$this->assign('info',$info);
		return $this->fetch();
	}
	/*正式发起工作流*/
	public function statr_save()
	{
		$wf_type = input('wf_type');
		$wf_id = input('wf_id');
		$wf_fid = input('wf_fid');
		$data = $this->request->param();
		$workflow = new workflow();
		$flow = $workflow->startworkflow($data,$this->uid);
		if($flow['code']==1){
			return msg_return('Success!');
		}
	}
	/*工作流状态信息*/
	public function get_flowinfo()
	{
		$wf_type = input('wf_type');
		$wf_fid = input('wf_fid');
		$workflow = new workflow();
		$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type);
		
	}
	public function do_check()
	{
		$wf_fid = input('wf_fid');
		$wf_type = input('wf_type');
		$info = ['wf_title'=>input('wf_title'),'wf_fid'=>$wf_fid,'wf_type'=>$wf_type];
		$workflow = new workflow();
		$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type);
		$this->assign('info',$info);
		$this->assign('flowinfo',$flowinfo);
		return $this->fetch();
	}
	public function do_check_save()
	{
		Session::set('uid',1);
		$data = $this->request->param();
		$workflow = new workflow();
		$flowinfo = $workflow->workdoaction($data);
		return msg_return('Success!');
	}
}