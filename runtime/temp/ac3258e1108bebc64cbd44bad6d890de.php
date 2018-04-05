<?php /*a:2:{s:49:"D:\tpflow\application\index\view\index\index.html";i:1522859561;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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
<header class="navbar-wrapper">
	<div class="navbar navbar-fixed-top">
		<div class="container-fluid cl"> <a class="logo navbar-logo f-l mr-10 hidden-xs" href="<?php echo url('index'); ?>">
		Tpflow</a> <a class="logo navbar-logo-m f-l mr-10 visible-xs" href="/aboutHui.shtml"></a> 
			<span class="logo navbar-slogan f-l mr-10 hidden-xs"> V1.5</span> 
			<span class='logo navbar-slogan f-l mr-10 hidden-xs'><b>这可能是Thinkphp第一款开源的，工作流插件</b>  </span>
			<span class='logo navbar-slogan f-l mr-10 hidden-xs'>开源协议：MIT  </span>
			<span class='logo navbar-slogan f-l mr-10 hidden-xs'>作者：蝈蝈（1838188896） 交流群：532797225</span>
		</nav>
		<nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
			<ul class="cl">
				<li class="dropDown dropDown_hover">
					<a href="#" class="dropDown_A"><?php if(app('session')->get('uid')): ?>欢迎您：<?php echo htmlentities(app('session')->get('uname')); ?> 使用本插件！<?php else: ?>请先模拟登入！<?php endif; ?></a>
			</li>
			</ul>
		</nav>
	</div>
</div>
</header>
<!--左侧菜单开始-->
<aside class="Hui-aside">
		<div class="menu_dropdown bk_2" >
			<dl>
				<dt><i class="Hui-iconfont"></i> 新闻管理<i class="Hui-iconfont menu_dropdown-arrow"></i></dt>
				<dd style="display: block;"><ul>
				<li><a data-href="<?php echo url('news/index'); ?>" data-title="新闻管理" href="javascript:void(0)">新闻管理</a></li>
				</dd>
				</dl>
			<dl>
			<?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
			<dl>
				<dt><i class="Hui-iconfont"></i> <?php echo htmlentities($k['name']); ?><i class="Hui-iconfont menu_dropdown-arrow"></i></dt>
				<dd style="display: block;"><ul>
				<li><a data-href="<?php echo url($k['url']); ?>" data-title="test" href="javascript:void(0)"><?php echo htmlentities($k['name']); ?></a></li>
				</dd>
				</dl>
			<?php endforeach; endif; else: echo "" ;endif; ?>
			<dl>
				<dt><i class="Hui-iconfont"></i> 用户管理<i class="Hui-iconfont menu_dropdown-arrow"></i></dt>
				<dd style="display: block;"><ul>
				<li><a data-href="<?php echo url('user/index'); ?>" data-title="用户列表" href="javascript:void(0)">用户列表</a></li>
				<li><a data-href="<?php echo url('user/role'); ?>" data-title="角色列表" href="javascript:void(0)">角色列表</a></li>
				</dd>
				</dl>
			<dl>
				<dt><i class="Hui-iconfont"></i> 表单自定义设计<i class="Hui-iconfont menu_dropdown-arrow"></i></dt>
				<dd style="display: block;"><ul>
				<li><a data-href="<?php echo url('Formdesign/index'); ?>" data-title="工作流列表" href="javascript:void(0)">表单设计</a></li>
				</dd>
			</dl>
			<dl>
				<dt><i class="Hui-iconfont"></i> 工作流设计<i class="Hui-iconfont menu_dropdown-arrow"></i></dt>
				<dd style="display: block;"><ul>
				<li><a data-href="<?php echo url('flowdesign/lists'); ?>" data-title="工作流列表" href="javascript:void(0)">工作流列表</a></li>
				<li><a data-href="<?php echo url('flow/index'); ?>" data-title="工作流监控" href="javascript:void(0)">工作流监控</a></li>
				</dd>
			</dl>
			
		</div>	
</div>
</aside>
<!--左侧菜单结束-->
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
	<div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
		<div class="Hui-tabNav-wp">
			<ul id="min_title_list" class="acrossTab cl">
				<li class="active">
					<span title="我的桌面" data-href="welcome.html">我的桌面</span>
					<em></em></li>
		</ul>
	</div>
		<div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
</div>
	<div id="iframe_box" class="Hui-article">
		<div class="show_iframe">
			<div style="display:none" class="loading"></div>
			<iframe scrolling="yes" frameborder="0" src="<?php echo url('index/welcome'); ?>"></iframe>
	</div>
</div>
</section>
<script>
var session ='<?php echo htmlentities(app('session')->get('uid')); ?>';
if(session =='' ){
layer.open({
      type: 2,
      title: '网站',
      shadeClose: true,
      shade: false,
      maxmin: true, //开启最大化最小化按钮
      area: ['493px', '600px'],
      content: '//cojz8.com/'
    });
	}
</script>
</body>
</html>