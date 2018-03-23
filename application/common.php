<?php
/**
 * Pms
 * 公共方法
 * @2018年01月
 * @Gwb
 */

use think\facade\Session;
use think\Db;
use PHPMailer\PHPMailer\PHPMailer;

function ids_parse($str,$dot_tmp=',')
{
    if(!$str) return '';
    if(is_array($str))
    {
        $idarr = $str;
    }else
    {
        $idarr = explode(',',$str);
    }
    $idarr = array_unique($idarr);
	$dot = '';
	$idstr ='';
    foreach($idarr as $id)
    {
        $id = intval($id);
        if($id>0)
        {
            $idstr.=$dot.$id;
            $dot = $dot_tmp;
        }
    }
    if(!$idstr) $idstr=0;
    return $idstr;
}
// 数组保存到文件
function arr2file($filename, $arr='')
{
    if(is_array($arr)){
        $con = var_export($arr,true);
    } else{
        $con = $arr;
    }
    $con = "<?php\nreturn $con;\n?>";//\n!defined('IN_MP') && die();\nreturn $con;\n
    write_file($filename, $con);
}
//文件写入
function write_file($l1, $l2='')
{
    $dir = dirname($l1);
    if(!is_dir($dir)){
        mkdirss($dir);
    }
    return @file_put_contents($l1, $l2);
}
/**
 * 获取数据库字段注释
 */
function get_db_column_comment($table_name = '', $field = true, $table_schema = ''){
    // 接收参数
    $database = config('database.');
    $table_schema = empty($table_schema) ? $database['database'] : $table_schema;
    $table_name = $database['prefix'] . $table_name;
    
    // 缓存名称
    $fieldName = $field === true ? 'allField' : $field;
    $cacheKeyName = 'db_' . $table_schema . '_' . $table_name . '_' . $fieldName;
    
    // 处理参数
    $param = [
        $table_name,
        $table_schema
    ];
    
    // 字段
    $columeName = '';
    if($field !== true){
        $param[] = $field;
        $columeName = "AND COLUMN_NAME = ?";
    }
    
    // 查询结果
    $result = Db :: query("SELECT COLUMN_NAME as field,column_comment as comment FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND table_schema = ? $columeName", $param);
	
    // pp(Db :: getlastsql());
    if(empty($result) && $field !== true){
        return $table_name . '表' . $field . '字段不存在';
    }
    return count($result) == 1 ? reset($result) : $result;
}
//对象转化数组
function obj2arr($obj) 
{
    return json_decode(json_encode($obj),true);
}
/**
 * 系统邮件发送函数
 * @param string $tomail 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @author static7 <static7@qq.com>
 */
function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) 
{
    $mail = new PHPMailer();           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';          // 使用安全协议
    $mail->Host = config('msg.smtp'); // SMTP 服务器
    $mail->Port = config('msg.eport');                  // SMTP服务器的端口号
    $mail->Username = config('msg.euser');    // SMTP服务器用户名
    $mail->Password = config('msg.epwd');     // SMTP服务器密码
    $mail->SetFrom(config('msg.euser'), config('sys.name'));
    $replyEmail = '';                   //留空则为发件人EMAIL
    $replyName = '';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($tomail, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}
/**
 * ajax数据返回，规范格式
 */
function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
{
    $ret = ["code" => $code, "msg" => $msg, "data" => $data];
	$extend['opt'] = [
        'alert'    => $alert,
        'close'    => $close,
        'redirect' => $redirect,
        'url'      => $url,
    ];
    $ret = array_merge($ret, $extend);
    return Response::create($ret, 'json');
}

function pay_time($pay,$s,$e,$type){
	$hire = [];
	$zday = diffBetweenTwoDays($s,$e);
	switch ($type)
		{
		case 0://年
			$cs = ceil($zday/365);
			for ($x=0; $x<$cs; $x++) {
			  $hire[$x]['time']= date('Y-m-d',strtotime("+$x year",strtotime($pay)));
			} 
		break;
		case 1://季
			$cs = ceil($zday/120);
			for ($x=0; $x<$cs; $x++) {
			  $up = $x*4;
			  $hire[$x]['time']= date('Y-m-d',strtotime("+$up month",strtotime($pay)));
			} 
		break;
		case 2://月
			$cs = floor($zday/30);
			for ($x=0; $x<$cs; $x++) {
			  $hire[$x]['time']= date('Y-m-d',strtotime("+$x month",strtotime($pay)));
			} 
		break;
		}
	return $hire;
}

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day1
 * @param string $day2
 * @return number
 */
function diffBetweenTwoDays ($day1, $day2)
{
  $second1 = strtotime($day1);
  $second2 = strtotime($day2);
    
  if ($second1 < $second2) {
    $tmp = $second2;
    $second2 = $second1;
    $second1 = $tmp;
  }
  return ($second1 - $second2) / 86400;
}
/**
 * 节点遍历
 *
 * @param        $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int    $root
 *
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = [];
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            if ($data instanceof \think\Model) {
                $list[$key] = $data->toArray();
            }
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            if (!isset($list[$key][$child])) {
                $list[$key][$child] = [];
            }
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }

    return $tree;
}
/**
 * 多维数组合并（支持多数组）
 * @return array
 */
function array_merge_multi()
{
    $args = func_get_args();
    $array = [];
    foreach ($args as $arg) {
        if (is_array($arg)) {
            foreach ($arg as $k => $v) {
                if (is_array($v)) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : [];
                    $array[$k] = array_merge_multi($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }

    return $array;
}

/******/
function get_commonval($table,$id,$val)
{
	return Db($table)->where('id',$id)->value($val);
}

/**
 * get_rolename 获取角色名
 */
function get_rolename($roleid)
{
	return Db('role')->where('id',$roleid)->value('name');
}
/**
 * get_username 获取角色名
 */
function get_username($uid)
{
	return Db('user')->where('id',$uid)->value('username');
}
/**
 * get_username 获取角色名
 */
function get_noticetype($id)
{
	return Db('notice_type')->where('id',$id)->value('type');
}
/*
 * 字符串转换成数组
 **/
function strtoarr($str,$val)
{
	return explode($val,$str);
}
/*
 * 获取通知对象
 * type 0(用户组) 1（返回通知对象消息信息）
 */
function get_noticeuser($id,$type = '0')
{
	if($type==0){
		$str =  Db('notice')->where('id',$id)->value('new_user');
		$info =  Db('role')->where('id','in',$str)->field('name')->select();
		foreach($info as $k=>$v){
			$role[] = $v['name'];
		}
		$info = implode(',',$role);
	}
	return $info;
}
/*
 * 获取通知对象
 * type 0(用户组) 1（返回通知对象消息信息）
 */
function get_hire_money($id,$type = '0')
{
	if($type==0){
		$str =  Db('notice')->where('id',$id)->value('new_user');
		$info =  Db('role')->where('id','in',$str)->field('name')->select();
		foreach($info as $k=>$v){
			$role[] = $v['name'];
		}
		$info = implode(',',$role);
	}
	return $info;
}

function get_paper_loan($pid){
		$pidarr = explode('|',$pid);
			$html ='';
			for($x=0;$x<count($pidarr);$x++){
				$p_name = db('paper')->where('id',$pidarr[$x])->value('p_name');
				$html .= ''.$p_name.'|';
			}
		return $html;
}
function get_log($table,$id,$type=''){
	if($type==''){
		$log =  Db($table)->where('tid',$id)->select();
	}else{
		$log =  Db($table)->where('tid',$id)->where('type',$type)->select();
	}
	
	$html ='<table   id="tb" align="center" class="table table-border table-bordered table-bg mt-5">
		 <tr width="100%" align="center">
		 <th width="30%">操作时间</th><th width="20%">操作人</th><th width="30%">操作内容</th></tr>';
	for($x=0;$x<count($log);$x++){
				
				$html .= '<th width="10%">'. date('Y-m-d h:i:s',$log[$x]['add_time']).'</th><th width="20%">'.get_username($log[$x]['uid']).'</th><th width="30%">'.$log[$x]['log_con'].'</th></tr>';
	}
	$html .='</table>';
	return $html;
}
function get_state($table,$id,$type=''){
	$log =  Db($table)->where('tid',$id)->order('id desc')->limit(1)->select();
	$html ='<h6>'.$log[0]['log_con'].'<br/>'. date('m-d h:i',$log[0]['add_time']).'</h6>';
	return $html;
}
function get_paper_last_log($id)
{
	$row =  Db('paper')->where('id',$id)->find();
	if($row['is_wj']=='1'){
		$jz = Db('paper_loan')->where('status','0')->where('id',$row['is_wj_id'])->order('id desc')->find();
		$html ='当前状态：<font color="red">证件外借</font><br/>
				借证人：'.$jz['c_jzname'].'<br/>约定归还:'.$jz['c_jzgh'].'
		';
	}else{
		$html ='当前状态：<font color="red">证件在库</font>';
	}
	if($row['is_zj']=='1'){
		$zj = Db('paper_xm')->where('id',$row['is_zj_id'])->find();
		$html .='<br/>证件在建：<font color="red">证件在建</font><br/>
				在建项目：'.get_commonval('cnt',$zj['c_xmid'],'c_title').'<br/>备案时间:'.$zj['c_xmstime'].'
		';
	}
	
	return $html;
}
function msg_add($uid,$title,$msg,$table,$tid)
{
	if(strstr($uid,",")){
		$uidarry = explode(",",$uid);
		for ($x=0; $x<count($uidarry); $x++) {
			  $data = [
						'uid'=>$uidarry[$x],
						'add_time'=>time(),
						'title'=>$title,
						'msg'=>$msg,
						'table'=>$table,
						'tid'=>$tid
					];
				Db('msg')->insertGetId($data);
				$mail = Db('user')->where('id',$uidarry[$x])->value('mail');
				if($mail !=''){
					send_mail($mail,$mail,$title,$msg);
				}
				
			} 
		
		}else{
		$data = [
			'uid'=>$uid,
			'add_time'=>time(),
			'title'=>$title,
			'msg'=>$msg,
			'table'=>$table,
			'tid'=>$tid
		];
		Db('msg')->insertGetId($data);
		$mail = Db('user')->where('id',$uid)->value('mail');
		if($mail !=''){
					send_mail($mail,$mail,$title,$msg);
		}
	}
}