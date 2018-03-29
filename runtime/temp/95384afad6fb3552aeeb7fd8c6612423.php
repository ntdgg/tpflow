<?php /*a:2:{s:46:"D:\tpflow\application\index\view\news\add.html";i:1522328460;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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
			<td style='width:75px'>新闻标题：</td><td style='width:330px'><input type="text" class="input-text" value="<?php echo isset($info['new_title']) ? htmlentities($info['new_title']) : ''; ?>" name="new_title"  datatype="*" ></td>
			<td style='width:75px'>是否置顶：</td><td>
					<div class="skin-minimal">
						<div class="radio-box" >
						<input type="radio" checked id="radio-0" name="new_top"  value='1'>
						<label for="radio-0">是</label>
					  </div>
					<div class="radio-box" >
						<input type="radio"  id="radio-1" name="new_top"  value='0'>
						<label for="radio-1">否</label>
					  </div>
					</div>
			</td>
			</tr>
			<tr>
			<td>新闻类别：</td><td>
			<span class="select-box">
				<select name="new_type"  class="select"  datatype="*" >
					<?php if(is_array($type) || $type instanceof \think\Collection || $type instanceof \think\Paginator): $i = 0; $__LIST__ = $type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
					<option value="<?php echo htmlentities($k['id']); ?>"><?php echo htmlentities($k['type']); ?></option>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
				</span>
			</td>
			<td></td><td>
			
			</td>
			</tr><tr><td>新闻内容</td><td colspan="3" ><textarea name='new_con'  datatype="*"  id="editor" type="text/plain" style="width:100%;height:300px;">
			<?php echo isset($info['new_con']) ? htmlentities($info['new_con']) : ''; ?>
			</textarea> </td></tr>
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