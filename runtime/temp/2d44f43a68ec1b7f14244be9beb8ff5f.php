<?php /*a:2:{s:54:"D:\tpflow\application\index\view\formdesign\index.html";i:1522851840;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i>  Tpflow 工作流插件示例 
<a href="<?php echo url('index/welcome'); ?>"  class="btn btn-primary radius"> 返回</a>

<a href="javascript:;" onclick="layer_show('新增表单','<?php echo url('add'); ?>','850','500')" class="btn btn-primary radius">
	<i class="Hui-iconfont">&#xe600;</i> 新增表单</a></nav>
<div class="page-container">
	<table class="table table-border table-bordered table-bg">
		<thead>
			<tr class="text-c">
				<th width="25">ID</th>
				<th width="50">名称</th>
				<th width="80">数据表（table）</th>
				<th width="150">生成文件</th>
				<th width="150">发布时间</th>
				<th width="150">状态</th>
				<th width="100">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
			<tr class="text-c">
				<td><?php echo htmlentities($k['id']); ?></td>
				<td><?php echo htmlentities($k['title']); ?></td>
				<td><?php echo htmlentities($k['name']); ?></td>
				<td><?php echo htmlentities($k['file']); ?></td>
				<td><?php echo htmlentities(date('Y-m-d H:i',!is_numeric($k['add_time'])? strtotime($k['add_time']) : $k['add_time'])); ?></td>
				<td>
				</td>
				<td class="td-manage">
				<div class="btn-group">
					<span class="btn  radius size-S" data-title="查看" data-href="<?php echo url('view',['id'=>$k['id']]); ?>" onclick="Hui_admin_tab(this)"><i class="Hui-iconfont">查看</span>
					<?php if($k['status'] == 0): ?>
					<span  onclick="layer_show('新增函数','<?php echo url('functions',['id'=>$k['id']]); ?>','850','500')" class="btn  radius size-S">新增函数</span>
					<span  onclick="layer_show('表单设计','<?php echo url('desc',['id'=>$k['id']]); ?>','950','500')" class="btn  radius size-S">表单设计</span>
					<span class="btn  radius size-S" onclick="layer_show('修改','<?php echo url('edit',['id'=>$k['id']]); ?>','850','500')">修改</span>
					<a class="btn  radius size-S" href="<?php echo url('shengcheng',['id'=>$k['id']]); ?>">生成</a>
					<?php else: ?>
					已生成
					<?php endif; ?>
				</div>
				</td>
			</tr>
			<?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
</div>
<div class="page-bootstrap"><?php echo $list; ?></div>
</body>
</html>