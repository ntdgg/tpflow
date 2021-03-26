/*!
 * Tpflow Design 流程设计器 V5.0
 * http://cojz8.com
 *
 * 
 * Auth:Guoguo(632522043@qq.com)
 * http://cojz8.com
 *
 * Date: 2020年11月28日21:12:20
 */
var Tpflow = {
	lopen : function(title,url,w,h) {
		if (title == null || title == '') {
				title=false;
			};
			if (w == null || w == '') {
				w=($(window).width());
			};
			if (h == null || h == '') {
				h=($(window).height());
			};
			layer.open({
				type: 2,
				area: [w+'px', h+'px'],
				fix: false, //不固定
				maxmin: true,
				shade:0.4,
				title: title,
				content: url
			});
	},
	lclose : function(title,url,w,h) {
		var index = parent.layer.getFrameIndex(window.name);
		parent.layer.close(index);
	},
	common_return : function(data) {
		if (data.code == 0) {
			layer.msg(data.msg,{icon:1,time: 1500},function(){
					parent.location.reload(); // 父页面刷新
			});          
		} else {
		   layer.alert(data.msg, {title: "错误信息", icon: 2});
		}
	},
	Init : function(processData) {
		_this.append('<input type="hidden" id="wf_active_id" value="0"/>');
		_this.append('<div id="wf_process_info"></div>');
		  var aConnections = [];
		  var setConnections = function(conn, remove) {
			  if (!remove) aConnections.push(conn);
			  else {
				  var idx = -1;
				  for (var i = 0; i < aConnections.length; i++) {
					  if (aConnections[i] == conn) {
						  idx = i; break;
					  }
				  }
				  if (idx != -1) aConnections.splice(idx, 1);
			  }
			  if (aConnections.length > 0) {
				  var s = "";
				  for ( var j = 0; j < aConnections.length; j++ ) {
					  var from = $('#'+aConnections[j].sourceId).attr('process_id');
					  var target = $('#'+aConnections[j].targetId).attr('process_id');
					  s = s + "<input type='hidden' value=\"" + from + "," + target + "\">";
				  }
				  $('#wf_process_info').html(s);
			  } else {
				  $('#wf_process_info').html('');
			  }
			  jsPlumb.repaintEverything();//重画
		  };
		var initEndPoints = function(){
		  $(".process-flag").each(function(i,e) {
			  var p = $(e).parent();
			  jsPlumb.makeSource($(e), {
				  parent:p,
				  anchor:"Continuous",
				  endpoint:[ "Dot", { radius:1 } ],
				  connector:[ "Flowchart", { stub:[5, 5] } ],
				  connectorStyle:{lineWidth:3,strokeStyle:"#49afcd",joinstyle:"round"},
				  hoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"},
				  dragOptions:{},
				  maxConnections:-1
			  });
		  });
		}
		jsPlumb.importDefaults({
            DragOptions : { cursor: 'pointer'},
            EndpointStyle : { fillStyle:'#225588' },
            Endpoint : [ "Dot", {radius:1} ],
            ConnectionOverlays : [
                [ "Arrow", { location:1 } ],
                [ "Label", {location:0.1,id:"label",cssClass:"aLabel"}]
            ],
            Anchor : 'Continuous',
            ConnectorZIndex:5,
            HoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"}
        });
        if( $.browser.msie && $.browser.version < '9.0' ){ //ie9以下，用VML画图
            jsPlumb.setRenderMode(jsPlumb.VML);
        } else { //其他浏览器用SVG
            jsPlumb.setRenderMode(jsPlumb.SVG);
        }
	 var lastProcessId=0;
	 $.each(processData.list, function(i,row) {
            var nodeDiv = document.createElement('div');
			 var nodeId = "window" + row.id;
            $(nodeDiv).attr("id",nodeId)
            .attr("style",row.style +'text-align: left;')
            .attr("process_to",row.process_to)
            .attr("process_id",row.id)
            .addClass("process-step wf_btn")
            .html('<span class="process-flag" style="text-align: left;"><img src="'+Tpflow.Ico()+'" width=18px></span>&nbsp;' +row.process_name + '<br/><img src="'+Tpflow.Ico(1)+'" width=18px>&nbsp;' +row.mode + '<br/><img src="'+Tpflow.Ico(2)+'" width=18px>&nbsp;' +row.name + '' );
            _this.append(nodeDiv);
            lastProcessId = row.id;
        });
	 var timeout = null;
    //点击或双击事件,这里进行了一个单击事件延迟，因为同时绑定了双击事件
	$(".process-step").live('click',function(){
        _this.find('#wf_active_id').val($(this).attr("process_id")),
        clearTimeout(timeout);
        var obj = this;
		Tpflow.DClick($(this).attr("process_id"));
        timeout = setTimeout(Tpflow.Click(),300);
    }).live('dblclick',function(){
        clearTimeout(timeout);
		if(confirm("你确定删除步骤吗？")){
			var activeId = _this.find("#wf_active_id").val();//右键当前的ID
			$.post(Server_Url+'?act=del',{"flow_id":the_flow_id,"id":activeId},function(data){
				if(data.code==0){
					 if(activeId>0){
						$("#window"+activeId).remove();
					 }
					Tpflow.Api('save');
				}
				layer.msg(data.msg);
				
			},'json');
		 }
    });
	 jsPlumb.draggable(jsPlumb.getSelector(".process-step"),{containment: 'parent'});//允许拖动
	 initEndPoints();
	 jsPlumb.bind("jsPlumbConnection", function(info) {
        setConnections(info.connection)
    });
    //绑定删除connection事件
    jsPlumb.bind("jsPlumbConnectionDetached", function(info) {
       setConnections(info.connection, true);
    });
    //绑定删除确认操作
    jsPlumb.bind("click", function(c) {
      if(confirm("你确定取消连接吗?"))
        jsPlumb.detach(c);
    });
    //连接成功回调函数
    function mtAfterDrop(params){
        defaults.mtAfterDrop({sourceId:$("#"+params.sourceId).attr('process_id'),targetId:$("#"+params.targetId).attr('process_id')});
    }
    jsPlumb.makeTarget(jsPlumb.getSelector(".process-step"), {
        dropOptions:{ hoverClass:"hover", activeClass:"active" },
        anchor:"Continuous",
        maxConnections:-1,
        endpoint:[ "Dot", { radius:1 } ],
        paintStyle:{ fillStyle:"#ec912a",radius:1 },
        hoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"},
        beforeDrop:function(params){
            if(params.sourceId == params.targetId) return false;/*不能链接自己*/
            var j = 0;
            $('#wf_process_info').find('input').each(function(i){
                var str = $('#' + params.sourceId).attr('process_id') + ',' + $('#' + params.targetId).attr('process_id');
                if(str == $(this).val()){
                    j++;
                    return;
                }
            })
            if( j > 0 ){
                defaults.fnRepeat();
                return false;
            } else {
                return true;
            }
        }
    });
	$('.process-step').each(function(i){
            var sourceId = $(this).attr('process_id');
            var prcsto = $(this).attr('process_to');
            var toArr = prcsto.split(",");
            $.each(toArr,function(j,targetId){
                if(targetId!='' && targetId!=0){
                    //检查 source 和 target是否存在
                    var is_source = false,is_target = false;
                    $.each(processData.list, function(i,row){
                        if(row.id == sourceId){
                            is_source = true;
                        }else if(row.id == targetId){
                            is_target = true;
                        }
                        if(is_source && is_target)
                            return true;
                    });
                    if(is_source && is_target){
                        jsPlumb.connect({
                            source:"window"+sourceId, 
                            target:"window"+targetId,
							overlays: [["Label", {cssClass: "component label",label: sourceId+" - "+targetId,}],"Arrow"]
                        });
                        return ;
                    }
                }
            })
			
        });
	},
	show : function(processData) {
		_this.append('<input type="hidden" id="wf_active_id" value="0"/>');
		_this.append('<div id="wf_process_info"></div>');
		 
		var initEndPoints = function(){
		  $(".process-flag").each(function(i,e) {
			  var p = $(e).parent();
			  jsPlumb.makeSource($(e), {
				  parent:p,
				  anchor:"Continuous",
				  endpoint:[ "Dot", { radius:1 } ],
				  connector:[ "Flowchart", { stub:[5, 5] } ],
				  connectorStyle:{lineWidth:3,strokeStyle:"#49afcd",joinstyle:"round"},
				  hoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"},
				  dragOptions:{},
				  maxConnections:-1
			  });
		  });
		}
		jsPlumb.importDefaults({
            DragOptions : { cursor: 'pointer'},
            EndpointStyle : { fillStyle:'#225588' },
            Endpoint : [ "Dot", {radius:1} ],
            ConnectionOverlays : [
                [ "Arrow", { location:1 } ],
                [ "Label", {location:0.1,id:"label",cssClass:"aLabel"}]
            ],
            Anchor : 'Continuous',
            ConnectorZIndex:5,
            HoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"}
        });
        if( $.browser.msie && $.browser.version < '9.0' ){ //ie9以下，用VML画图
            jsPlumb.setRenderMode(jsPlumb.VML);
        } else { //其他浏览器用SVG
            jsPlumb.setRenderMode(jsPlumb.SVG);
        }
	 var lastProcessId=0;
	 $.each(processData.list, function(i,row) {
            var nodeDiv = document.createElement('div');
			 var nodeId = "window" + row.id;
            $(nodeDiv).attr("id",nodeId)
            .attr("style",row.style +'text-align: left;')
            .attr("process_to",row.process_to)
            .attr("process_id",row.id)
            .addClass("process-step wf_btn")
            .html('<span class="process-flag" style="text-align: left;"><img src="'+Tpflow.Ico()+'" width=18px></span>&nbsp;' +row.process_name + '<br/><img src="'+Tpflow.Ico(1)+'" width=18px>&nbsp;' +row.mode + '<br/><img src="'+Tpflow.Ico(2)+'" width=18px>&nbsp;' +row.name + '' );
            _this.append(nodeDiv);
            lastProcessId = row.id;
        });
		initEndPoints();
	$('.process-step').each(function(i){
            var sourceId = $(this).attr('process_id');
            var prcsto = $(this).attr('process_to');
            var toArr = prcsto.split(",");
            $.each(toArr,function(j,targetId){
                if(targetId!='' && targetId!=0){
                    //检查 source 和 target是否存在
                    var is_source = false,is_target = false;
                    $.each(processData.list, function(i,row){
                        if(row.id == sourceId){
                            is_source = true;
                        }else if(row.id == targetId){
                            is_target = true;
                        }
                        if(is_source && is_target)
                            return true;
                    });
                    if(is_source && is_target){
                        jsPlumb.connect({
                            source:"window"+sourceId, 
                            target:"window"+targetId,
							overlays: [["Label", {cssClass: "component label",label: sourceId+" - "+targetId,}],"Arrow"]
                        });
                        return ;
                    }
                }
            })
			
        });
	},
	
	DClick : function(id) {
		var url = Server_Url+"?id="+id+"&act=att";
		$('#iframepage').attr('src',url);
	},
	Api : function(Action) {
		var reload = false;
		switch(Action) {    
			case 'save':
				var PostData = {"flow_id":the_flow_id,"process_info":Tpflow.GetJProcessData()};//获取到步骤信息
				break;
			case 'delAll':
				var PostData = {"flow_id":the_flow_id};
				reload = true;
				break;
			case 'att':
				
				return ;
				break;
			case 'add':
				var PostData = {"flow_id":the_flow_id};
				reload = true;
				break;
			case 'check':
				var PostData = {"flow_id":the_flow_id};
				break;
			case 'Refresh':
				location.reload();return;
				break;
			case 'Help':
				layer.open({
					  type: 2,
					  title: '工作流官网',
					  shadeClose: true,
					  shade: false,
					  maxmin: true, //开启最大化最小化按钮
					  area: ['893px', '600px'],
					  content: '//cojz8.com/'
				});
				break;
			 default:
				
		} 
		var Url = Server_Url+'?act='+Action;
		Tpflow.sPost(Url,PostData,reload);
	},
    wfconfirm : function(url,data,msg) {
		layer.confirm(msg, {
			  btn: ['执行','取消'] //按钮
		}, function(){
			  Tpflow.sPost(url,data);
		}, function(){
			  layer.msg('取消操作', {
				time: 2000, //20s后自动关闭
			  });
		});
	},
	sPost : function(Post_Url,PostData,reload=true) {
		$.post(Post_Url,PostData,function(data){
			if(data.code==0){
				layer.msg(data.msg,{icon:1,time: 1500},function(){
					if(reload){
						location.reload();
					}
				}); 
			}else{
				layer.msg(data.msg, {
				time: 2000, //20s后自动关闭
			  });
				
			}
		},'json');
	},
	DelJProcessData : function(){
		if(confirm("你确定删除步骤吗？")){
			var activeId = _this.find("#wf_active_id").val();//右键当前的ID
			$.post(Server_Url+'?act=del',{"flow_id":the_flow_id,"id":activeId},function(data){
				if(data.status==1){
					 if(activeId>0){
						$("#window"+activeId).remove();
					 }
					Tpflow.Api('save');
				}
				layer.msg(data.msg);
			},'json');
		 }
	},
	Click : function(){
		return 123;
	},
	GetJProcessData : function(){
		try{
              var aProcessData = {};
              $("#wf_process_info input[type=hidden]").each(function(i){
                  var processVal = $(this).val().split(",");
                  if(processVal.length==2)
                  {
                    if(!aProcessData[processVal[0]])
                    {
                        aProcessData[processVal[0]] = {"top":0,"left":0,"process_to":[]};
                    }
                    aProcessData[processVal[0]]["process_to"].push(processVal[1]);
                  }
              })
              _this.find("div.process-step").each(function(i){ //生成Json字符串，发送到服务器解析
                      if($(this).attr('id')){
                          var pId = $(this).attr('process_id');
                          var pLeft = parseInt($(this).css('left'));
                          var pTop = parseInt($(this).css('top'));
                         if(!aProcessData[pId])
                          {
                              aProcessData[pId] = {"top":0,"left":0,"process_to":[]};
                          }
                          aProcessData[pId]["top"] =pTop;
                          aProcessData[pId]["left"] =pLeft;
                      }
                  })
            return JSON.stringify(aProcessData);
          }catch(e){
              return '';
          }
	},
	tabchange : function(obj,value) {
		$(obj).attr("class","choice")
		$(obj).siblings().attr("class","")
		$("#box").find("div:eq("+value+")").attr("class","show")
		$("#box").find("div:eq("+value+")").siblings().attr("class","tab-item")
	},
	onchange : function(obj,type) {
		var apid = $(obj).val();
		if(type=='auto_person'){
			$("#range_user_ids").removeAttr("datatype");
			$("#auto_sponsor_ids").removeAttr("datatype");
			$("#auto_role_ids").removeAttr("datatype");
			if(apid==3){
				 $("#range_user_ids").attr({datatype:"*",nullmsg:"请选择办理人员1"});
			}
			if(apid==4){
				$("#auto_sponsor_ids").attr({datatype:"*",nullmsg:"请选择办理人员2"});
			}
			if(apid==5){
				$("#auto_role_ids").attr({datatype:"*",nullmsg:"请选择办理角色3"});
			}
			$(".auto_person").hide();
			$("#auto_person_"+apid).show();
		}else{
			if(apid==1){
				$("#wf_mode_2").show();
			}else{
				$("#wf_mode_2").hide();
			}
		}
	},
	SetHeight : function() {
		var ifm= document.getElementById("iframepage");   
		var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;   
		if(ifm != null && subWeb != null) {
		   ifm.height = '100%';
		   ifm.width = '100%';
		}   
	},
	Ico : function(id=0) {
		var data = ['data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAQAAABKfvVzAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfbBRIXBAD72CbZAAADCElEQVQ4y13US2xVZRSG4effe7elFGhpoVWgUArtQe5XkUg0BC8hkagQExPjAAIDB4wcOXFg4tihA2Vk1IEaBt5RxEaDYmtTqglIa0hpS1qIRWiF9pzu8zvoCRi/2cq6fMmbtVZQ0UGoVbDDPgUrBCMGdPklXtp751WZALJKKaxzxBOxPaSVuFOnA4bCmdmPs4szN09rskNWSdY57FjcVhOabUlXpU2BiTicX8jH1jR2HHp28puBN9PhkTkH1HnFidi0NjxVtTtbGKoqU0pVU7FvtikptDu+suHaib03SA/CS15LFj+SHqvZldWGVMkd0xLVasPqtCUJ8tDd9snohp5haYG1Xrd2T3K0ZkUaRcGfTjlvvmUiAnpnT8axpd/97Eai2svl7e1erG5Joku63HXHkCtuyZ3Tq4wHksXRNkfVZtY5UJPuz9pSBp1y3aQmqUTJDz5TJ7HViuTpqqFi8UkfZLbFjua4J4OoqKRLs5Kgx5S7MmUBu7LPi1fa7E7sC+nGtD6ZA/+C5WaMmBGNmdTokB1gYdicCvZlNtORVqHPNdE8iVjBGtQa94VGO2U6UiVbMi3UB+jRLUgl7mvUVbmCLTKLAlqy+8kFGiVm3b3nwHzVcosqezS3S+MW3orwqI1mnDPwH4elHrPAfNW4HTGe6GcwL6FVwd9GlAUBQTRuXLt2iVmDOfoTXTH/LZ/z+N3XpjTYoFai04Omfa9HxGS8kIvOJn4NA9fDTyVYbo0Gz9gpKFvvOc1WahPQPTvKkPOZS76cWXfGprQtbfa8CQX9FagbzFOtFSPl06VpybcupoXcSNh1c/mtcke6INRZIigJlum0RKN6/FX+sNhbDn3ecC0tMIE9ozXXy61JQwL1HrLJkgqp4fJ7Mz/m5X+85SvSywoMyOPm4drBnMYkC0mFe8lkPDf7frE3xtve9q7ip0Llpuscdjxur4kttqat6dLARLya9+VjpkO44B0fmeJ+A6x3xP64+t4TgLKrzjpZ4TDX8L8387DHdVolGHHZWd3+mJs9p38BQHkS326FepQAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTgtMDYtMjhUMjI6Mzg6MjQrMDg6MDCBECHGAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDExLTA1LTE4VDIzOjA0OjAwKzA4OjAwRsh68wAAAEN0RVh0c29mdHdhcmUAL3Vzci9sb2NhbC9pbWFnZW1hZ2ljay9zaGFyZS9kb2MvSW1hZ2VNYWdpY2stNy8vaW5kZXguaHRtbL21eQoAAAAYdEVYdFRodW1iOjpEb2N1bWVudDo6UGFnZXMAMaf/uy8AAAAXdEVYdFRodW1iOjpJbWFnZTo6SGVpZ2h0ADMyKPT49AAAABZ0RVh0VGh1bWI6OkltYWdlOjpXaWR0aAAzMtBbOHkAAAAZdEVYdFRodW1iOjpNaW1ldHlwZQBpbWFnZS9wbmc/slZOAAAAF3RFWHRUaHVtYjo6TVRpbWUAMTMwNTczMTA0MJzV/YIAAAARdEVYdFRodW1iOjpTaXplADEzMzhCRu5XfwAAAGB0RVh0VGh1bWI6OlVSSQBmaWxlOi8vL2hvbWUvd3d3cm9vdC9uZXdzaXRlL3d3dy5lYXN5aWNvbi5uZXQvY2RuLWltZy5lYXN5aWNvbi5jbi9zcmMvNTA0Ny81MDQ3NTMucG5nxUdQRAAAAABJRU5ErkJggg==','data:image/png;base64,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eJWkknqeCfn8YZt9h27yjSowQmiHpKBpTZtzGCy7U18ibfIBwTI69UfaJx6DFiPW/xb/b/gP8zuk/2nab5kAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTktMDEtMDlUMDM6NTg6MTMrMDg6MDA5NyEdAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE5LTAxLTA5VDAzOjU4OjEzKzA4OjAwSGqZoQAAAEN0RVh0c29mdHdhcmUAL3Vzci9sb2NhbC9pbWFnZW1hZ2ljay9zaGFyZS9kb2MvSW1hZ2VNYWdpY2stNy8vaW5kZXguaHRtbL21eQoAAABjdEVYdHN2Zzpjb21tZW50ACBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIM5IkAsAAAAYdEVYdFRodW1iOjpEb2N1bWVudDo6UGFnZXMAMaf/uy8AAAAYdEVYdFRodW1iOjpJbWFnZTo6SGVpZ2h0ADU1MXLoxz8AAAAXdEVYdFRodW1iOjpJbWFnZTo6V2lkdGgANTUx4RmXYgAAABl0RVh0VGh1bWI6Ok1pbWV0eXBlAGltYWdlL3BuZz+yVk4AAAAXdEVYdFRodW1iOjpNVGltZQAxNTQ2OTc3NDkzmyKPKAAAABJ0RVh0VGh1bWI6OlNpemUAMzE4NjFCZKk/ngAAAGJ0RVh0VGh1bWI6OlVSSQBmaWxlOi8vL2hvbWUvd3d3cm9vdC9uZXdzaXRlL3d3dy5lYXN5aWNvbi5uZXQvY2RuLWltZy5lYXN5aWNvbi5jbi9maWxlcy8xMjAvMTIwMTMzNy5wbmfCdaGGAAAAAElFTkSuQmCC','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAxCAYAAACcXioiAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QAAAAAAAD5Q7t/AAAACXBIWXMAAAD9AAAA/QCVfLMFAAAAB3RJTUUH4wEJAzcWevkOigAABRFJREFUaN7N2mmoVVUUB/DfUx9laaXNIRGVaVHUS3vSZJQRkUhzFg00CFGZUV8qkigriqBIKaPpQ0WQDahpHyKTRtMnZfNoijaYCmVmmaXPPqx95XA69/nuufe+14LLhXPu3vv/33udtf5rnduiwXbdxEld3n/k4WkNXa+lycBb0/c/zSJTN4Ec8L4YjhNxNPZL11bjU7yDj7GpUSTqIpADfziux1kYgFUJ+Fbskch0Yh4ewruNIFGaQAZ8C8bjXuyKF/AivsBvicAAHIxxuBT9cTcekdyrLIm+dYKHK/EwvsMEPIpl2JDAbcaf+AFv4HUMxdX4G+9ja3v7KIs7FtWMpV/ZE0h2Ku5HRwK/onIjv6MZ0p/hikR6Mr7GzLIAanahDJDBmI29cSa+rAa+YCzsjzniFMZiTVdjq1mfsszTosfgge6Ar9zL3F+JB3EETi8LoiyBVpwjfH12d8BXsdeE241V8nksS2BPHIkF+LlW8JnfrsGHIgQPYvuZvC4Cmcn3SAt+Wcv4AuvE8jTf4DITlDmBVlyI3bCeurPpqkRgctqUphMYgWswA6+WRZ0h/TymiWQ4uicIDMWOeAI/lSWQsbUic28RcqPpBLYI30XD5PFOQnKs6wkCa9P3XtQeNarYIWlTvu8JAivwO0ZWLpQhkRnTD2PwI77tCQIrReweJ+J3PdaKy0UmniPkd9MJ/IXHhAs9jQOp7RQyvx0tpMhHac6arWwmnoMbcJgQcmXtDCHmJmIpTRZzmck7hQT+SGiiXejeKWR+s7vw/Q5R/JSymk8gQ2K9iN8jcEKJtY/DMMyVauQyIbkeOQ2zxIM3ATvQ9Slk7rWK0nK1UKSlrZSEXdyxSHv7KPhVhMEJIgwugaLyMEfsPNyEqXiF8gmx3hOAp7AQd4gC5z+Ac+APw13C75+sd/HSBDI7tk4oyRZR0B9dBTi04XGhOicrWUY2hEDOFmIS9hXq8vgCEseKh/4g0T+a14iF6yKQ27mZuFiE1GsL5r5AFC2XCileNEfPEiiwb0Q/qMi2pE/NcqFpBHI+vjNuxRBR6HTmfj5XhM+7hasVzVGz1dMXytqhuB3nigf5FmwkXCSN6Suek3uwGLfhPVEHbLNaXapbBKqA3kn0O88UeWCgEGZTJTeqgMlJ54sxRfRLn8VzIqT+kV+gO2S6JFAAfKBop5ySPkeIDDw/gX+7sqNdtBalcTfi7IThE9E3nS/a7+u7S6SQQAHwIaJtPh5HiYbtF3hTNGs7RAO3ywVz87aKvHCGEHWHpxP6BC8LmbJse0RatrPIbrhMdCEOSLszS8TwrxREnO4ce8EGDRDCboxwyTbRMHtSZPrV1eZv6WLio3AfThYvI6aLY15XBnQ3iRB55ETRfj8NH+BmVV6ItFSZ7CSR8ncVYe8ZNfhlg8j0x/lCY+0gip5tbfjK+kXvB0aKY9ssfP6tZgLPz5shsjFt3OdpM6fjlzyeltygwUKvDE/gG/Ieq4zlTqQt4VorgsnqCqZ8Jr5ICLEpvQm+YM0luDMRuSR7I0tgkGhxLBaKstfAV1l7pmjnXyLq6SCQOaoRotiYId4u9ir4AhIb8JLo4rURbpY9gRHireKC3gbdhS1KGNsqF7JRaJjo1S+vXGhQ37ORtkJUcUPzBPoInbMLrhLdt4b9j6JBtlXkhoFCIfRBZ5bAUuwjep7/N/BZEkuFRuqbJbBZZLxGV2jNsk7pLwrZZ2BTubl61/4FsR6JKpzlaNkAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTktMDEtMDlUMDM6NTU6MjIrMDg6MDDkMab6AAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE5LTAxLTA5VDAzOjU1OjIyKzA4OjAwlWweRgAAAEN0RVh0c29mdHdhcmUAL3Vzci9sb2NhbC9pbWFnZW1hZ2ljay9zaGFyZS9kb2MvSW1hZ2VNYWdpY2stNy8vaW5kZXguaHRtbL21eQoAAABTdEVYdHN2Zzpjb21tZW50ACBHZW5lcmF0b3I6IFNrZXRjaCAzLjAuMyAoNzg5MSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggbh/q1QAAABB0RVh0c3ZnOnRpdGxlAFBlcnNvbjwcGLsAAAAYdEVYdFRodW1iOjpEb2N1bWVudDo6UGFnZXMAMaf/uy8AAAAYdEVYdFRodW1iOjpJbWFnZTo6SGVpZ2h0ADI0NvDYdVgAAAAXdEVYdFRodW1iOjpJbWFnZTo6V2lkdGgAMjQx/U2wpgAAABl0RVh0VGh1bWI6Ok1pbWV0eXBlAGltYWdlL3BuZz+yVk4AAAAXdEVYdFRodW1iOjpNVGltZQAxNTQ2OTc3MzIyCp5w8AAAABF0RVh0VGh1bWI6OlNpemUANjg1N0Kg2mwTAAAAYnRFWHRUaHVtYjo6VVJJAGZpbGU6Ly8vaG9tZS93d3dyb290L25ld3NpdGUvd3d3LmVhc3lpY29uLm5ldC9jZG4taW1nLmVhc3lpY29uLmNuL2ZpbGVzLzExOS8xMTk4NTgxLnBuZ+cNqJ8AAAAASUVORK5CYII='];
		return data[id];
	}
}