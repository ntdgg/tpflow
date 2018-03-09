<?php
/**
 * Pms
 * 投标分层控制器
 * @2018年01月
 * @Gwb
 */
namespace app\index\event;

use think\Controller;
use think\Db;
use think\facade\Request;

class Tb extends Controller
{
	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
    public function datalist($map='',$field='',$order_by='id desc')
    {
		if($map==''){
			$map[] = ['is_del','eq',0];
		}
		$list = Db::name('tb')//使用数据库视图查询
                ->field($field)
                ->where($map)
                ->order($order_by)
				->paginate(config("ctrl.pagenum"));
        $this->assign('list',$list);
	}
	/*
	 * data 提交的数据 
	 */
	public function add($data)
	{
		$data['uid']=session('auth_id');
		$data['add_time']=time();
		$data['status'] = $this->do_check();
		$is_paper = config('tb.is_paper');
		if($is_paper == 0){
		 $t_zjtype = $data['t_zjtype'];
		 $t_zjname = $data['t_zjname'];
		 $t_zjbz = $data['t_zjbz'];
		 unset($data['t_zjtype']); 
		 unset($data['t_zjname']); 
		 unset($data['t_zjbz']); 
		}
		$id=Db::name('tb')->insertGetId($data);
		if($id){
			if($is_paper == 0){
			$this->tb_paper($t_zjtype,$t_zjname,$t_zjbz,$id);
			}
			$this->do_msg(0,$id);
			$this->tb_log($id,'新增投标');
			return ['code'=>0,'data'=>$id];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	public function tb_papers($data){
		$tb['status'] = $this->do_check(1);
		$rets = Db::name('tb')->where('id', $data['id'])->update($tb);
		$ret = $this->tb_paper($data['t_zjtype'],$data['t_zjname'],$data['t_zjbz'],$data['id']);
		if($ret){
			$this->do_msg(1,$data['id']);
			$this->tb_log($data['id'],'人员安排！');
			return ['code'=>0,'data'=>$data['id']];
		}else{
			return ['code'=>1,'data'=>'Db0001-写入数据库出错！'];
		}
	}
	
	private function tb_paper($t_zjtype,$t_zjname,$t_zjbz,$id){
		Db::name('tb_paper')->where('tid='.$id)->delete();
		for ($x=0; $x<count($t_zjtype); $x++) {
			if($t_zjtype[$x] != ''){
			$paper['tid']=$id;
			$paper['pid']=$t_zjtype[$x];
			$paper['name']=$t_zjname[$x];
			$paper['bz']=$t_zjbz[$x];
			$ret = Db::name('tb_paper')->insertGetId($paper);
			}
		}
		return $ret;
	}
	public function edit($data)
	{
		$is_paper = config('tb.is_paper');
		if($is_paper == 0){
		$t_zjtype = $data['t_zjtype'];
		$t_zjname = $data['t_zjname'];
		$t_zjbz = $data['t_zjbz'];
		 unset($data['t_zjtype']); 
		 unset($data['t_zjname']); 
		 unset($data['t_zjbz']); 
		}
		$data['status'] = $this->do_check();
		$ret = Db::name('tb')->where('id', $data['id'])->update($data);
		if($ret){
			if($is_paper == 0){
			$this->tb_paper($t_zjtype,$t_zjname,$t_zjbz,$data['id']);
			}
			$this->do_msg(0,$data['id']);
			$this->tb_log($data['id'],'修改投标');
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function check($data)
	{
		switch($data['s_status']){
			case 0 :
			$tb['status']=0;
			$con = '[退回修改]'.$data['s_yj'];
			break;
			case 1 :
			$tb['status']=1;
			$con = '[退回人员]'.$data['s_yj'];
			break;
			case 3 :
			$tb['is_success']=1;
			$tb['status'] = $this->do_check(2);
			$this->do_msg(2,$data['id']);
			$con = '[审批通过]'.$data['s_yj'];
			break;
		}
		$ret = Db::name('tb')->where('id', $data['id'])->update($tb);
		if($ret){
			$this->tb_log($data['id'],$con);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function tb_bzj($data)
	{
		$tb['status'] =4;
		$con = '[保证金]打款金额：'.$data['b_m'].'；打款时间：'.$data['b_t'];
		$ret = Db::name('tb')->where('id', $data['id'])->update($tb);
		if($ret){
			$this->tb_log($data['id'],$con);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function tb_ctrl($data)
	{
		if($data['val']=='is_success'){
			$tb['is_success'] = 1;
			$con = '恭喜中标！';
		}else{
			$tb['is_del'] =1;
			$con = '删除了投标！';
		}
		$ret = Db::name('tb')->where('id', $data['id'])->update($tb);
		if($ret){
			$this->tb_log($data['id'],$con);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function do_dengji($data)
	{
		$tb['status']=5;
		$con = '[标后分析]'.$data['tb_end'];
		$ret = Db::name('tb')->where('id', $data['id'])->update($tb);
		if($ret){
			$this->tb_log($data['id'],$con);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	public function do_yanqi($data)
	{
		$tb['t_ktime']=$data['t_ktime'];
		$con = '[投标延期]新开标时间：'.$data['t_ktime'];
		$ret = Db::name('tb')->where('id', $data['id'])->update($tb);
		if($ret){
			$this->tb_log($data['id'],$con);
			return ['code'=>0,'data'=>$ret];
		}else{
			return ['code'=>1,'data'=>'Db0002-更新数据库出错！'];
		}	
	}
	
	private function do_check($step='0'){
		$ctrl = config('tb.');
		switch($step){
			case 0 :
			if($ctrl['is_paper']==1){
				$status = 1;
			}elseif($ctrl['is_sh']==1){
				$status = 2;
			}elseif($ctrl['is_bzj']==1){
				$status = 3;
			}else{
				$status = 4;
			}
			break; 
			case 1 :
			if($ctrl['is_sh']==1){
				$status = 2;
			}elseif($ctrl['is_bzj']==1){
				$status = 3;
			}else{
				$status = 4;
			}
			break; 
			case 2 :
			if($ctrl['is_bzj']==1){
				$status = 3;
			}else{
				$status = 4;
			}
			break; 
		}
		return $status;
	}
	private function do_msg($step='0',$id){
		$ctrl = config('tb.');
		switch($step){
			case 0 :
			if($ctrl['is_paper']==1){
				msg_add($ctrl['paper'],'内部消息：投标人员安排','您好：您需要安排<u>&(tb,t_title)&</u>，请立即登入系统。','tb',$id);
			}elseif($ctrl['is_sh']==1){
				msg_add($ctrl['paper'],'内部消息：投标申请审核','您好：您需要审核<u>&(tb,t_title)&</u>，请立即登入系统。','tb',$id);
			}elseif($ctrl['is_bzj']==1){
				msg_add($ctrl['paper'],'内部消息：投标保证金打款提示','您好：您需要打款<u>&(tb,t_title)&</u>的保证金。，请立即登入系统。','tb',$id);
			}
			break; 
			case 1 :
			if($ctrl['is_sh']==1){
				msg_add($ctrl['paper'],'内部消息：投标申请审核','您好：您需要审核<u>&(tb,t_title)&</u>，请立即登入系统。','tb',$id);
			}elseif($ctrl['is_bzj']==1){
				msg_add($ctrl['paper'],'内部消息：投标保证金打款提示','您好：您需要打款<u>&(tb,t_title)&</u>的保证金。，请立即登入系统。','tb',$id);
			}
			break; 
			case 2 :
			if($ctrl['is_bzj']==1){
				msg_add($ctrl['paper'],'内部消息：投标保证金打款提示','您好：您需要打款<u>&(tb,t_title)&</u>的保证金。，请立即登入系统。','tb',$id);
			}
			break; 
		}
	}
	
	/*
	 * @tid id
	 * @log_con  记录内容
	 ****/
	Private function tb_log($tid,$log_con){
		$data=[
			'uid'=>session('auth_id'),
			'add_time'=>time(),
			'tid'=>$tid,
			'log_con'=>$log_con
		];
		Db::name('tb_log')->insertGetId($data);
	}
}
