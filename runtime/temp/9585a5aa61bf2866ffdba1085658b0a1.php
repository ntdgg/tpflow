<?php /*a:2:{s:58:"D:\tpflow\application\index\view\formdesign\functions.html";i:1522751500;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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
			<form action="<?php echo url('functions'); ?>" method="post" name="form" id="form">
			<input type="hidden" name="fid" value="<?php echo htmlentities($fid); ?>">
		<?php endif; ?>
		<table class="table table-border table-bordered table-bg">
			<tr>
			<td style='width:75px'>函数名称</td><td style='width:330px' colspan='2'>
			<input type="text" class="input-text" value="<?php echo isset($info['name']) ? htmlentities($info['name']) : ''; ?>" name="name"  datatype="*" ></td>
			</tr>
			<tr>
			<td style='width:75px'>函数名称</td><td style='width:330px'>
			<ul class="cl">
				<li class="dropDown dropDown_hover">
					<a href="#" class="dropDown_A">Sql语句填写示例<i class="Hui-iconfont">&#xe6d5;</i></a>
					<ul class="dropDown-menu menu radius box-shadow">
						<li><a href="javascript:insert('SELECT * FROM `[table]` WHERE 1')">select</a></li>
						<li><a href="javascript:insert('SELECT * FROM `[table]` WHERE 1 Limit 1')">find</a></li>
					</ul>
				</li>
			</ul>
			</td>
			<td style='width:330px' >执行结果</td>
			</tr>
			<tr valign="top">
			<td style='width:75px'>函数语句：</td><td style='width:330px' >
			<textarea placeholder="此为SQL语句，非专业人士请勿随意提交！" id='sql' name='sql' type="text/plain" style="width:100%;height:150px;"></textarea> </td>
		    <td style='width:330px' ><p id="result" style="background-color: #cccccc;"></p></td>
			
			</tr>
		</table>
		
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<a  class="btn btn-primary radius" id='forms'><i class="Hui-iconfont">&#xe632;</i> 测试</a>
				<button  class="btn btn-primary radius" id='oks' type="submit" disabled="disabled"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
				
				<button  class="btn btn-default radius" type="button" onclick="layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</article>


<script src="https://cdn.bootcss.com/Base64/1.0.1/base64.js"></script>
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
function insert($sql){
    if ($sql){
        var table=$("#tables").val();
        $("#sql").text($sql.replace("[table]", table));
    }
}
$("#forms").click(function(){
    var sql=$("#sql").val();
    if (!sql){layer.msg("SQL不能为空!!");return;}
    $.ajax({  
         url:'<?php echo url("ajax_sql"); ?>',
         data:{sql:sql},  
         type:'post',  
         cache:true,  
         dataType:'html',  
         success:function(data) {  
			if(data == 1){
				$("#result").html('错误的SQL语句！<br/>'+$("#sql").val());
			}else{
				$('#oks').attr("disabled",false); 
				$("#result").html(data); 
			}
          },  
          error : function() {  
              $("#result").html('错误的SQL语句！<br/>'+$("#sql").val());
          }  
     }); 
      
    
})
</script>
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>