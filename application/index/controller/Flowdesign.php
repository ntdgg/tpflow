<?php
namespace app\index\Controller;
use app\common\controller\admin;
use think\Db;
use workflow\workflow;

class Flowdesign extends Admin {
    public function initialize()
    {
        parent::initialize();
        $this->work = new workflow();
    }
    /**
	 * 流程设计首页
	 * @param $map 查询参数
	 */
    public function lists($map = []){
        $this->assign('list',$this->work->FlowApi('List'));
		$this->assign('type', ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息']);
        return  $this->fetch();
    }
    /**
	 * 流程添加
	 */
    public function add()
    {
		if ($this->request->isPost()) {
			$data = input('post.');
			$data['uid']=session('uid');
			$data['add_time']=time();
			$ret= $this->work->FlowApi('AddFlow',$data);
			if($ret['code']==0){
				return msg_return('发布成功！');
				}else{
				return msg_return($ret['data'],1);
			}
	   }
	   $this->assign('type', ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息']);
       return  $this->fetch();
    }
	 /**
	 * 流程修改
	 */
	public function edit()
    {
        if ($this->request->isPost()) {
			$data = input('post.');
			$ret= $this->work->FlowApi('EditFlow',$data);
			if($ret['code']==0){
				return msg_return('修改成功！');
				}else{
				return msg_return($ret['data'],1);
			}
	   }
	   if(input('id')){
		 $this->assign('info', $this->work->FlowApi('GetFlowInfo',input('id')));
	   }
	   $this->assign('type', ['news'=>'新闻信息','cnt'=>'合同信息','paper'=>'证件信息']);
       return $this->fetch('add');
    }
	/**
	 * 状态改变
	 */
	public function change()
	{
		 if ($this->request->isGet()) {
			$data = ['id'=>input('id'),'status'=>input('status')];
			$ret= $this->work->FlowApi('EditFlow',$data);
			if($ret['code']==0){
				$this->success('操作成功',url('Flowdesign/lists'));
				}else{
				$this->error('操作失败！',url('Flowdesign/lists'));
			}
		 }
	}
	/**
	 * 工作流设计界面
	 */
    public function index(){
        $flow_id = intval(input('flow_id'));
        if($flow_id<=0){
            $this->error('参数有误，请返回重试!');
		}
        $one = $this->work->FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
            $this->error('未找到数据，请返回重试!');
        }
        $this->assign('one', $one);
        $this->assign('process_data',$this->work->ProcessApi('All',$flow_id));
        return $this->fetch();
    }
    /**
	 * 删除流程
	 **/
    function delete_process()
    {
		return json($this->work->ProcessApi('ProcessDel',input('flow_id'),input('process_id')));
    }
	public function del_allprocess()
	{
		return json($this->work->ProcessApi('ProcessDelAll',input('flow_id')));
	}
	/**
	 * 添加流程
	 **/
    public function add_process()
    {
        $flow_id = input('flow_id');
        $one = $this->work->FlowApi('GetFlowInfo',$flow_id);
        if(!$one){
          return json(['status'=>0,'msg'=>'添加失败,未找到流程','info'=>'']);
        }
		return json($this->work->ProcessApi('ProcessAdd',$flow_id));
    }
    /**
	 * 保存布局
	 **/
    public function save_canvas()
    {
		return json($this->work->ProcessApi('ProcessLink',input('flow_id'),input('process_info')));
    }
    //右键属性
    public function attribute()
    {
	    $info = $this->work->ProcessApi('ProcessAttView',input('id'));
	    $this->assign('op',$info['show']);
        $this->assign('one',$info['info']);
		$this->assign('from',$info['from']);
        $this->assign('process_to_list',$info['process_to_list']);
        $this->assign('child_flow_list',$info['child_flow_list']);
		return $this->fetch();
    }
    public function save_attribute()
    {
	    $data = input('post.');
		
		return json($this->work->ProcessApi('ProcessAttSave',$data['process_id'],$data));
    }
   
	//用户选择控件
    public function super_user()
    {
		$this->assign('user',db('user')->field('id,username')->select());
		$this->assign('kid',input('kid'));
        return $this->fetch();
    }
	//用户选择控件
    public function super_role()
    {
		$this->assign('role',db('role')->field('id,name as username')->select());
        return $this->fetch();
    }
	public function super_get()
	{
		 $type = trim(input('type'));
		 if($type=='user'){
			$info =  db('user')->where('username','like','%'.input('key').'%')->field('id as vlaue,username as text')->select();
		 }else{
			 $info =  db('role')->where('name','like','%'.input('key').'%')->field('id as vlaue,name as text')->select();
		 }
		 return ['data'=>$info,'code'=>1,'msg'=>'查询成功！'];
	}
}