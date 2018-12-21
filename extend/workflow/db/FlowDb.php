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