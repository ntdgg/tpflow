<?php /*a:2:{s:77:"C:\Users\Administrator\web\tpflow\application\index\view\formdesign\desc.html";i:1522739226;s:70:"C:\Users\Administrator\web\tpflow\application\index\view\pub\base.html";i:1521687383;}*/ ?>
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
  <link rel="stylesheet" href="/static/formbuilder/vendor.css" />
  <link rel="stylesheet" href="/static/formbuilder/formbuilder.css" />
  <style>
  * {
    box-sizing: border-box;
  }

  body {
    background-color: #d6d3d3;
    font-family: sans-serif;
  }

  .fb-main {
    background-color: #fff;
    border-radius: 5px;
    min-height: auto;
  }

  input[type=text] {
    height: 26px;
    margin-bottom: 3px;
  }

  select {
    margin-bottom: 5px;
    font-size: 40px;
  }
  </style>
<div class="page-container">
   <div class='fb-main'></div>
</div>
<input name='ziduan' id='ziduan' value=''>
<input name='id' id='id' value='<?php echo htmlentities($fid); ?>'>

  <script src="/static/formbuilder/vendor.js"></script>
  <script src="/static/formbuilder/formbuilder.js"></script>
 <script>
    $(function(){
      fb = new Formbuilder({
        selector: '.fb-main',
        bootstrapData: [
         
         
        ]
      });

      fb.on('save', function(payload){
        $('#ziduan').val(payload);
		
		
      })
	  $("#up").click(function(){
		 var ziduan=$("#ziduan").val();
		 var id=$("#id").val();
		
		$.ajax({  
			 url:'<?php echo url("desc"); ?>',
			 data:{ziduan:ziduan,id:id},  
			 type:'post',  
			 cache:true,  
			dataType:'json',			 
			 success:function(data) {  
				if(data.code==0){
					lay.msg('Success!');
					layer_close();
				}
			  },  
			  error : function() {  
				  
			  }  
		 }); 
		
		})
    });
	
  </script>
