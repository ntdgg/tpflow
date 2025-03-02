/*Tpflow6.0 版权所有 删除此版权将追究侵权责任*/
var TFAPI = {
    sApi : function(act) {
        var reload = false;
        switch(act) {
            case 'zoomIn':
                graph.zoom(-0.1)
                graph.centerContent()
                return;
                break;
            case 'zoomOut':
                graph.centerContent()
                graph.zoom(0.1)
                return;
                break;
            case 'save':
                var PostData = {"flow_id":Tpflow_Id,"process_info":JSON.stringify(graph.toJSON().cells)};//获取到步骤信息
                break;
            case 'delAll':
                if(!confirm("你确定清空全部节点吗？")){//delAll会自动保存，添加确认步骤更安全
                    return;
                }
                var PostData = {"flow_id":Tpflow_Id};
                reload = true;
                break;
            case 'add':
                var PostData = {"flow_id":Tpflow_Id};
                reload = true;
                break;
            case 'check':
                var PostData = {"flow_id":Tpflow_Id};
                break;
            case 'Refresh':
                location.reload();return;
                break;
            case 'Help':
                window.open("//www.cojz8.com/");
                break;
            case 'Doc':
                window.open("//gadmin8.com/index/product.html");
                break;
            default:
                window.open("//gitee.com/ntdgg/tpflow");

        }

        var Url = Tpflow_Server_Url+'?act='+act;
        TFAPI.sPost(Url,PostData,reload);
    },
    lopen : function(title,url,w,h) {
        if (title == null || title === '') {
            title=false;
        }
        if (w === null || w === '') {
            w=($(window).width());
        }
        if (h === null || h === '') {
            h=($(window).height());
        }
        layer.open({
            type: 2,
            area: [w+'%', h+'%'],
            fix: false, //不固定
            maxmin: true,
            shade:0.4,
            title: title,
            content: url
        });
    },
    attset:function(url){
        layer.open({
            type: 2,
            title: '流程节点设置',
            offset: 'r',
            anim: 'slideLeft', // 从右往左
            area: ['720px', '100%'],
            shade: 0.1,
            shadeClose: true,
            id: 'flow-att',
            content: url
        });
    },
    sReload:function(){
        var Url = Tpflow_Server_Url+'?act=nodejson&flow_id='+Tpflow_Id;
        $.post(Url,{},function(ret){
            graph.fromJSON(ret.x6);
        },'json');
    },
    sPost : function(Post_Url,PostData,reload=true) {
        $.post(Post_Url,PostData,function(data){
            if(data.code==0){
                layer.msg(data.msg,{time: 1000},function(){
                    TFAPI.sReload();return;
                });
            }else{
                layer.msg(data.msg, {icon:2,
                    time: 2000, //20s后自动关闭
                });
            }
        },'json');
    },
    sAdd:function(act,id){
        TFAPI.sPost(Tpflow_Server_Url+'?act=quilklink',{"flow_id":Tpflow_Id,"process_id":id,"fun":act},true);
    }
}