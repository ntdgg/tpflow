<?php /*a:2:{s:72:"C:\Users\Administrator\web\tpflow\application/index/view\demo\index.html";i:1520499845;s:73:"C:\Users\Administrator\web\tpflow\application/index/view\Public\base.html";i:1520499604;}*/ ?>

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

    <h1>流程设计器使用实例演示</h1>
    <p>
       使用流程设计器，有效强化系统审批工作的功能！<br /> 本实例仅方便大家了解使用方法和简化二次开发，你可以任意修改使功能无限强大！
    </p>
    
  </div>
</div>

<div class="container">



<div class="row">
    <ol class="breadcrumb">
        <li><a href="/">流程设计器</a> <span class="divider">/</span></li>
        <li class="active">实例</li>
    </ol>
</div>


<div class="row">
<p>
    <a href="<?php echo url('/demo/add'); ?>" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-plus"></span> 添加流程</a>
</p>

<table class="table table-hover">
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
        <a href="<?php echo url('/run/add/flow_id/'.$vo['id']); ?>" onclick="return confirm('你确定使用该流程发起一个工作流程吗？') ? true : false;"  target="_blank" title="使用该流程发起工作"><span class="glyphicon glyphicon-eye-open"></span> 发起</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?php echo url('/run/index/flow_id/'.$vo['id']); ?>"  target="_blank" ><span class="glyphicon glyphicon-eye-open"></span> 发起记录</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <if condition="$vo.fields gt 0">
            <a href="<?php echo url('/demodata/index/flow_id/'.$vo['id']); ?>" target="_blank"><span class="glyphicon glyphicon-list"></span> 流程数据</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </if>
            <a href="<?php echo url('/index/flowdesign/index',['flow_id'=>$vo['id']]); ?>" target="_blank"><span class="glyphicon glyphicon-new-window"></span> 设计流程</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo url('/demo/edit/flow_id/'.$vo['id']); ?>" target="_blank"><span class="glyphicon glyphicon-edit"></span> 编辑</a></td>
    </tr>
    <?php endforeach; endif; else: echo "" ;endif; ?>
</table>

<?php echo htmlentities($page); ?>

</div><!--end row-->

</div><!--end container-->



</block><!--end body-->

<block name="footer_js">

<!-- script end -->


</block><!--end footer_js-->
