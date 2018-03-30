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
	   $this->role = session('role');
    }
	/*流程监控*/
	public function index($map = [])
	{
		$workflow = new workflow();
		$flow = $workflow->worklist();
		$this->assign('list', $flow);
		return $this->fetch();
		
	}
	public function btn($wf_fid,$wf_type,$status)
	{
		$url = url("/index/flow/do_check/",["wf_type"=>$wf_type,"wf_title"=>'2','wf_fid'=>$wf_fid]);
		$url_star = url("/index/flow/start/",["wf_type"=>$wf_type,"wf_title"=>'2','wf_fid'=>$wf_fid]);
		switch ($status)
		{
		case 0:
		  return '<span class="btn  radius size-S" onclick=layer_show(\'发起工作流\',"'.$url_star.'","450","350")>发起工作流</span>';
		  break;
		case 1:
			$st = 0;
			$workflow = new workflow();
			$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type);
			if($flowinfo['process']['auto_person']==4){
				$user = explode(",", $flowinfo['process']['auto_sponsor_ids']);
				if (in_array($this->uid, $user)) {
					$st = 1;
				}
			}

			if($flowinfo['process']['auto_person']==5){
				$user = explode(",", $flowinfo['process']['auto_role_ids']);
				if (in_array($this->role, $user)) {
					$st = 1;
				}
			}
			if($st == 1){
				 return '<span class="btn  radius size-S" onclick=layer_show(\'审核\',"'.$url.'","850","650")>审核</span>';
				}else{
				 return '<span class="btn  radius size-S">无权限</span>';
			}
		
		 
		  break;
		default:
		  return '';
		}
	}
	public function status($status)
	{
		switch ($status)
		{
		case 0:
		  return '<span class="label radius">保存中</span>';
		  
		  break;
		case 1:
		  return '<span class="label radius" >流程中</span>';
		  break;
		case 2:
		  return '<span class="label label-success radius" >审核通过</span>';
		  break;
		default: //-1
		  return '<span class="label label-danger radius" >退回修改</span>';
		}
		
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
		$data = $this->request->param();
		$workflow = new workflow();
		$flowinfo = $workflow->workdoaction($data,$this->uid);
		return msg_return('Success!');
	}
}