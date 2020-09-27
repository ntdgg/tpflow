

//数据通用返回处理
function ajax_progress(data, callback, param) {
    if (data.code == 0) {
		layer.msg(data.msg,{icon:1,time: 1500},function(){
                parent.location.reload(); // 父页面刷新
        });          
    } else {
       layer.alert(data.msg, {title: "错误信息", icon: 2});
    }
}
/*弹出层*/
function layer_open(title,url,w,h){
	if (title == null || title == '') {
		title=false;
	};
	if (w == null || w == '') {
		w=800;
	};
	if (h == null || h == '') {
		h=($(window).height() - 50);
	};
	layer.open({
		type: 2,
		area: [w, h],
		fix: false, //不固定
		maxmin: true,
		shade:0.4,
		title: title,
		content: url
	});
}
/*关闭弹出框口*/
function layer_close(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
}