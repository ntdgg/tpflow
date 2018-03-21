<?php /*a:2:{s:48:"D:\tpflow\application/index/view\flow\start.html";i:1521642638;s:46:"D:\tpflow\application/index/view\pub\base.html";i:1516702710;}*/ ?>
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
<title>PMS</title>
</head>
<body>
  <link rel="stylesheet" href="/static/lib/multiple-select/multiple-select.css" />
<article class="page-container">
		<form action="<?php echo url('statr_save'); ?>" method="post" name="form" id="form">
		<input type='hidden' value='<?php echo htmlentities($info['wf_type']); ?>' name='wf_type'>
		<input type='hidden' value='<?php echo htmlentities($info['wf_fid']); ?>' name='wf_fid'>
		<table class="table table-border table-bordered table-bg">
			<tr>
			<td style='width:75px'>项目名称：</td>
			<td style='width:330px'><?php echo htmlentities($info['wf_title']); ?></td>
			</tr>
			<tr>
			<td>选择工作流：</td><td>
			<span class="select-box">
				<select name="wf_id"  class="select"  datatype="*" >
					<option value="0">请选择工作流</option>
					<?php if(is_array($flow) || $flow instanceof \think\Collection || $flow instanceof \think\Paginator): $i = 0; $__LIST__ = $flow;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
					<option value="<?php echo htmlentities($k['id']); ?>"><?php echo htmlentities($k['flow_name']); ?></option>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
				</span>
			</td>
			</tr>
			<tr>
			<td>紧急程度：</td><td>
			<span class="select-box">
				<select name="new_type"  class="select"  datatype="*" >
					<option value="0">普通</option>
					<option value="1">加急</option>
					<option value="2">紧急</option>
					<option value="3">特急</option>
				</select>
				</span>
			</td>
			</tr>
			<tr>
			<td>审核意见：</td><td>
				<input type="text" class="input-text" value="<?php echo isset($info['new_title']) ? htmlentities($info['new_title']) : ''; ?>" name="new_title"  datatype="*" >
			</td>
			</tr>
			<tr>
			<td colspan='2' class='text-c'>
			
			<button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
				<button  class="btn btn-default radius" type="button" onclick="layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button></td>、
			</tr>
		</table>
		
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				
			</div>
		</div>
	</form>
</article>


<script type="text/javascript">
$(function(){
	$("[name='new_top'][value='<?php echo isset($info['new_top']) ? htmlentities($info['new_top']) : ''; ?>']").attr("checked",true);
	$("[name='new_type']").find("[value='<?php echo isset($info['new_type']) ? htmlentities($info['new_type']) : '0'; ?>']").attr("selected",true);
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});
	
	$("#form").Validform({
            tiptype:2,
            ajaxPost:true,
            showAllError:true,
            callback:function(ret){
                ajax_progress(ret);
            }
        });

});
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>