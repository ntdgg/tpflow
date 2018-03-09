<?php /*a:2:{s:46:"D:\tpflow\application/index/view\run\edit.html";i:1520591782;s:49:"D:\tpflow\application/index/view\Public\base.html";i:1520590614;}*/ ?>

<!DOCTYPE HTML>
<html>
 <head>
        <title> Flowdesign.leipi.org</title>
  <meta name="keyword" content="流程设计器,Web Flowdesign,Flowdesigner,专业流程设计器,WEB流程设计器">
  <meta name="description" content="国内最容易使用和开发的流程设计器，你可以在此基础上任意修改使功能无限强大！">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="author" content="leipi.org">
    <link href="/static/work/css/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="/static/work/css/bootstrap/css/bootstrap-ie6.css">
    <![endif]-->
    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="/static/work/css/bootstrap/css/ie.css">
    <![endif]-->
    <link href="/static/work/css/site.css" rel="stylesheet" type="text/css" />
    
 </head>
<body>

<!-- fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="brand" href="http://www.leipi.org" target="_blank">雷劈网</a>
      <div class="nav-collapse collapse">
        <ul class="nav">
            <li><a  href="http://flowdesign.leipi.org/">流程设计器</a></li>
            <li><a href="http://formdesign.leipi.org">表单设计器</a></li>
            <li><a href="http://qrcode.leipi.org">自动生成二维码</a></li>
            <li><a href="http://flowdesign.leipi.org/index.php?s=/doc.html">文档</a></li>
			<li class="active"><a href="<?php echo url('/demo'); ?>">实例</a></li>
            <li><a href="http://flowdesign.leipi.org/index.php?s=/downloads.html">下载</a></li>
            <li><a href="http://flowdesign.leipi.org/index.php?s=/feedback.html">公开讨论</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<include file="Public:team"/>



</body>
</html>

<block name="title">
    <title>流程设计器 实例演示 Flowdesign.leipi.org</title>
    <meta name="keyword" content="流程设计器实例演示,Web Flowdesign,Flowdesigner,专业流程设计器,WEB流程设计器">
    <meta name="description" content="流程设计器实例演示，国内最容易使用和开发的流程设计器，你可以在此基础上任意修改使功能无限强大！">
</block>
<block name="head">
</block><!--end head-->

<block name="body">


<div class="bs-header" id="content">
  <div class="container">

    <h1>办理工作</h1>
    <p>
       填好表后请记得保存或转交给下一步经办人喔。
    </p>
    
  </div>
</div>

<div class="container">

<div class="row">
    <ol class="breadcrumb">
        <li><a href="/">流程设计器</a> <span class="divider">/</span></li>
        <li><a href="<?php echo url('/demo'); ?>">实例</a> <span class="divider">/</span></li>
        <li class="active"><if condition="empty($run_process)">发起<else/>办理</if></li>
    </ol>
</div>

<div class="row">
<form action="<?php echo url('/index/run/edit_save'); ?>" method="post">
<input type="hidden" value="<?php echo htmlentities($run_process['id']); ?>" name="run_process">

<p>
	<h4><i class="icon-play"></i>工作名称</h4>
    <input type="text" class="span6" placeholder="必填项" name="run_name" value="<?php echo htmlentities($run_one['run_name']); ?>">
</p>
<hr/>
<p>
	<h4><i class="icon-play"></i>填写表单</h4>
    <?php echo htmlentities($design_content); ?>
</p>
<hr/>
<p>
	<h4><i class="icon-play"></i>上传附件</h4>
	未完成...
</p>
<hr/>
<p>
	<h4><i class="icon-play"></i>会签意见</h4>
	未完成...
</p>


<hr/>
<button type="submit" name="submit_to_save" value="save" class="btn btn-primary">确定保存</button>
<button type="submit" name="submit_to_next" value="next" class="btn btn-info">保存转交下一步</button>
<button type="submit" name="submit_to_end" value="end" class="btn btn-success">办结</button>
</form>


</div><!--end row-->
</div><!--end container-->




<block name="footer_js">

<!-- script end -->


</block><!--end footer_js-->