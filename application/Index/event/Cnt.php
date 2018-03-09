<?php
/**
 * Pms
 * 合同分层控制器
 * @2018年01月
 * @Gwb
 */
namespace app\index\event;

use think\Controller;
use think\Db;
use think\facade\Request;

class Cnt extends Controller
{
	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
    public function datalist($map='',$field='',$order_by='id desc')
    {
		$list = Db::name('cnt')//使用数据库视图查询
                ->field($field)
                ->where($map)
                ->order($order_by)
				->paginate(config("ctrl.pagenum"))->each(function($item, $key){
			 $item['cnt_change'] = db('cnt_change')->where('cid',$item['id'])->select();
			return $item;
		});;
        $this->assign('list',$list);
	}
	/*
	 * table 表名，不含表前缀
	 * data 提交的数据 
	 */
	public function add($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$data['status'] = $this->do_check();
		$id=Db::name('cnt')->insertGetId($data);
		if($id){
			$this->do_msg(0,$id);
			$this->cnt_log($id,'新增合同');
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function edit($data)
	{
		$data['status'] = $this->do_check();
		$ret = Db::name('cnt')->where('id', $data['id'])->update($data);
		if($ret){
			$this->do_msg(0,$data['id']);
			$this->cnt_log($data['id'],'修改合同');
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function fadd($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$id=Db::name('cnt_file')->insertGetId($data);
		if($id){
			$this->cnt_log($data['hid'],'新增附件'.$data['f_title']);
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function oadd($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$data['status'] = $this->do_check();
		$id=Db::name('cnt_change')->insertGetId($data);
		if($id){
			$this->do_msg(0,$id,1);
			$this->cnt_log($id,'新增合同合同协议',1);
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function oedit($data)
	{
		$data['status'] = $this->do_check();
		$ret = Db::name('cnt_change')->where('id', $data['id'])->update($data);
		if($ret){
			$this->do_msg(0,$data['id'],1);
			$this->cnt_log($data['id'],'修改协议合同',1);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function check($data)
	{
		switch($data['s_status']){
			case 0 :
			$cnt['status']=0;
			$con = '[退回修改]'.$data['s_yj'];
			break;
			case 2 :
			$cnt['status'] = $this->do_check(1);
			if($data['type'] =='cnt_change'){
				$type = 1;
			}else{
				$type = 0;
			}
			$this->do_msg(1,$data['id'],$type);
			$con = '[审批通过]'.$data['s_yj'];
			break;
		}
		 $table =$data['type'];
		 unset($data['type']);
		$ret = Db::name($table)->where('id', $data['id'])->update($cnt);
		if($ret){
			if($table =='cnt_change'){
				$log = 1;
			}else{
				$log = 0;
			}
			$this->cnt_log($data['id'],$con,$log);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	
	public function do_gz($data)
	{
		$data['status'] = $this->do_check(2);
		$data['gz_time'] = date('Y-m-d H:i:S');
		$data['gz_uid'] = session('auth_id');
		$table =$data['type'];
		 unset($data['type']);
		$ret = Db::name($table)->where('id', $data['id'])->update($data);
		if($ret){
			if($table =='cnt_change'){
				$log = 1;
			}else{
				$log = 0;
			}
			$this->do_msg(2,$data['id'],$log);
			$this->cnt_log($data['id'],'盖章登记',$log);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	
	public function do_cd($data)
	{
		$data['status'] = 4;
		$data['cd_time'] = date('Y-m-d H:i:S');
		$data['cd_uid'] = session('auth_id');
		$table =$data['type'];
		 unset($data['type']);
		$ret = Db::name($table)->where('id', $data['id'])->update($data);
		if($ret){
			if($table =='cnt_change'){
				$log = 1;
			}else{
				$log = 0;
			}
			$this->cnt_log($data['id'],'存档登记',$log);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	
	private function do_check($step='0'){
		$ctrl = config('cnt.');
		switch($step){
			case 0 :
			if($ctrl['is_sh']==1){
				$status = 1;
			}elseif($ctrl['is_gz']==1){
				$status = 2;
			}elseif($ctrl['is_cd']==1){
				$status = 3;
			}else{
				$status = 4;
			}
			break; 
			case 1 :
			if($ctrl['is_gz']==1){
				$status = 2;
			}elseif($ctrl['is_cd']==1){
				$status = 3;
			}else{
				$status = 4;
			}
			break; 
			case 2 :
			if($ctrl['is_cd']==1){
				$status = 3;
			}else{
				$status = 4;
			}
			break; 
		}
		return $status;
	}
	private function do_msg($step='0',$id,$type='0'){
		$ctrl = config('cnt.');
		if($type=='0'){
			$table = 'cnt';
		}else{
			$table = 'cnt_change';
		}
		switch($step){
			case 0 :
			if($ctrl['is_sh']==1){
				msg_add($ctrl['sh'],'内部消息：合同审核','您好：您需要审核<u>&('.$table.',c_title)&</u>，请立即登入系统。',$table,$id);
			}elseif($ctrl['is_gz']==1){
				msg_add($ctrl['sh'],'内部消息：合同盖章','您好：您需要盖章<u>&('.$table.',c_title)&</u>，请立即登入系统。',$table,$id);
			}elseif($ctrl['is_cd']==1){
				msg_add($ctrl['sh'],'内部消息：合同存档','您好：您需要存档<u>&('.$table.',c_title)&</u>，请立即登入系统。',$table,$id);
			}
			break; 
			case 1 :
			if($ctrl['is_gz']==1){
				msg_add($ctrl['sh'],'内部消息：合同盖章','您好：您需要盖章<u>&('.$table.',c_title)&</u>，请立即登入系统。',$table,$id);
			}elseif($ctrl['is_cd']==1){
				msg_add($ctrl['sh'],'内部消息：合同存档','您好：您需要存档<u>&('.$table.',c_title)&</u>，请立即登入系统。',$table,$id);
			}
			break; 
			case 2 :
			if($ctrl['is_cd']==1){
				msg_add($ctrl['sh'],'内部消息：合同存档','您好：您需要存档<u>&('.$table.',c_title)&</u>，请立即登入系统。',$table,$id);
			}
			break; 
		}
	}
	
	/*
	 * @tid id
	 * @log_con  记录内容
	 ****/
	Private function cnt_log($cid,$log_con,$type=0){
		$data=[
			'uid'=>session('auth_id'),
			'add_time'=>time(),
			'tid'=>$cid,
			'log_con'=>$log_con,
			'type'=>$type
		];
		Db::name('cnt_log')->insertGetId($data);
	}
}
