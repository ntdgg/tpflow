<?php /*a:2:{s:46:"D:\tpflow\application\index\view\cnt\edit.html";i:1522890831;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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

<div class="page-container">
    <form class="form form-horizontal" id="form" method="post" action="">
        <input type="hidden" name="id" value="<?php echo isset($vo['id']) ? htmlentities($vo['id']) : ''; ?>">
		<table class="table table-border table-bordered table-bg"><tr><td>姓名</td><td><input type="text" class="input-text" placeholder="姓名" name="name2" value="<?php echo isset($vo['name2']) ? htmlentities($vo['name2']) : ''; ?>" ></td><td>联系电话</td><td><input type="text" class="input-text" placeholder="联系电话" name="tel2" value="<?php echo isset($vo['tel2']) ? htmlentities($vo['tel2']) : ''; ?>" ></td><td>基本信息</td><td><input type="text" class="input-text" placeholder="基本信息" name="cont2" value="<?php echo isset($vo['cont2']) ? htmlentities($vo['cont2']) : ''; ?>" ></td></tr><tr><td>姓名2</td><td><input type="text" class="input-text" placeholder="姓名2" name="name" value="<?php echo isset($vo['name']) ? htmlentities($vo['name']) : ''; ?>" ></td><td>联系电话2</td><td><input type="text" class="input-text" placeholder="联系电话2" name="tel" value="<?php echo isset($vo['tel']) ? htmlentities($vo['tel']) : ''; ?>" ></td><td>基本信息2</td><td><input type="text" class="input-text" placeholder="基本信息2" name="cont" value="<?php echo isset($vo['cont']) ? htmlentities($vo['cont']) : ''; ?>" ></td></tr></table>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <button type="submit" class="btn btn-primary radius">&nbsp;&nbsp;提交&nbsp;&nbsp;</button>
                <button type="button" class="btn btn-default radius ml-20" onClick="layer_close();">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
        </div>
    </form>
</div>


<script type="text/javascript" src="/static/lib/Validform/5.3.2/Validform.min.js"></script>
<script>
    $(function () {


        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });

        $("#form").Validform({
            tiptype: 2,
            ajaxPost: true,
            showAllError: true,
            callback: function (ret){
                ajax_progress(ret);
            }
        });
    })
</script>

