<?php /*a:2:{s:49:"D:\tpflow\application\index\view\index\index.html";i:1522407879;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
﻿<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="Bookmark" href="/favicon.ico" >
<link rel="Shortcut Icon" href="/favicon.ico" />
<!--[if lt IE 9]>
<script type="text/javascript" src="lib/html5shiv.js"></script>
<script type="text/javascript" src="lib/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/static/lib/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/skin/blue/skin.css" id="blue" />
<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/style.css" />
<link rel="stylesheet" type="text/css" href="/static/h-ui.admin/common.css" />
<!--[if IE 6]>
<script type="text/javascript" src="lib/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<script type="text/javascript" src="/static/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/static/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/static/h-ui/js/H-ui.min.js"></script>
<script type="text/javascript" src="/static/h-ui.admin/js/H-ui.admin.js"></script>
<script type="text/javascript" src="/static/lib/Validform/5.3.2/Validform.min.js"></script>
<script type="text/javascript" src="/static/h-ui.admin/common.js"></script>
<script type="text/javascript" src="/static/lib/laydate5.0.9/laydate.js"></script>
<title>Tpflow</title>
</head>
<body>
<body onload="prettyPrint()">
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> Tpflow 工作流插件示例
<a href="<?php echo url('user/index'); ?>"  class="btn btn-primary radius"> 模拟用户</a>
<a href="<?php echo url('user/role'); ?>"  class="btn btn-primary radius"> 模拟角色</a>
<a href="<?php echo url('news/index'); ?>"  class="btn btn-primary radius"> 单据审核</a>
<a href="<?php echo url('flowdesign/lists'); ?>"  class="btn btn-primary radius">工作流设计</a>
<a href="<?php echo url('flow/index'); ?>"  class="btn btn-primary radius">流程监控</a>　　
</nav>
<div class="page-container">
<h4>模拟登入(点击姓名，进行模拟登入)：
<mark>
<?php if(app('session')->get('uid')): ?>
欢迎您：<?php echo htmlentities(app('session')->get('uname')); ?> 使用本插件！
<?php else: ?>
	请先模拟登入！
<?php endif; ?>
</mark></h4>
<?php if(is_array($user) || $user instanceof \think\Collection || $user instanceof \think\Paginator): $i = 0; $__LIST__ = $user;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
	<a href="<?php echo url('login',['id'=>$k['id'],'user'=>$k['username'],'role'=>$k['role']]); ?>" class="btn btn-primary radius"><?php echo htmlentities($k['username']); ?></a>
<?php endforeach; endif; else: echo "" ;endif; ?>
<h2>API 文档说明</h2>
<h4>第一步：工作流设计</h4>
<pre class="prettyprint linenums">
//详见 Flowdesign.php 
//工作流类别为了后期方便建议：使用表名(不含表前缀)为类别
@param $status   工作流状态 0为正常，1为禁用工作流
@param $flow_name  工作流名称
@param $wf_type 工作流通常是表名 工作流设计的时候会根据表名，查找字段组装拼接转出条件，表名不能出错！
$wf_type = [
	'news'=>'新闻信息',
	'cnt'=>'合同信息',
	'paper'=>'证件信息'
];
</pre>
<h4>第二步：单据填写</h4>
<h4>第三步：选择工作流——>发起流程</h4>
<pre class="prettyprint linenums">
@param $wf_type 工作流通常是表名
@param $wf_id   工作流id
@param $wf_fid  单据编号
$workflow = new workflow();
//获取本类工作流信息
$flow = $workflow->getWorkFlow($wf_type);

//直接发起工作流
$wf_type = input('wf_type');
$wf_id = input('wf_id');
$wf_fid = input('wf_fid');
$flow = $workflow->startworkflow($wf_id,$wf_fid,$wf_type);
</pre>
<h4>第四步：审核单据发起——>获取工作流信息，获取下一个工作流信息——>日志记录——>发起消息通知</h4>
<pre class="prettyprint linenums">
@param $wf_title 单据名称
@param $flow_id  工作流id
@param $npid     下一个工作流id，如果为空，及结束节点
@param $run_id   运行记录id
@param $check_con   审批意见
@param $submit_to_save   按钮值
@param $run_flow_process 当前运行的进程id

$workflow = new workflow();
//工作流审核发起，获取当前及下一个审批流信息
$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type);

//工作流审核发起保存
$flowinfo = $workflow->workdoaction($config);

</pre>
	</table>
</div>

</body>
</html>
<script type="text/javascript" src="http://cdn.bootcss.com/prettify/r298/prettify.min.js"></script>