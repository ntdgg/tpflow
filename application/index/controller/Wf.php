<?php
namespace app\index\Controller;
use app\common\controller\admin;
use think\Db;
use workflow\workflow;
use think\facade\Session;

class wf extends Admin {
    public function initialize()
    {
        parent::initialize();
        $this->work = new workflow();
		$this->uid = session('uid');
	    $this->role = session('role');
		$this->Tmp  = '../extend/workflow/view/';
		$this->table  = Db :: query("select replace(TABLE_NAME,'".config('database.prefix')."','')as name,TABLE_COMMENT as title from information_schema.tables where table_schema='".config('database.database')."' and table_type='base table' and TABLE_COMMENT like '[work]%';");
    }
	
	
	
    /*发起流程，选择工作流*/
	public function wfstart()
	{
		$info = ['wf_type'=>input('wf_type'),'wf_title'=>input('wf_title'),'wf_fid'=>input('wf_fid')];
		$flow =  $this->work->getWorkFlow(input('wf_type'));
		$this->assign('flow',$flow);
		$this->assign('info',$info);
		return $this->fetch();
	}
	/*正式发起工作流*/
	public function statr_save()
	{
		$data = $this->request->param();
		$flow = $this->work->startworkflow($data,$this->uid);
		if($flow['code']==1){
			return $this->msg_return('Success!');
		}
	}
	
	public function wfcheck()
	{
		$info = ['wf_title'=>input('wf_title'),'wf_fid'=>input('wf_fid'),'wf_type'=>input('wf_type')];
		$this->assign('info',$info);
		$this->assign('flowinfo',$this->work->workflowInfo(input('wf_fid'),input('wf_type'),['uid'=>$this->uid,'role'=>$this->role]));
		return $this->fetch();
	}
	public function do_check_save()
	{
		$data = $this->request->param();
		$flowinfo =  $this->work->workdoaction($data,$this->uid);
		
		if($flowinfo['code']=='0'){
			return $this->msg_return('Success!');
			}else{
			return $this->msg_return($flowinfo['msg'],1);
		}
	}
	public function ajax_back()
	{
		$flowinfo =  $this->work->getprocessinfo(input('back_id'),input('run_id'));
		return $flowinfo;
	}
	public function Checkflow($fid){
		return $this->work->SuperApi('CheckFlow',$fid);
	}
	
	 public function wfup()
    {
        return $this->fetch();
    }
	
	public function wfend()
	{
		$flowinfo =  $this->work->SuperApi('WfEnd',input('get.id'),$this->uid);
		return $this->msg_return('Success!');
	}
	public function wfupsave()
    {
        $files = $this->request->file('file');
        $insert = [];
        foreach ($files as $file) {
            $path = \Env::get('root_path') . '/public/uploads/';
            $info = $file->move($path);
            if ($info) {
                $data[] = $info->getSaveName();
            } else {
                $error[] = $file->getError();
            }
        }
        return $this->msg_return($data,0,$info->getInfo('name'));
    }
	public function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
	{
		$ret = ["code" => $code, "msg" => $msg, "data" => $data];
		$extend['opt'] = [
			'alert'    => $alert,
			'close'    => $close,
			'redirect' => $redirect,
			'url'      => $url,
		];
		$ret = array_merge($ret, $extend);
		return json($ret);
	}
	public function wflogs($id,$wf_type,$type='html'){
		$logs = $this->work->FlowLog('logs',$id,$wf_type);
		echo $logs[$type];
	}
	public function wfgl(){
		return $this->fetch($this->Tmp.'wfgl.html');
	}
}