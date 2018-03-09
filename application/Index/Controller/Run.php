<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use Formdesign;

class Run extends Controller {
    
	public $_obj_model='';
    protected function model()
    {
        if($this->_obj_model)
            return $this->_obj_model;
        return $this->_obj_model = db('flow');
    }
    
    public function index(){
	    
        echo '未完成';exit;
        
        
        $map = array(
            'is_del'=>0,
        );
        $page='';
        $list = array();
        $count = $this->model()->where($map)->count('id');
        if ($count > 0)
        {
            import("@.Org.Util.Page");
            $p = new \Page($count, 5);
            //分页跳转的时候保证查询条件
            foreach ($_GET as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= $key.'=' . urlencode($val) . '&';
                }
            }
             //分页显示
            $page = $p->show();
            
            $list = $this->model()->relation(true)->field('id,form_id,flow_name,flow_desc,updatetime,dateline')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();

        }
        $this->assign('page', $page);
        $this->assign('list', $list);

        $this->display();
    }
    //发起工作流程  然后缓存 1 流程 2 表单 3 等数据
    public function add()
    {
        //self::edit('add');
        
        $flow_id = intval(input('flow_id'));//流程ID
        //先找到流程
        $flow_one = db('flow')->where('id',$flow_id)->where('is_del',0)->find();
        if(!$flow_one)
        {
            $this->error('未找到流程');
        }
        //流程步骤
        $map = array(
            'flow_id'=>$flow_one['id'],
            'is_del'=>0,
        );
        $flow_process = db('flow_process')->field('is_del,updatetime,dateline',true)->where($map)->order('id asc')->select();
        
        if(!$flow_process)
        {
            $this->error('未找到流程步骤');
        }
        
        //找出表单
        $map = array(
            'id'=>$flow_one['form_id'],
            'is_del'=>0,
        );
        $form_one = db('form')->where($map)->find();
        if(!$form_one)
        {
            $this->error('未找到表单数据');
        }
        
        //默认名称
        $run_name = $flow_one['flow_name'].'('.date('Y-m-d H:i:s').')'; 
       
        //找到 流程第一步
        $flow_process_first = array();
        foreach($flow_process as $value)
        {
            if($value['process_type'] == 'is_one')
            {
                $flow_process_first = $value;
                break;
            }
            
        }
        
        if(!$flow_process_first)
        {
            $this->error('未找到流程第一步骤');
        }
        
        //条件满足-----------------
        
        //发起工作
        $this->model()->startTrans();
        $data = array(
            'pid'=>0,
            //'uid'=>$this->_user_id,
            'flow_id'=>$flow_id,
            //'cat_id'=>$flow_one['cat_id'],
            'run_name'=>$run_name,
            'run_flow_id'=>$flow_id,
            'run_flow_process'=>$flow_process_first['id'],
            'dateline'=>time(),
        );
        //dump($data);
        $run_id = db('run')->insertGetId($data);
        if($run_id<=0)
            $this->error('发起失败，请重试');
            
        //添加步骤
        $data = array(
            //'uid'=>$this->_user_id,
            'run_id'=>$run_id,
            'run_flow'=>$flow_id,
            'run_flow_process'=>$flow_process_first['id'],
            'parent_flow'=>0,
            'parent_flow_process'=>0,
            'run_child'=>0,//未处理，第一步不能进入子流程
            'remark'=>'',
            'is_sponsor'=>1,
            'status'=>1,
            'js_time'=>time(),
            'bl_time'=>time(),
            'dateline'=>time(),
        );
        $trans  = $process_id = db('run_process')->insertGetId($data);
        //开始缓存 表单 流程，等后台要用的全部数据，这样即使流程删除后也不影响这个工作 
        if($trans)
        {
             $run_cache = array(
                'run_id'=>$run_id,
                'form_id'=>$flow_one['form_id'],
                'flow_id'=>$flow_one['id'],
                'run_form'=>json_encode($form_one),//从 serialize 改用  json_encode 兼容其它语言
                'run_flow'=>json_encode($flow_one),
                'run_flow_process'=>json_encode($flow_process), //这里未缓存 子流程 数据是不完善的， 后期会完善
                'dateline'=>time()
            );
            $trans = db('run_cache')->insertGetId($run_cache);
        }
        
        if(!$trans)
        {
            $this->model()->rollback();
            $this->error('发起失败，请重试');
        }
        $this->model()->commit();
        //run log
        //记录日志
        
        
        $this->redirect('/index/Run/edit/process_id/'.$process_id);
        
        //$this->display('edit');
        
    }
    
    //填写流程表单
    public function edit()
    {
        
        $process_id = intval(input('process_id'));//步骤ID
        
        $run_process = array();
        if($process_id>0)
        {
            
            $run_process =db('run_process')->field('is_del,updatetime,dateline',true)->where('id',$process_id)->where('is_del',0)->find();
        }
        if(!$run_process)
        {
            $this->error('未找到步骤信息');
        }
        //run 数据
        $map = array(
            'id'=>$run_process['run_id'],
        );
        $run_one = db('run')->where($map)->find();
        if(!$run_one)
        {
            $this->error('未找到工作流程信息');
        }
        
        //从缓存中获取数据
        $map = array(
            'run_id'=>$run_process['run_id'],
        );
        $run_cache = db('run_cache')->where($map)->find();
        $run_cache['run_form'] = json_decode($run_cache['run_form'],true);
        $run_cache['run_flow'] = json_decode($run_cache['run_flow'],true);
        $run_cache['run_flow_process'] = json_decode($run_cache['run_flow_process'],true);
        
        
        $formdesign = new \Formdesign;
        $form_data = array();
        $design_content ='';
        
        $this->assign('run_one',$run_one);
        $this->assign('run_process',$run_process);
        $this->assign('design_content',$design_content);

        return $this->fetch('edit');
    }

	public function edit_save()
	{
		echo '未完成';
	}

  
}