<?php
/**
*+------------------
* 流信息处理
*+------------------ 
*/
namespace workflow;

use think\Db;
use think\facade\Session;

class FlowDb{
	/**
	 * 获取类别工作流
	 *
	 * @param $wf_type
	 */
	public static function getWorkflowByType($wf_type) 
	{
		$workflow = array ();
		if ($wf_type == '') {
			return $workflow;
		}
		$info = Db::name('flow')->where('is_del','eq',0)->where('status','eq',0)->where('type','eq',$wf_type)->select();
		return  $info;
	}
	/**
	 * 获取流程信息
	 *
	 * @param $fid
	 */
	public static function GetFlowInfo($fid)
	{
		if ($fid == '') {
			return false;
		}
		$info = Db::name('flow')->find($fid);		
		if($info){
			return  $info['flow_name'];
			}else{
			return  false;
		}
	}
	/**
	 * 判断工作流是否存在
	 *
	 * @param $wf_id
	 */
	public static function getWorkflow($wf_id) 
	{
		if ($wf_id == '') {
			return false;
		}
		$info = Db::name('flow')->find($wf_id);
		if($info){
			return  $info;
			}else{
			return  false;
		}
	}
	/**
	 * 获取步骤信息
	 *
	 * @param $id
	 */
	public static function getflowprocess($id) 
	{
		if ($id == '') {
			return false;
		}
		$info = Db::name('flow_process')->field('*')->find($id);
		if($data){
			return  $data;
			}else{
			return  false;
		}
	}
	/**
	 * API获取工作流列表
	 * API接口调用
	 */
	public static function GetFlow($info='')
	{
		if($info ==''){
			$list = Db::name('flow')->order('id desc')->where('is_del','0')->paginate('10');
			$list->each(function($item, $key){
				$item['edit'] = Db::name('run')->where('flow_id',$item['id'])->where('status','0')->value('id');
				return $item;
			});
		}else{
			$list = Db::name('flow')->find($info);
		}
		return $list;
	}
	/**
	 * API 新增工作流
	 * @param $data POST提交的数据
	 */
	public static function AddFlow($data)
	{
        $id = Db::name('flow')->insertGetId($data);
		if($id){
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	/**
	 * API 编辑工作流
	 * @param $data POST提交的数据
	 */
	public static function EditFlow($data)
	{
        $id = Db::name('flow')->update($data);
		if($id){
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	
	public static function ProcessAll($flow_id)
	{
		$list = Db::name('flow_process')->where('flow_id',$flow_id)->order('id asc')->select();
        $process_data = [];
        $process_total = 0;
        foreach($list as $value)
        {
            $process_total +=1;
            $style = json_decode($value['style'],true);
            $process_data[] = [
                'id'=>$value['id'],
                'flow_id'=>$value['flow_id'], 
                'process_name'=>$value['process_name'],
                'process_to'=>$value['process_to'],
                'style'=>'width:'.$style['width'].'px;height:'.$style['height'].'px;line-height:30px;color:'.$style['color'].';left:'.$value['setleft'].'px;top:'.$value['settop'].'px;',
            ];
        }
		return json_encode(['total'=>$process_total,'list'=>$process_data]);
	}
	
	public static function ProcessDel($flow_id,$process_id)
	{
        if($process_id<=0 or $flow_id<=0){
            return ['status'=>0,'msg'=>'操作不正确'];
        }
        $map = ['id'=>$process_id,'flow_id'=>$flow_id,'is_del'=>0];
        $process_model = Db::name('flow_process');
        $process_model->startTrans(); 
        $trans = $process_model->where($map)->delete();
        if(!$trans){
            $process_model->rollback();
            return ['status'=>0,'msg'=>'删除失败','info'=>''];
        }
        $list = Db::name('flow_process')->field('id,process_to')->where('flow_id',$flow_id)->where('is_del',0)->where('','exp',"FIND_IN_SET(".$process_id.",process_to)")->select();
		if(is_array($list)){
			foreach($list as $value){
				$arr = explode(',',$value['process_to']);
				$k = array_search($process_id,$arr);
				unset($arr[$k]);
				$process_to = '';
				if(!empty($arr)){
					$process_to = implode(',',$arr);
				}
				$data = ['process_to'=>$process_to,'updatetime'=>time()];
				$trans = Db::name('flow_process')->where('id',$value['id'])->update($data);
				if(!$trans){//有错误，跳出
					break;
				}
			}
        }
        if(!$trans){
            $process_model->rollback();
            return ['status'=>0,'msg'=>'删除失败，请重试','info'=>''];
        }
        $process_model->commit();
        return ['status'=>1,'msg'=>'删除成功','info'=>''];
	}
	
	public static function ProcessDelAll($flow_id)
	{
        $res = Db::name('flow_process')->where('flow_id',$flow_id)->delete();
		if($res){
			return ['status'=>1,'data'=>$res,'msg'=>'操作成功！'];
		}else{
			return ['status'=>0,'msg'=>'操作错误！'];
		}
	}
	public static function ProcessAdd($flow_id)
	{
		$process_count = Db::name('flow_process')->where('flow_id',$flow_id)->count();
        $process_type = 'is_step';
        if($process_count<=0)
            $process_type = 'is_one';
			$data = [
			   'flow_id'=>$flow_id, 
			   'process_type'=>$process_type,'style'=>json_encode(['width'=>'120','height'=>'38','color'=>'#0e76a8'])
			];
			$processid = Db::name('flow_process')->insertGetId($data);
			if($processid<=0){
				return ['status'=>0,'msg'=>'添加失败！','info'=>''];
			}else{
				return ['status'=>1,'msg'=>'添加成功！','info'=>''];
			}
	}
	
	public static function ProcessLink($flow_id,$process_info)
	{
		$one = self::GetFlow($flow_id);;
        if(!$one){
           return ['status'=>0,'msg'=>'未找到流程数据','info'=>''];
        }
		$process_info = json_decode(htmlspecialchars_decode(trim($process_info)),true);
        if($flow_id<=0 or !$process_info){
			return ['status'=>0,'msg'=>'参数有误，请重试','info'=>''];
        }
        foreach($process_info as $process_id=>$value){
            $datas = array(
                'setleft'=>(int)$value['left'],
                'settop'=>(int)$value['top'],
                'process_to'=>self::ids_parse($value['process_to']),
                'updatetime'=>time()
            );
            $ret =  Db::name('flow_process')->where('id','eq',$process_id)->where('flow_id','eq',$flow_id)->update($datas);
        }
		return ['status'=>1,'msg'=>'添加成功！','info'=>''];
	}
	public static function ids_parse($str,$dot_tmp=',')
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
	
	
	/**
	 * 获取表字段信息
	 * 
	 */
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
	
}