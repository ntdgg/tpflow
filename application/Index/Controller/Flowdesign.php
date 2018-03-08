<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;

class Flowdesign extends Controller {
    
	public $_obj_model='';
    protected function model()
    {
        if($this->_obj_model)
            return $this->_obj_model;
        return $this->_obj_model = db('flow');
    }
    
    public function index(){
        $flow_id = intval(input('flow_id'));

        if($flow_id<=0)
        {
            $this->error('参数有误，请返回重试!');
        }
        $map = array(
            'id'=>$flow_id,
            'is_del'=>0,
        );
        $one = $this->model()->where($map)->find();
        if(!$one)
        {
            $this->error('未找到数据，请返回重试!');
        }
        
        //获取步骤
        $map = array(
            'flow_id'=>$flow_id,
            'is_del'=>0,
        );
        $list = db('flow_process')->where($map)->order('id asc')->select();
        //改变步骤格式，适应 js
        $process_data = array();
        $process_total = 0;
        foreach($list as $value)
        {
            $process_total +=1;
            $style = json_decode($value['style'],true);
            $process_data[] = array(
                'id'=>$value['id'],
                'flow_id'=>$value['flow_id'], 
                'process_name'=>$value['process_name'],
                'process_to'=>$value['process_to'],
                'icon'=>$style['icon'],//图标
                'style'=>'width:'.$style['width'].'px;height:'.$style['height'].'px;line-height:'.$style['height'].'px;color:'.$style['color'].';left:'.$value['setleft'].'px;top:'.$value['settop'].'px;',
            );
        }
        //传到模板渲染
        $this->assign('one', $one);
        $this->assign('process_data', json_encode(array(
                                                        'total'=>$process_total,
                                                        'list'=>$process_data
                                                        )));
        //显示页面
        return $this->fetch();
       
    }
    
    //添加步骤
    public function add_process()
    {
        //获取参数
        $flow_id = intval(I('post.flow_id'));
        $left  = intval(I('post.left'));
        $top  = intval(I('post.top'));
        //查找流程是否存在
        $map = array(
            'id'=>$flow_id,
            'is_del'=>0,
        );
        $one = $this->model()->where($map)->find();
        if(!$one)
        {
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'添加失败,未找到流程',
                'info'=>'',
            ));
        }
        //是否有步骤  设为第一步
        $map = array(
            'flow_id'=>$flow_id,
            'is_del'=>0,
        );
        $process_count = D('flow_process')->where($map)->count();
        
        $process_type = 'is_step';//正常步骤
        if($process_count<=0)
            $process_type = 'is_one';//设为第一步
        $data = array(
           'flow_id'=>$flow_id, 
           'process_type'=>$process_type,
           'process_name'=>'新建步骤',
           'setleft'=>$left,
           'settop'=>$top,
           'process_to'=>'',
           'style'=>json_encode(array(
                'icon'=>'icon-star',//图标
                'width'=>'120',
                'height'=>'30',
                'color'=>'#0e76a8',
           )),
           //默认值 
           'child_after'=>1,//子流程结束后动作  默认 1 同时结束父流程    2返回父流程步骤
           'auto_unlock'=>1,//权限：允许更改
        );
        $id = D('flow_process')->add($data);

        if($id<=0)
        {
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'添加失败',
                'info'=>'',
            ));
        }
        //返回 json 数据
        $this->ajaxReturn(array(
            'status'=>1,
            'msg'=>'success',
            'info'=>array(
                'id'=>$id,
                'flow_id'=>$flow_id, 
                'process_name'=>'新建步骤'.$max_step,
                'process_to'=>'',
                'icon'=>'',//图标
                'style'=>'left:'.$left.'px;top:'.$top.'px;color:#0e76a8;'//样式 
            ),
        ));

    }
    
    //删除步骤
    function delete_process()
    {
        $process_id = intval(I('post.process_id'));
        $flow_id = intval(I('post.flow_id'));
        
        if($process_id<=0 or $flow_id<=0)
        {
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'操作不正确',
                'info'=>'',
            ));
        }
        
        $map = array(
            'id'=>$process_id,
            'flow_id'=>$flow_id,
            'is_del'=>0,
        );
        $data = array(
            'updatetime'=>$this->_timestamp,
            'is_del'=>1,
        );
        
        $process_model = D('flow_process');
        
        //开启数据库事务 , 确保整个操作 全部正常 才删除成功  
        $process_model->startTrans(); 
        
        $trans = $process_model->where($map)->save($data);
        if(!$trans)
        {
            $process_model->rollback();
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'删除失败',
                'info'=>'',
            ));
        }
        
        //start  删除成功后，会重新保存设计，此步可省略
        // 修改 同流程中与$process_id有关的 process_to
        $map = array(
            'flow_id'=>$flow_id,
            'is_del'=>0,
            '_string'=>"FIND_IN_SET('".$process_id."',process_to)",
        );
        $list = $process_model->field('id,process_to')->where($map)->select();
        foreach($list as $value)
        {
            //把 process_to 去除 $process_id 再保存
            $arr = explode(',',$value['process_to']);
            $k = array_search($process_id,$arr);
            unset($arr[$k]);
            
            $process_to = '';
            if(!empty($arr))
            {
                $process_to = implode(',',$arr);
            }

            $map =array(
                'id'=>$value['id'],
            );
            $data = array(
                'process_to'=>$process_to,
                'updatetime'=>$this->_timestamp,
            );
            $trans = $process_model->where($map)->save($data);
            if(!$trans)//有错误，跳出
            {
                break;
            }
        }
        
        //end  删除成功后，会重新保存设计，此步可省略
        
        
        //有错误 回滚
        if(!$trans)
        {
            $process_model->rollback();
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'删除失败，请重试',
                'info'=>'',
            ));
        }
        
        $process_model->commit();
        $this->ajaxReturn(array(
            'status'=>1,
            'msg'=>'删除成功',
            'info'=>'',
        ));
        
    }
    
    
    /* 保存布局  位置 和 步骤连接*/
    public function save_canvas()
    {
        $flow_id = intval(I('post.flow_id'));
        $process_info = trim($_POST['process_info']);
        $process_info = json_decode($process_info,true);

        
        if($flow_id<=0 or !$process_info)
        {
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'参数有误，请重试',
                'info'=>'',
            ));
        }
        
        //检查流程
        $map = array(
            'id'=>$flow_id,
            'is_del'=>0,
        );
        $one = D('flow')->field('id')->where($map)->find();
        if(!$one)
        {
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'未找到流程数据',
                'info'=>'',
            ));
        }
        //保存数据
        $process_model = D('flow_process');
        foreach($process_info as $process_id=>$value)
        {
            $map = array(
                'id'=>$process_id,
                'flow_id'=>$flow_id,
                'is_del'=>0,
            );
            $data = array(
                'setleft'=>(int)$value['left'],
                'settop'=>(int)$value['top'],
                'process_to'=>ids_parse($value['process_to']),
                'updatetime'=>$this->_timestamp
            );
            $process_model->where($map)->save($data);
        }
        
        $this->ajaxReturn(array(
                'status'=>1,
                'msg'=>'^_^ 保存成功',
                'info'=>'',
            ));
    }
    
    
    //右键属性
    public function attribute()
    {
        //步骤ID
        $process_id = intval(I('get.id'));
        $op = trim(I('get.op'));
        if(!$op)
            $op = 'basic';
        
        //连接数据表用的。表 model 
        $flow_model = D('flow');
        $process_model = D('flow_process');
        $form_model = D('form');
        
        //map查询条件  验证步骤
        $map = array(
            'id'=>$process_id,
            'is_del'=>0,
        );
        $one = $process_model->where($map)->find();
        if(!$one)
        {
            exit('T_T 未找到步骤信息');
        }
        //验证流程
        $map = array(
            'id'=>$one['flow_id'],//流程ID
            'is_del'=>0,
        );
        
        $flow_one = $flow_model->where($map)->find();
        if(!$flow_one)
        {
            exit('T_T 未找到流程信息');
        }
        
        if($flow_one['flow_type']==1)
        {
            exit('^_^ 亲，自由流程不用设置步骤的喔');
        }
        
        //验证 表单    $flow_one['form_id']
        $map = array(
            'id'=>$flow_one['form_id'],
            'is_del'=>0,
        );
        $form_one = $form_model->field('id,form_name,content_data,fields')->where($map)->find();
        if(!$form_one)
        {
            exit('T_T 未找到表单信息');
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
        //$one['lock_fields'] = $one['lock_fields']=='' ? array() : explode(',',$one['lock_fields']);//锁定 字段
        
        
        $form_one['content_data'] = $form_one['content_data']=='' ? array() : unserialize($form_one['content_data']);
        
        $one['out_condition'] = self::parse_out_condition($one['out_condition'],$form_one['content_data']);//json

        
        /* 设计表 你可以提示一下
        if(!$form_one['content_data'])
        {
            exit('T_T 请先设计表单');
        }*/
        //print_R($form_one['content_data']);exit;
        
        
        
        
        
        
        //备选步骤  同一个流程全部步骤
        $map = array(
            'flow_id'=>$one['flow_id'],//流程ID
            //'id'=>array('neq',$one['id']),//不用排除当前步骤ID    子流程结束后 返回步骤  要用
            'is_del'=>0,
        );
        $process_to_list = $process_model->field('id,process_name,process_type')->where($map)->select();

        
        //子流程 列表
        $map = array(
            'is_del'=>0,
        );
        $child_flow_list = $flow_model->field('id,flow_name')->where($map)->select();
        
        
        
        
        
        
        //赋值到模板上
        $this->assign('op',$op);
        $this->assign('one',$one);
        $this->assign('form_one',$form_one);
        //这个在配置文件中配置，\Application\Common\Conf\config.php
        $this->assign('form_plugins',C('FORM_PLUGINS'));
        
        $this->assign('process_to_list',$process_to_list);
        $this->assign('child_flow_list',$child_flow_list);
        
        //渲染显示模板
        $this->display();
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
        
        /*
        $flow_id  = intval($_POST['flow_id']);
        $process_id  = intval($_POST['process_id']);
        
        $arr = array(
            //步骤ID => desc 不符合条件时的提示   option 显示文本   value 值 
            '59'=>array(
                'condition_desc'=>'不符合条件时的提示',
                'condition'=>"<option value=\"'data_1' = '33'  AND\">'爱好' = '33' AND</option><option value=\"'data_2' = '44'\">'姓名' = '44'</option>"
            ),
        );
        echo json_encode($arr);
        */
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
    
    //右键属性 iframe 提交保存
    public function save_attribute()
    {
        //print_R($_POST);exit;
        //start 避免出错，都列出来先
        $flow_id = intval(I('post.flow_id'));//流程ID
		$process_id = intval(I('post.process_id'));//步骤ID
        //常规
        $process_name = trim(I('post.process_name'));//步骤名称
        $process_type = trim(I('post.process_type'));//类型
        $process_to = ids_parse(I('post.process_to'));//下一步
        $child_id = intval(I('post.child_id'));//子流程ID
        $child_after = intval(I('post.child_after'));//子流程结束后动作
        $child_back_process = intval(I('post.child_back_process'));//结束返回
        //表单
        
        $write_fields = I('post.write_fields','') ? implode(',',I('post.write_fields','')) :'';//可写字段
        $secret_fields = I('post.secret_fields','') ? implode(',',I('post.secret_fields','')) :'';//保密字段
        //权限
		$auto_person = intval(I('post.auto_person'));//自动选人
		$auto_unlock = intval(I('post.auto_unlock'));//>预先设置自动选人，更方便转交工作
		$auto_sponsor_ids = trim(I('post.auto_sponsor_ids'));//指定主办人
        $auto_sponsor_text = trim(I('post.auto_sponsor_text'));
        $auto_respon_ids = trim(I('post.auto_respon_ids'));//指定经办人
        $auto_respon_text = trim(I('post.auto_respon_text'));
        $auto_role_ids = trim(I('post.auto_role_ids'));//指定角色
        $auto_role_text = trim(I('post.auto_role_text'));
        $range_user_ids = trim(I('post.range_user_ids'));//授权人员
        $range_user_text = trim(I('post.range_user_text'));
        $range_dept_ids = trim(I('post.range_dept_ids'));//授权部门
        $range_dept_text = trim(I('post.range_dept_text'));
        $range_role_ids = trim(I('post.range_role_ids'));//授权角色
        $range_role_text = trim(I('post.range_role_text'));
        //操作
        $receive_type = intval(I('post.receive_type'));//交接方式
        $is_user_end = intval(I('post.is_user_end'));//允许主办人办结流程
        $is_userop_pass = intval(I('post.is_userop_pass'));//经办人可以转交下一步
        $is_sing = intval(I('post.is_sing'));//会签方式
        $sign_look = intval(I('post.sign_look'));//可见性
        $is_back = intval(I('post.is_back'));//回退方式
        //转出条件
       $process_condition =  trim(I('post.process_condition'),',');//process_to
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
       $style_width = intval(I('post.style_width'));
       $style_height = intval(I('post.style_height'));
       $style_color = trim(I('post.style_color'));
       $style_icon = trim(I('post.style_icon'));
//end 避免出错，都列出来先
       
       $process_model = D('flow_process');
       //对数据进行判断
       if($flow_id<=0 or $process_id<=0)
       {
           self::return_iframe_ajax(array(
                'status'=>0,
                'msg'=>'保存失败',
                'info'=>'',
            ));
       }
       
       //检查步骤是否存在
        $map = array(
            'id'=>$process_id,
            'flow_id'=>$flow_id,
            'is_del'=>0,
        );
        $process_one = $process_model->where($map)->find();
        if(!$process_one){
            self::return_iframe_ajax(array(
                'status'=>0,
                'msg'=>'未找到步骤，请刷新再试',
                'info'=>'',
            ));
        }
        
        //保存数据， 不列出来的，直接写这里也可以呀
        $data = array(
            //常规
            'process_name'=>$process_name,
            'process_type'=>$process_type,
            'process_to'=>$process_to,
            'child_id'=>$child_id,
            'child_after'=>$child_after,
            'child_back_process'=>$child_back_process,
            //表单
            'write_fields'=>$write_fields,
            'secret_fields'=>$secret_fields,
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
//print_r($data);exit;
		$map = array(
			'id'=>$process_id,
			'is_del'=>0,
		);
		$process_model->where($map)->save($data);
        
        
        
        //成功返回
        self::return_iframe_ajax(array(
            'status'=>1,
            'msg'=>'保存成功',
            'info'=>'',
        ));
    }
    //返回给firame提交的表单
    public function return_iframe_ajax($ajax_return)
    {
        //返回格式
        /*
        $ajax_return = array(
            'status'=>1,
            'msg'=>'保存成功',
            'info'=>'',
        );*/
        //回调页面的函数
        echo '<script type="text/javascript">parent.saveAttribute('.json_encode($ajax_return).');</script>';
        exit;
    }


    
    
   
}