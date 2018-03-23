<?php /*a:2:{s:72:"C:\Users\Administrator\web\tpflow\application/index/view\demo\index.html";i:1521687302;s:70:"C:\Users\Administrator\web\tpflow\application/index/view\pub\base.html";i:1521687383;}*/ ?>
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
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i>  Tpflow 工作流插件示例 <a href="<?php echo url('index/index'); ?>"  class="btn btn-primary radius"> 返回</a></nav>
<div class="page-container">

<table class="table table-border table-bordered table-bg">
    <tr>
        <th>ID</th>
        <th>流程名称</th>
        <th>表单</th>
        <th>类型</th>
        <th>适用部门</th>
        <th>添加时间</th>
        <th>操作</th>
    </tr>
    <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
    <tr>
        <td><?php echo htmlentities($vo['id']); ?></td>
        <td><span title="<?php echo htmlentities($vo['flow_desc']); ?>"><?php echo htmlentities($vo['flow_name']); ?></span></td>
        <td><?php echo htmlentities($vo['form_id']); ?></td>
        <td>固定流程</td>
        <td>默认</td>
        <td><?php echo date('Y/m/d H:i',$vo['dateline']); ?></td>
        <td>
       <a class='btn  radius size-S' href="<?php echo url('/index/flowdesign/index',['flow_id'=>$vo['id']]); ?>" target="_blank"> 设计流程</a></td>
    </tr>
    <?php endforeach; endif; else: echo "" ;endif; ?>
</table>

</div>