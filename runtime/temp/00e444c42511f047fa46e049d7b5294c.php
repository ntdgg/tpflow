<?php /*a:2:{s:54:"D:\tpflow\application\index\view\flowdesign\index.html";i:1522922476;s:46:"D:\tpflow\application\index\view\pub\base.html";i:1521724601;}*/ ?>
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
<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="author" content="leipi.org">
    <link href="/static/work/css/bootstrap/css/bootstrap.css?" rel="stylesheet" type="text/css" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="/static/work/css/bootstrap/css/bootstrap-ie6.css?">
    <![endif]-->
    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="/static/work/css/bootstrap/css/ie.css?">
    <![endif]-->
    <link href="/static/work/css/site.css?" rel="stylesheet" type="text/css" />
  <title>流程设计器 Flowdesign.leipi.org</title>
  <meta name="keyword" content="流程设计器,Web Flowdesign,Flowdesigner,专业流程设计器,WEB流程设计器">
  <meta name="description" content="国内最容易使用和开发的流程设计器，你可以在此基础上任意修改使功能无限强大！">
<link rel="stylesheet" type="text/css" href="/static/work/js/flowdesign/flowdesign.css"/>
<!--select 2-->
<link rel="stylesheet" type="text/css" href="/static/work/js/jquery.multiselect2side/css/jquery.multiselect2side.css"/>
<!-- fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <div class="pull-right">
        <button class="btn btn-info" type="button" id="leipi_save">保存设计</button>
      </div>

      <div class="nav-collapse collapse">
        <ul class="nav">
            <li><a href="javascript:void(0);">正在设计【<?php echo htmlentities($one['flow_name']); ?>】</a></li>
        </ul>
      </div>
      
    </div><!-- container -->
  </div>
</div>
<!-- end fixed navbar -->
<!-- attributeModal -->
<div id="attributeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:800px;margin-left:-350px">
  <div class="modal-body" style="max-height:500px;"><!-- body --></div>
  <div class="modal-footer" style="padding:5px;">
    <a href="http://www.leipi.org" target="_blank"><img src="http://formdesign.leipi.org/Public/images/leipi.png" alt="雷劈认证 icon" style="width:40px"></a>
    <!--a href="#" class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-white"></i></a-->
  </div>
</div>
<!--contextmenu div-->
<div id="processMenu" style="display:none;">
  <ul>
    <li id="pmAttribute"><i class="icon-cog"></i>&nbsp;<span class="_label">属性</span></li>
    <li id="pmForm"><i class="icon-th"></i>&nbsp;<span class="_label">表单字段</span></li>
    <li id="pmJudge"><i class="icon-share-alt"></i>&nbsp;<span class="_label">转出条件</span></li>
    <li id="pmSetting"><i class=" icon-wrench"></i>&nbsp;<span class="_label">样式</span></li>
    <li id="pmDelete"><i class="icon-trash"></i>&nbsp;<span class="_label">删除</span></li>
  </ul>
</div>
<div id="canvasMenu" style="display:none;">
  <ul>
    <li id="cmSave"><i class="icon-ok"></i>&nbsp;<span class="_label">保存设计</span></li>
    <li id="cmAdd"><i class="icon-plus"></i>&nbsp;<span class="_label">添加步骤</span></li>
    <li id="cmRefresh"><i class="icon-refresh"></i>&nbsp;<span class="_label">刷新</span></li>
    <li id="cmHelp"><i class="icon-search"></i>&nbsp;<span class="_label">帮助</span></li>
  </ul>
</div>
<!--end div--> 
<div class="container mini-layout" id="flowdesign_canvas">

</div> <!-- /container -->
<script type="text/javascript" src="/static/work/js/jquery-1.7.2.min.js?"></script>
<script type="text/javascript" src="/static/work/css/bootstrap/js/bootstrap.min.js?"></script>
<script type="text/javascript" src="/static/work/js/jquery-ui/jquery-ui-1.9.2-min.js?" ></script>
<script type="text/javascript" src="/static/work/js/jsPlumb/jquery.jsPlumb-1.3.16-all-min.js?"></script>
<script type="text/javascript" src="/static/work/js/jquery.contextmenu.r2.js?"></script>
<!--select 2-->
<script type="text/javascript" src="/static/work/js/jquery.multiselect2side/js/jquery.multiselect2side.js?" ></script>
<!--flowdesign-->
<script type="text/javascript" src="/static/work/js/flowdesign/leipi.flowdesign.v3.js?"></script>
<script type="text/javascript">
var the_flow_id ='<?php echo htmlentities($one['id']); ?>';
function callbackSuperDialog(selectValue){
     var aResult = selectValue.split('@leipi@');
     $('#'+window._viewField).val(aResult[0]);
     $('#'+window._hidField).val(aResult[1]);
    //document.getElementById(window._hidField).value = aResult[1];
    
}
/**
 * 弹出窗选择用户部门角色
 * showModalDialog 方式选择用户
 * URL 选择器地址
 * viewField 用来显示数据的ID
 * hidField 隐藏域数据ID
 * isOnly 是否只能选一条数据
 * dialogWidth * dialogHeight 弹出的窗口大小
 */
function superDialog(URL,viewField,hidField,isOnly,dialogWidth,dialogHeight)
{
    dialogWidth || (dialogWidth = 620)
    ,dialogHeight || (dialogHeight = 520)
    ,loc_x = 500
    ,loc_y = 40
    ,window._viewField = viewField
    ,window._hidField= hidField;
    // loc_x = document.body.scrollLeft+event.clientX-event.offsetX;
    //loc_y = document.body.scrollTop+event.clientY-event.offsetY;
    if(window.ActiveXObject){ //IE  
        var selectValue = window.showModalDialog(URL,self,"edge:raised;scroll:1;status:0;help:0;resizable:1;dialogWidth:"+dialogWidth+"px;dialogHeight:"+dialogHeight+"px;dialogTop:"+loc_y+"px;dialogLeft:"+loc_x+"px");
        if(selectValue){
            callbackSuperDialog(selectValue);
        }
    }else{  //非IE 
        var selectValue = window.open(URL, 'newwindow','height='+dialogHeight+',width='+dialogWidth+',top='+loc_y+',left='+loc_x+',toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no');  
    
    }
}
$(function(){
    var attributeModal =  $("#attributeModal");
    //属性设置
    attributeModal.on("hidden", function() {
        $(this).removeData("modal");//移除数据，防止缓存
    });
    ajaxModal = function(url,fn)
    {
        url += url.indexOf('?') ? '&' : '?';
        url += '_t='+ new Date().getTime();
        attributeModal.find(".modal-body").html('<img src="/Public/images/loading.gif"/>');
        attributeModal.modal({
            remote:url
        });
        //加载完成执行
        if(fn)
        {
            attributeModal.on('shown',fn);
        }
    }

    /*步骤数据*/
    var processData = <?php echo $process_data; ?>;
    /*创建流程设计器*/
    var _canvas = $("#flowdesign_canvas").Flowdesign({
                      "processData":processData
                      /*画面右键*/
                      ,canvasMenus:{
                        "cmAdd": function(t) {
                            var mLeft = $("#jqContextMenu").css("left"),mTop = $("#jqContextMenu").css("top");
                            var url = "<?php echo url('add_process'); ?>";
                            $.post(url,{"flow_id":the_flow_id,"left":mLeft,"top":mTop},function(data){
							
                                if(data.status==1)
                                {
									location.reload();
                                }else if(!_canvas.addProcess(data.info))//添加
                               {
									 layer.msg("添加失败");
                               }
                               
                            },'json');

                        },
                        "cmSave": function(t) {
                            var processInfo = _canvas.getProcessInfo();//连接信息
                            var url = "<?php echo url('save_canvas'); ?>";
                            $.post(url,{"flow_id":the_flow_id,"process_info":processInfo},function(data){
								layer.msg(data.msg);
                            },'json');
                        },
                        "cmRefresh":function(t){
                            location.reload();//_canvas.refresh();
                        },
                        "cmHelp": function(t) {
                           layer.msg("欢迎使用");
                        }
                       
                      }
                      /*步骤右键*/
                      ,processMenus: {
                          "pmDelete":function(t)
                          {
                              if(confirm("你确定删除步骤吗？"))
                              {
                                    var activeId = _canvas.getActiveId();//右键当前的ID
                                    var url = "<?php echo url('delete_process'); ?>";
                                    $.post(url,{"flow_id":the_flow_id,"process_id":activeId},function(data){
                                        if(data.status==1)
                                        {
                                            _canvas.delProcess(activeId);
                                            var processInfo = _canvas.getProcessInfo();//连接信息
                                            var url = "<?php echo url('save_canvas'); ?>";
                                            $.post(url,{"flow_id":the_flow_id,"process_info":processInfo},function(data){
                                                location.reload();
                                            },'json');
                                            
                                        }
                                        layer.msg(data.msg);
										
                                    },'json');
                              }
                          },
                          "pmAttribute":function(t)
                          {
                              var activeId = _canvas.getActiveId();//右键当前的ID
                              var url = "<?php echo url('attribute'); ?>?id="+activeId;
							   layer_show('编辑',url,'700','400');
                          },
                          "pmForm": function(t) {
                                var activeId = _canvas.getActiveId();//右键当前的ID
								var url = "<?php echo url('attribute'); ?>?op=form&id="+activeId;
                                layer_show('编辑',url,'700','400');
                          },
                          "pmJudge": function(t) {
                                var activeId = _canvas.getActiveId();//右键当前的ID
								var url = "<?php echo url('attribute'); ?>?op=judge&id="+activeId;
                                layer_show('编辑',url,'700','400');
                          },
                          "pmSetting": function(t) {
                                var activeId = _canvas.getActiveId();//右键当前的ID
								var url = "<?php echo url('attribute'); ?>?op=style&id="+activeId;
                                layer_show('编辑',url,'700','400');
                          }
                      }
                      ,fnRepeat:function(){
                        //alert("步骤连接重复1");//可使用 jquery ui 或其它方式提示
                        layer.msg("步骤连接重复了，请重新连接");
                        
                      }
                      ,fnClick:function(){
                          var activeId = _canvas.getActiveId();
						  layer.msg("查看步骤信息 " + activeId);
                      }
                      ,fnDbClick:function(){
                          //和 pmAttribute 一样
                          var activeId = _canvas.getActiveId();//右键当前的ID
                              var url = "<?php echo url('attribute'); ?>?id="+activeId;
							   layer_show('编辑',url,'700','400');
                      }
                  });
    /*保存*/
    $("#leipi_save").bind('click',function(){
        var processInfo = _canvas.getProcessInfo();//连接信息
        var url = "<?php echo url('save_canvas'); ?>";
        $.post(url,{"flow_id":the_flow_id,"process_info":processInfo},function(data){
			layer.msg(data.msg);
        },'json');
    });
});

 
</script>