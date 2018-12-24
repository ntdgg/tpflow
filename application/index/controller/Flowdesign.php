<?php
namespace app\index\Controller;
use app\common\controller\admin;
use think\Db;
use workflow\workflow;

class Flowdesign extends Admin {
	protected $work = null;
    public function initialize()
    {
        parent::initialize();
        $this->work = new workflow();
    }
	/**
	 *前置方法
	 */
	protected $beforeActionList = [
        'type'  =>  ['only'=>'add,edit,lists'],
    ];
	/**
	 *前置方法角色及类别部署
	 */
	protected function type()
    {
        $wf_type = [
			'news'=>'新闻信息',
			'cnt'=>'合同信息',
			'paper'=>'证件信息'
		];
		$this->assign('type', $wf_type);
    }

    /**
	 * 流程设计首页
	 * @param $map 查询参数
	 */
    public function lists($map = []){
		$list = $this->work->FlowApi('List');
        $this->assign('list', $list);
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
	 * 保存布局  位置 和 步骤连接
	 **/
    public function save_canvas()
    {
		return json($this->work->ProcessApi('ProcessLink',input('flow_id'),input('process_info')));
    }
    
    public function ids_parse($str,$dot_tmp=',')
	{
		if(!$str) return '';
		if(is_array($str)){
			$idarr = $str;
		}else{
			$idarr = explode(',',$str);
		}
		$idarr = array_unique($idarr);
		$dot = '';
		$idstr ='';
		foreach($idarr as $id){
			$id = intval($id);
			if($id>0){
				$idstr.=$dot.$id;
				$dot = $dot_tmp;
			}
		}
		if(!$idstr) $idstr=0;
		return $idstr;
	}
    //右键属性
    public function attribute()
    {
        $process_id = intval(input('id'));
        $op = trim(input('op'));
        if(!$op)$op = 'basic';
        //连接数据表用的。表 model 
        $flow_model = db('flow');
        $process_model = db('flow_process');
        $one = db('flow_process')->find($process_id);
        if(!$one){
            exit('T_T 未找到步骤信息');
        }
        $flow_one = db('flow')->find($one['flow_id']);
        if(!$flow_one){
            exit('T_T 未找到流程信息');
        }
		
        //初始化必须字段
        $one['process_to'] = $one['process_to']=='' ? array() : explode(',',$one['process_to']);
        //转出条件 但没 process_to
        if($op=='judge' && empty($one['process_to']))
        {
            exit('T_T 请先设置属性 -> 选择下一步步骤');
        }
        $one['style'] = json_decode($one['style'],true);
        $one['write_fields'] = $one['write_fields']=='' ? array() : explode(',',$one['write_fields']);//可写字段
        $one['secret_fields'] = $one['secret_fields']=='' ? array() : explode(',',$one['secret_fields']);//保密 隐藏的字段
        $one['out_condition'] = self::parse_out_condition($one['out_condition'],'');//json
        //备选步骤  同一个流程全部步骤
        $map = array(
            'flow_id'=>$one['flow_id'],//流程ID
            //'id'=>array('neq',$one['id']),//不用排除当前步骤ID    子流程结束后 返回步骤  要用
            'is_del'=>0,
        );
        $process_to_list = db('flow_process')->field('id,process_name,process_type')->where($map)->select();
        //子流程 列表 process_to_list
        $map = array(
            'is_del'=>0,
        );
        $child_flow_list = db('flow')->field('id,flow_name')->where($map)->select();
        //赋值到模板上
        $this->assign('op',$op);
        $this->assign('one',$one);
		$this->assign('from',$this->get_db_column_comment($flow_one['type']));
        $this->assign('process_to_list',$process_to_list);
        $this->assign('child_flow_list',$child_flow_list);
		return $this->fetch();
    }
    
    //$json_data is json    
    //return json
    public function parse_out_condition($json_data,$field_data)
    {
        $array = json_decode($json_data,true);
        if(!$array)
        {
            return '[]';
        }
        
        $json_data = array();//重置
        foreach($array as $key=>$value)
        {
            $condition = '';
            foreach($value['condition'] as $val)
            {
                //匹配 $field_data 
                //把data_x 替换回 中文名称
                $preg =  "/'(data_[0-9]*|checkboxs_[0-9]*)'/s";
                preg_match_all($preg,$val,$temparr);
                $val_text = '';
                foreach($temparr[0] as $k=>$v)
                {
                    $field_name = self::get_field_name($temparr[1][$k],$field_data);
                    if($field_name)
                        $val_text = str_replace($v,"'".$field_name."'",$val);
                    else
                        $val_text = $val;
                }
                
                $condition.='<option value="'.$val.'">'.$val_text.'</option>';
            }
            
            $value['condition'] = $condition;
            $json_data[$key] = $value;
        }
        
        return json_encode($json_data);
    }
    
    //通过 name  data_x 找到 title
    public function get_field_name($field,$field_data)
    {
        $field = trim($field);
        if(!$field) return '';
        $title = '';
        foreach($field_data as $value)
        {
            if($value['leipiplugins'] =='checkboxs' && $value['parse_name']==$field)
            {
                $title = $value['title'];
                break;
            }else if($value['name']==$field)
            {
                $title = $value['title'];
                break;
            }
        }
        return $title;
    }
    
    public function save_attribute()
    {
        $flow_id = intval(input('post.flow_id'));//流程ID
		$process_id = intval(input('post.process_id'));//步骤ID
        $process_name = trim(input('post.process_name'));//步骤名称
        $process_type = trim(input('post.process_type'));//类型
		$auto_person = intval(input('post.auto_person'));//自动选人
		$process_to = $this->ids_parse(input('post.process_to/a'));//下一步
		$auto_unlock = intval(input('post.auto_unlock'));//>预先设置自动选人，更方便转交工作
		$auto_sponsor_ids = trim(input('post.auto_sponsor_ids'));//指定主办人
        $auto_sponsor_text = trim(input('post.auto_sponsor_text'));
        $auto_respon_ids = trim(input('post.auto_respon_ids'));//指定经办人
        $auto_respon_text = trim(input('post.auto_respon_text'));
        $auto_role_ids = trim(input('post.auto_role_ids'));//指定角色
        $auto_role_text = trim(input('post.auto_role_text'));
        $range_user_ids = trim(input('post.range_user_ids'));//授权人员
        $range_user_text = trim(input('post.range_user_text'));
        $range_dept_ids = trim(input('post.range_dept_ids'));//授权部门
        $range_dept_text = trim(input('post.range_dept_text'));
        $range_role_ids = trim(input('post.range_role_ids'));//授权角色
        $range_role_text = trim(input('post.range_role_text'));
        //操作
        $receive_type = intval(input('post.receive_type'));//交接方式
        $is_user_end = intval(input('post.is_user_end'));//允许主办人办结流程
        $is_userop_pass = intval(input('post.is_userop_pass'));//经办人可以转交下一步
        $is_sing = intval(input('post.is_sing'));//会签方式
        $sign_look = intval(input('post.sign_look'));//可见性
        $is_back = intval(input('post.is_back'));//回退方式
        //转出条件
       $process_condition =  trim(input('post.process_condition'),',');//process_to
       $process_condition = explode(',',$process_condition);
       $out_condition = array();
       foreach($process_condition as $value)
       {
           $value = intval($value);
           if($value>0)
           {
               $condition = trim($_POST['process_in_set_'.$value],"@leipi@");
               $condition = $condition ? explode("@leipi@",$condition) : array();
               $out_condition[$value] = array(
                     'condition'=>$condition,
                     'condition_desc'=>trim($_POST['process_in_desc_'.$value]),
               );
           }
       }
       //样式
       $style_width = intval(input('post.style_width'));
       $style_height = intval(input('post.style_height'));
       $style_color = trim(input('post.style_color'));
       $style_icon = trim(input('post.style_icon'));
		//end 避免出错，都列出来先
       $process_model = db('flow_process');
       //对数据进行判断
       if($flow_id<=0 || $process_id<=0){
		   return msg_return('保存失败',1);
       }
        $process_one = db('flow_process')->find($process_id);
        if(!$process_one){
			return msg_return('未找到步骤，请刷新再试',1);
        }
        //保存数据， 不列出来的，直接写这里也可以呀
        $data = array(
            //常规
            'process_name'=>$process_name,
            'process_type'=>$process_type,
			'process_to'=>$process_to,
            //权限
            'auto_person'=>$auto_person,
            'auto_unlock'=>$auto_unlock,
            'auto_sponsor_ids'=>$auto_sponsor_ids,
            'auto_sponsor_text'=>$auto_sponsor_text,
            'auto_respon_ids'=>$auto_respon_ids,
            'auto_respon_text'=>$auto_respon_text,
            'auto_role_ids'=>$auto_role_ids,
            'auto_role_text'=>$auto_role_text,
            'range_user_ids'=>$range_user_ids,
            'range_user_text'=>$range_user_text,
            'range_dept_ids'=>$range_dept_ids,
            'range_dept_text'=>$range_dept_text,
            'range_role_ids'=>$range_role_ids,
            'range_role_text'=>$range_role_text,
            //操作
            'receive_type'=>$receive_type,
            'is_user_end'=>$is_user_end,
            'is_userop_pass'=>$is_userop_pass,
            'is_sing'=>$is_sing,
            'sign_look'=>$sign_look,
            'is_back'=>$is_back,
            //转出条件
            'out_condition'=>json_encode($out_condition),
            //样式
            'style'=>json_encode(array(
                'width'=>$style_width,
                'height'=>$style_height,
                'color'=>$style_color,
                'icon'=>$style_icon,
            )),
           
        );
		db('flow_process')->where('id',$process_id)->update($data);
		return msg_return('发布成功！');
    }
    //返回给firame提交的表单
    public function return_iframe_ajax($ajax_return)
    {
        echo '<script type="text/javascript">parent.saveAttribute('.json_encode($ajax_return).');</script>';
        exit;
    } 
	private function get_db_column_comment($table_name = '', $field = true, $table_schema = ''){
		$database = config('database.');
		$table_schema = empty($table_schema) ? $database['database'] : $table_schema;
		$table_name = $database['prefix'] . $table_name;
		$fieldName = $field === true ? 'allField' : $field;
		$cacheKeyName = 'db_' . $table_schema . '_' . $table_name . '_' . $fieldName;
		$param = [
			$table_name,
			$table_schema
		];
		$columeName = '';
		if($field !== true){
			$param[] = $field;
			$columeName = "AND COLUMN_NAME = ?";
		}
		$res = Db::query("SELECT COLUMN_NAME as field,column_comment as comment FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND table_schema = ? $columeName", $param);
		$result=array(); 
		foreach($res as $k =>$value){
			foreach($value as $key=>$v){  
				if($value['comment'] !=''){
					$result[$value['field']]=$value['comment'];
				}
			}  
		}
		return count($result) == 1 ? reset($result) : $result;
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