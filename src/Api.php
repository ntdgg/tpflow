<?php
/**
 *+------------------
 * Tpflow 7.0 系统默认模板接口调用类
 *+------------------
 */
declare (strict_types = 1);

namespace tpflow;

define('BEASE_URL', realpath ( dirname ( __FILE__ ) ) );

define('Tpflow_Ver', '7.0.6' );
//引用适配器核心控制
use tpflow\service\Control;
//引用工具类
use tpflow\lib\unit;
use think\facade\Request;

	class Api{
	public function  __construct(){
		if(unit::getuserinfo()==-1){
            die('Access Error!');
		}
    }
	 /**
	  * Tpflow 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	  */
	 public function WfDo($act='index'){
		if($act=='start'){
			if (unit::is_post()) {
				$data = input('post.');
                return unit::return_msg(Control::WfCenter($act,input('wf_fid'),input('wf_type'),$data));
			 }else{
				 return Control::WfCenter($act,input('wf_fid'),input('wf_type'));
			 }
		}
		if($act=='endflow'||$act=='cancelflow'){
            return unit::return_msg(Control::WfCenter($act,'','',['bill_table'=>input('bill_table'),'bill_id'=>input('bill_id')]));
		}
		if($act=='do'){

			$wf_op = input('wf_op') ?? 'check';
			$ssing = input('ssing') ?? 'sing';
			$submit = input('submit') ?? 'ok';
			if (unit::is_post()) {
				$post = input('post.');
                return unit::return_msg(Control::WfCenter($act,input('wf_fid'),input('wf_type'),['wf_op'=>$wf_op,'ssing'=>$ssing,'submit'=>$submit],$post));
			 }else{
                return unit::return_msg(Control::WfCenter($act,input('wf_fid'),input('wf_type'),['wf_op'=>$wf_op,'ssing'=>$ssing,'submit'=>$submit]));
			 }
		}
        //通过ajax方式调用
        if($act=='ajax_do'){
            $wf_op = input('wf_op') ?? 'check';
            $ssing = input('ssing') ?? 'sing';
            $submit = input('submit') ?? 'ok';
            $data = Control::WfCenter($act,input('wf_fid'),input('wf_type'),['wf_op'=>$wf_op,'ssing'=>$ssing,'submit'=>$submit]);
            return unit::return_msg($data);
        }

        /*用户确认抄送*/
        if($act=='entCc'){
            return Control::WfCenter($act,input('id'));
        }
	}
	/**
	 * Tpflow 5.0统一接口设计器
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public function designapi($act){
		if($act=='welcome' ||$act=='check' || $act=='delAll' || $act=='wfdesc' || $act=='nodejson'){
            return unit::return_msg(Control::WfDescCenter($act,input('flow_id')));
		}
        if($act=='add'){
            return unit::return_msg(Control::WfDescCenter($act,input('flow_id'),input('data')));
        }
		if($act=='save'){
            return unit::return_msg(Control::WfDescCenter($act,input('flow_id'),input('process_info')));
		}
		if($act=='del' ||$act=='att'){
            return unit::return_msg(Control::WfDescCenter($act,input('flow_id'),input('id')));
		}
		if($act=='saveatt'){
            return unit::return_msg(Control::WfDescCenter($act,'',input('post.')));
		}
		if($act=='super_user'){
			return unit::return_msg(Control::WfDescCenter($act,'',['kid'=>input('kid'),'type_mode'=>input('type_mode'),'key'=>input('key'),'type'=>input('type')]));
		}
        if($act=='quilklink'){
            return unit::return_msg(Control::WfDescCenter($act,input('flow_id'),['process_id'=>input('process_id'),'fun'=>input('fun')]));
        }
	}
	/**
	 * Tpflow 5.0统一接口 流程管理
	 * @param string $act 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public function wfapi($act='index'){
		if($act=='index'||$act=='wfjk' ||$act=='verUpdate'){
			return Control::WfFlowCenter($act);
		}
		if($act=='wfdl'){
			return Control::WfEntrustCenter('index');
		}
		if($act=='event'){
			if (unit::is_post()) {
				$data = input('post.');
				return unit::return_msg(Control::WfFlowCenter($act,$data));
			}else{
				$data = input('id') ?? -1;
				return unit::return_msg(Control::WfFlowCenter($act,$data));
			}
		}
		if($act=='add'){
			if (unit::is_post()) {
				$data = input('post.');
                return unit::return_msg(Control::WfFlowCenter($act,$data));
			 }else{
                $data = input('id') ?? -1;
                return unit::return_msg(Control::WfFlowCenter($act,$data));
			 }
		}
        if($act=='view'){
            return unit::return_msg(Control::WfDescCenter($act,input('id')));
        }
        if($act=='del' || $act=='ver'){
            if (unit::is_post()) {
                $data = input('post.');
                return unit::return_msg(Control::WfFlowCenter($act,$data));
            }
        }
		if($act=='wfend'){
			 return Control::WfFlowCenter($act,input('id'));
		}
		if($act=='dladd'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::WfEntrustCenter('add',$data);
			 }else{
				 return Control::WfEntrustCenter('add',input('id'));
			 }
		}
	}
	/**
	 * Tpflow 5.0统一接口 数据接口
	 * @param string $act 调用接口方法
	 * @param int    $uid 用户id
	 * @param array  $map 查询方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	 public static function wfUserData($act='userFlow',$map=[],$field='',$order='',$group='',$page=1,$limit=20){
		return Control::wfUserData($act,$map,$field,$order,$group,$page,$limit);
	}
	 
	 
	/**
	 * Tpflow 5.0统一接口 前端权限控制中心
	 * @param string $act 调用接口方法
     * @param string $data 调用接口方法
	 * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
     * @return array 返回类型
	 */
	public static function wfAccess($act='log',$data=''){
		return Control::wfAccess($act,$data);
	}
    /**
     * Tpflow 7.0 我发起的流程
     */
    public static  function wfMysend($page=1,$limit=20,$map=[]){
        return Control::wfMysend($page,$limit,$map);
    }
    /**
     * Tpflow 7.0 我的会签数据
     */
    public static  function wfMysing($page=1,$limit=20,$map=[]){
        return Control::wfMysing($page,$limit,$map);
    }

}
	