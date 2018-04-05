<?php /*a:2:{s:47:"D:\tpflow\application\index\view\cnt\index.html";i:1522890553;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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
    <form class="mb-20" method="get" action="<?php echo url(app('request')->action()); ?>">
        <input type="text" class="input-text" style="width:250px" placeholder="姓名" name="name2" value="<?php echo htmlentities(app('request')->param('name2')); ?>" >
        <input type="text" class="input-text" style="width:250px" placeholder="联系电话" name="tel2" value="<?php echo htmlentities(app('request')->param('tel2')); ?>" >
        <input type="text" class="input-text" style="width:250px" placeholder="基本信息" name="cont2" value="<?php echo htmlentities(app('request')->param('cont2')); ?>" >
        <input type="text" class="input-text" style="width:250px" placeholder="姓名2" name="name" value="<?php echo htmlentities(app('request')->param('name')); ?>" >
        <input type="text" class="input-text" style="width:250px" placeholder="联系电话2" name="tel" value="<?php echo htmlentities(app('request')->param('tel')); ?>" >
        <input type="text" class="input-text" style="width:250px" placeholder="基本信息2" name="cont" value="<?php echo htmlentities(app('request')->param('cont')); ?>" >
        <button type="submit" class="btn btn-success"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
    </form>
    <div class="cl pd-5 bg-1 bk-gray">
        <span class="l">
            <a href="javascript:;" onclick="layer_show('新增','<?php echo url('add'); ?>','850','500')" class="btn btn-primary radius">新增</a>
        </span>
        <span class="r pt-5 pr-5">
            共有数据 ：<strong><?php echo isset($count) ? htmlentities($count) : '0'; ?></strong> 条
        </span>
    </div>
    <table class="table table-border table-bordered table-hover table-bg mt-20">
        <thead>
        <tr class="text-c">
            <th width="25"><input type="checkbox"></th>
            <th>姓名</th>
            <th>联系电话</th>
            <th>基本信息</th>
            <th>姓名2</th>
            <th>联系电话2</th>
            <th>基本信息2</th>
            <th width="70">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <tr class="text-c">
            <td><input type="checkbox" name="id[]" value="<?php echo htmlentities($vo['id']); ?>"></td>
            <td><?php echo htmlentities($vo['name2']); ?></td>
            <td><?php echo htmlentities($vo['tel2']); ?></td>
            <td><?php echo htmlentities($vo['cont2']); ?></td>
            <td><?php echo htmlentities($vo['name']); ?></td>
            <td><?php echo htmlentities($vo['tel']); ?></td>
            <td><?php echo htmlentities($vo['cont']); ?></td>
            <td class="f-14">
					<span class="btn  radius size-S" onclick="layer_show('修改','<?php echo url('edit',['id'=>$vo['id']]); ?>','850','500')">修改</span>
            </td>
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
    <div class="page-bootstrap"><?php echo isset($page) ? htmlentities($page) : ''; ?></div>
</div>


