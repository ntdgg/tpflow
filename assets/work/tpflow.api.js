/*Tpflow6.0 版权所有 删除此版权将追究侵权责任*/
var TFAPI = {
    sApi : function(act) {
        var reload = false;
        switch(act) {
            case 'zoomIn':
                graph.zoom(0.1)
                return;
                break;
            case 'zoomOut':
                graph.zoom(-0.1)
                return;
                break;
            case 'save':
                var PostData = {"flow_id":Tpflow_Id,"process_info":JSON.stringify(graph.toJSON().cells)};//获取到步骤信息
                break;
            case 'delAll':
                if(!confirm("你确定清空全部吗？")){//delAll会自动保存，添加确认步骤更安全
                    return;
                }
                var PostData = {"flow_id":Tpflow_Id};
                reload = true;
                break;
            case 'att':
                return ;
                break;
            case 'add':
                //Tpflow.sPost(Server_Url+'?act=save',{"flow_id":Tpflow_Id,"process_info":Tpflow.GetJProcessData()},false);
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
}