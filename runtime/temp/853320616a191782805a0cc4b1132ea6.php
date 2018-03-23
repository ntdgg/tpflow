<?php /*a:2:{s:75:"C:\Users\Administrator\web\tpflow\application/index/view\flow\do_check.html";i:1521764418;s:70:"C:\Users\Administrator\web\tpflow\application/index/view\pub\base.html";i:1521687383;}*/ ?>
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
<link rel="stylesheet" href="/static/lib/multiple-select/multiple-select.css" />
<div class="page-container" style='width:98%'>
<form action="<?php echo url('do_check_save'); ?>" method="post" name="form" id="forms">
<input type="hidden" value="<?php echo htmlentities($info['wf_title']); ?>" name="wf_title">
<input type="hidden" value="<?php echo htmlentities($info['wf_fid']); ?>" name="wf_fid">
<input type="hidden" value="<?php echo htmlentities($info['wf_type']); ?>" name="wf_type">
<input type="hidden" value="<?php echo htmlentities($flowinfo['flow_id']); ?>" name="flow_id">
<input type="hidden" value="<?php echo htmlentities($flowinfo['run_flow_process']); ?>" name="run_flow_process">
<input type="hidden" value="<?php echo htmlentities($flowinfo['run_id']); ?>" name="run_id">
		<table class="table table-border table-bordered table-bg" style='width:98%'>
			<thead>
			<tr>
			<th style='width:38%' class='text-c'>单据审批</th>
			<th style='width:59%' class='text-c'>审批记录</th>
			</tr>
			<tr>
			</thead>
			<td style='height:80px'>
				<table class="table table-border table-bordered table-bg">
				<tr>
				<th style='width:30px'>审批意见</th>
				<th><textarea name='check_con'  datatype="*" style="width:100%;height:75px;"></textarea> </th>
				</tr>
				<tr>
				<td colspan=2 class='text-c'>
				<input id='submit_to_save' name='submit_to_save' value='' type='hidden'>
				<a class="btn btn-primary radius" onclick='tj("ok")' >提交</a> 
				<a class="btn btn-primary radius" onclick='tj("back")'value='back' >回退</a> 
				<a class="btn btn-primary radius" onclick='tj("sing")' value='sing' >会签</a>
				</td>
				</tr>
				</table>
			</td>
			<td>
			<?php if(is_array($flowinfo['log']) || $flowinfo['log'] instanceof \think\Collection || $flowinfo['log'] instanceof \think\Paginator): $i = 0; $__LIST__ = $flowinfo['log'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
				<?php echo htmlentities($k['user']); ?>-<?php echo htmlentities($k['content']); ?>-<?php echo htmlentities($k['dateline']); endforeach; endif; else: echo "" ;endif; ?>
			</td>
			</tr>
		</table>
</form>		
		<table class="table table-border table-bordered mt-20" style='width:98%'>
		<tr><td>
		
		<iframe src="<?php echo url('news/view',['id'=>$info['wf_fid']]); ?>" id="iframepage" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" onLoad="iFrameHeight()"></iframe>
		
		</td></tr>
		</table>
	
</div>

<script type="text/javascript">
$(function(){
	$("#forms").Validform({
            tiptype:2,
            ajaxPost:true,
            showAllError:true,
            callback:function(ret){
                ajax_progress(ret);
            }
        });
	 
});
function tj(value){
	$('#submit_to_save').val(value);
	$('#forms').submit();
} 
function iFrameHeight() {   
		var ifm= document.getElementById("iframepage");   
		var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;   
		if(ifm != null && subWeb != null) {
		   ifm.height = subWeb.body.scrollHeight;
		   ifm.width = '100%';
		}   
} 
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>