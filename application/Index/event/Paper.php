<?php
/**
 * Pms
 * 证件分层控制器
 * @2018年01月
 * @Gwb
 */
namespace app\index\event;

use think\Controller;
use think\Db;
use think\facade\Request;

class Paper extends Controller
{
	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
    public function datalist($map='',$field='',$order_by='id desc')
    {
		$list = Db::name('view_paper')//使用数据库视图查询
                ->field($field)
                ->where($map)
                ->order($order_by)
				->paginate(config("ctrl.pagenum"))->each(function($item, $key){
					$item['paper_d'] = db('paper_d')->where('pid',$item['id'])->select();
					return $item;
				});
        $this->assign('list',$list);
	}
	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
    public function loan($map='',$field='',$order_by='id desc')
    {
		$list = Db::name('paper_loan')//使用数据库视图查询
                ->field($field)
                ->where($map)
                ->order($order_by)
				->paginate(config("ctrl.pagenum"));
        $this->assign('list',$list);
	}
	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
    public function hire($map='',$field='',$order_by='id desc')
    {
		$list = Db::name('paper_hire as h')//使用数据库视图查询
                ->field($field)
                ->where($map)
                ->order($order_by)
				->paginate(config("ctrl.pagenum"))->each(function($item, $key){
					$m = Db::name('paper_hire_money')->where('pid',$item['id'])->where('status',0)->limit(1)->order('d_time')->select();
					if(!empty($m)){
						$item['m'] = '下次付款时间：'.$m[0]['d_time'];
						}else{
						$item['m'] = '结算完毕！';
					}
					return $item;
				});
        $this->assign('list',$list);
	}
	public function zj($map='',$field='',$order_by='id desc')
    {
		$list = Db::name('paper_xm')//使用数据库视图查询
                ->field($field)
                ->where($map)
                ->order($order_by)
				->paginate(config("ctrl.pagenum"));
        $this->assign('list',$list);
	}//paper_zjt
	public function paper_zjt($map='',$field='',$order_by='id desc')
    {
		$list = Db::name('paper_zjt')//使用数据库视图查询
                ->field($field)
                ->where($map)
                ->order($order_by)
				->paginate(config("ctrl.pagenum"));
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
		if($data['p_user_type']==1){
			$data['is_py']=1;
		}
		$id=Db::name('Paper')->insertGetId($data);
		if($id){
			$this->paper_log($id,0,'新增证件');
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function hire_add($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$time = $data['d_time'];
		$money = $data['d_money'];
		 unset($data['d_time']); 
		 unset($data['d_money']); 
		
		$id=Db::name('paper_hire')->insertGetId($data);
		if($id){
			$this->hire_money($time,$money,$id);
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function loan_add($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$pid = $data['pid'];
		$data['pid']=implode("|",$data['pid']);
		$id=Db::name('paper_loan')->insertGetId($data);
		if($id){
			for ($x=0; $x<count($pid); $x++) {
				  $up = ['is_wj_id'=>$id,'is_wj'=>'1','id'=>$pid[$x],'up_time'=>time()];
				  $ret = $this->up('paper',$up,3,'证件外借',0);
			}
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function loan_gh($data)
	{
		$data['upuid']=session('auth_id');
		$data['up_time']=time();
		$data['status']=1;
		$id = input('id','0','intval');
		$info = db('paper_loan')->find($id);
		$pid = explode('|',$info['pid']);
		$id=Db::name('paper_loan')->where('id',$id)->update($data);
		if($id){
			for ($x=0; $x<count($pid); $x++) {
				  $up = ['is_wj_id'=>'','is_wj'=>'0','id'=>$pid[$x],'up_time'=>time()];
				  $ret = $this->up('paper',$up,3,'证件归还',0);
			}
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function hire_edit($data)
	{
		$time = $data['d_time'];
		$money = $data['d_money'];
		 unset($data['d_time']); 
		 unset($data['d_money']); 
		$ret=Db::name('paper_hire')->where('id', $data['id'])->update($data);
		if($ret){
			Db::name('paper_hire_money')->where('pid', $data['id'])->delete();
			$this->hire_money($time,$money,$data['id']);
			return ['code'=>0,'data'=>$data['id']];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function hire_pay($data)
	{
		$data['up_time']=time();
		$data['status']=1;
		$data['uid'] = session('auth_id');
		$ret=Db::name('paper_hire_money')->where('id', $data['id'])->update($data);
		if($ret){
			return ['code'=>0,'data'=>$data['id']];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	
	private function hire_money($d_time,$d_money,$pid){
		for ($x=0; $x<count($d_time); $x++) {
			$money['pid']=$pid;
			$money['d_money']=$d_money[$x];
			$money['d_time']=$d_time[$x];
			$id=Db::name('paper_hire_money')->insertGetId($money);
		}
	}
	public function zj_wg($data)
	{
		$data['c_xmetime']=date('Y-m-d');
		$data['status']=1;
		$id = input('id','0','intval');
		$info = db('paper_xm')->find($id);
		$res=Db::name('paper_xm')->where('id',$id)->update($data);
		if($res){
				  $up = ['is_zj_id'=>'','is_zj'=>'0','id'=>$info['pid'],'up_time'=>time()];
				  $ret = $this->up('paper',$up,3,'项目完工',0);
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	
	public function zadd($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$id=Db::name('Paper_d')->insertGetId($data);
		if($id){
			$this->paper_log($id,0,'新增证件专业',1);
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function zedit($data)
	{
		$ret = Db::name('Paper_d')->where('id', $data['id'])->update($data);
		if($ret){
			$this->paper_log($data['id'],1,'修改证件',1);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function edit($data)
	{
		$ret = Db::name('Paper')->where('id', $data['id'])->update($data);
		if($ret){
			$this->paper_log($data['id'],1,'修改证件');
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	Private function up($table,$data,$log_type,$con,$type)
	{
		$ret = Db::name($table)->where('id', $data['id'])->update($data);
		if($ret){
			$this->paper_log($data['id'],$log_type,$con,$type);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function change($data)
	{
		$table =$data['type'];
		if($table =='paper'){
			$table_type ='0';
		}else{
			$table_type ='1';
		}
		switch ($data['ctype'])
		{
		case 1://入库（证件归入系统）
		   $up = ['is_zj'=>'0','is_wj'=>'0','id'=>$data['pid'],'up_time'=>time()];
		   $ret = $this->up($table,$up,3,'证件入库',$table_type);
		   if($ret['code']==0){
			   
			  $paper_loan['upuid']=session('auth_id');
			  $paper_loan['up_time']=time();
		      $paper_loan['status']=1;

			  $loan_id=Db::name($table)->where('id',$data['pid'])->value('is_wj_id'); 
			  
			  Db::name('paper_loan')->where('id',$loan_id)->update($paper_loan); 
			   
			  $change = ['type'=>$table_type,'pid'=>$data['pid'],'change'=>$data['ctype'],'cid'=>0,'con'=>$data['c_rktime'].'|'.$data['c_rkbz']];
			  $this->paper_change($change);
			}
			break;
		case 2://在建（证件备案项目）is_zj
		   $up = ['is_zj'=>'1','id'=>$data['pid'],'up_time'=>time()];
		   $ret = $this->up($table,$up,3,'项目在建操作',$table_type);
		   if($ret['code']==0){
			 //项目建库
				$xm = ['type'=>$table_type,'pid'=>$data['pid'],'c_xmid'=>$data['c_xmid'],'c_xmzw'=>$data['c_xmzw'],'c_xmstime'=>$data['c_xmstime']];
				$xmid = $this->paper_xm($xm);//
				$xmzj = ['is_zj_id'=>$xmid,'id'=>$data['pid']];
				Db::name($table)->where('id',$data['pid'])->update($xmzj); 
				//记录下证件变更记录
				$change = ['type'=>$table_type,'pid'=>$data['pid'],'change'=>$data['ctype'],'cid'=>$xmid,'con'=>'项目在建操作'];
				$this->paper_change($change);
			}else{
			   
		   }
			break;
		case 3://外借（证件临时外借）is_wj
		   $up = ['is_wj'=>'1','id'=>$data['pid'],'up_time'=>time()];
		   $ret = $this->up($table,$up,3,'证件外借',$table_type);
		   if($ret['code']==0){
			   //证件外借库
				$loan = ['type'=>$table_type,'pid'=>$data['pid'],'c_jzname'=>$data['c_jzname'],'c_jztel'=>$data['c_jztel'],'c_jztime'=>$data['c_jztime'],'c_jzgh'=>$data['c_jzgh'],'c_jzbz'=>$data['c_jzbz']];
				$loanid = $this->paper_loan($loan);
				
				$wj_up = ['is_wj_id'=>$loanid,'id'=>$data['pid']];
				Db::name($table)->where('id',$data['pid'])->update($wj_up); 
				
				//记录下证件变更记录
				$change = ['type'=>$table_type,'pid'=>$data['pid'],'change'=>$data['ctype'],'cid'=>$loanid,'con'=>$data['c_jzname'].'--借了证件'];
				$this->paper_change($change);
			}else{
			   
		   }
			break;
		case 4://转出（证件转出公司）is_zc is_del
		   $up = ['is_del'=>'1','is_zc'=>'1','id'=>$data['pid'],'up_time'=>time()];
		   $ret = $this->up($table,$up,3,'证件转出',$table_type);
		   if($ret['code']==0){
				//记录下证件变更记录
				$change = ['type'=>$table_type,'pid'=>$data['pid'],'change'=>$data['ctype'],'cid'=>0,'con'=>'证件转出'];
				$this->paper_change($change);
			}else{
			   
		   }
			break;
		case 5://延期（证件延期记录）
		   $up = ['p_etime'=>$data['c_yqetime'],'id'=>$data['pid'],'up_time'=>time()];
		   $ret = $this->up($table,$up,3,'证件延期',$table_type);
		   if($ret['code']==0){
				//记录下证件变更记录
				$change = ['type'=>$table_type,'pid'=>$data['pid'],'change'=>$data['ctype'],'cid'=>0,'con'=>$data['c_yqbz']];
				$this->paper_change($change);
			}else{
			   
		   }
			break;
		}
		
	}
	/*
	 * @pid 证件id
	 * @log_type 日志类别 0:新增 1:修改 2:删除 3:变更 4:新增专业 5:借证 
	 * @log_con  记录内容
	 * @type 类型 0 paper主表 1 paper_d附表
	 ****/
	Private function paper_log($pid,$log_type,$log_con,$type = '0'){
		$data=[
			'uid'=>session('auth_id'),
			'add_time'=>time(),
			'pid'=>$pid,
			'paper_type'=>$type,
			'log_type'=>$log_type,
			'log_con'=>$log_con
		];
		Db::name('paper_log')->insertGetId($data);
	}
	
	private function paper_loan($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		return Db::name('paper_loan')->insertGetId($data);
	}
	
	Private function paper_xm($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		return Db::name('paper_xm')->insertGetId($data);
	}
	
	Private function paper_change($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$id = Db::name('paper_change')->insertGetId($data);
		return $id;
	}
}
