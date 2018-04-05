<?php /*a:2:{s:52:"D:\tpflow\application\index\view\formdesign\add.html";i:1522751500;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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
<article class="page-container">
		<?php if(isset($info['id'])): ?>
			<form action="<?php echo url('edit'); ?>" method="post" name="form" id="form">
			<input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>">
			<input type="hidden" name="status" value="0">
		<?php else: ?>
			<form action="<?php echo url('add'); ?>" method="post" name="form" id="form">
		<?php endif; ?>
		<table class="table table-border table-bordered table-bg">
			<tr>
			<td style='width:75px'>菜单名称</td><td style='width:330px'>
			<input type="text" class="input-text" value="<?php echo isset($info['title']) ? htmlentities($info['title']) : ''; ?>" name="title"  datatype="*" ></td>
			<td style='width:75px'>数据表（table）</td><td>
				<input type="text" class="input-text" value="<?php echo isset($info['name']) ? htmlentities($info['name']) : ''; ?>" name="name"  datatype="*" >
			</td>
			</tr>
			<tr>
			<td style='width:75px'>生成文件：</td><td style='width:330px'>
			 <div class="select-box" style="width: 260px">
                    <select name="file" class="select">
                        <option value="all">默认生成文件（all）</option>
                        <option value="controller">控制器（controller）</option>
						<option value="数据表（table）">数据表（table）</option>
                    </select>
                </div>
			</td>
			<td style='width:75px'>生成栏目：</td><td>
				 <div class="select-box" style="width: 260px">
                    <select name="menu" class="select">
                        <option value="0">是</option>
                        <option value="1">否</option>
                    </select>
                </div>	
			</td>
			</tr>
			<tr>
			<td style='width:75px'>挂带审批：</td><td style='width:330px'>
			 <div class="select-box" style="width: 260px">
                    <select name="flow" class="select">
                        <option value="0">挂带审批流</option>
                        <option value="1">无需审批流</option>
                    </select>
                </div>
			</td>
			<td style='width:75px'></td><td>
				
			</td>
			</tr>
		</table>
		
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
				<button  class="btn btn-default radius" type="button" onclick="layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</article>


<script type="text/javascript" src="/static/lib/ueditor/1.4.3/ueditor.config.js"></script> 
<script type="text/javascript" src="/static/lib/ueditor/1.4.3/ueditor.all.min.js"> </script> 
<script type="text/javascript" src="/static/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
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
	var ue = UE.getEditor('editor');
});
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>