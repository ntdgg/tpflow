const {Stencil} = X6.Addon
const {Rect, Circle, Polygon, Image, Cylinder} = X6.Shape
const {DataUri} = X6.Addon

const graph = new X6.Graph({
    container: document.getElementById("main"),
    // 定义网格样式
    grid: {
        size: 10,
        visible: true,
        type: 'doubleMesh',
        args: [
            {
                color: '#E7E8EA',
                thickness: 1,
            },
            {
                color: '#CBCED3',
                thickness: 1,
                factor: 5,
            },
        ],
    },
    // 对齐线
    snapline: {
        enabled: true,
        sharp: true,
    },
    anchor: 'center',
    scroller: {
        enabled: true,
        pageVisible: false,
        pageBreak: false,
        pannable: true,
    },
    width: '1000',
    height: '800',
    panning: {
        enabled: true,
        eventTypes: ['leftMouseDown', 'rightMouseDown', 'mouseWheel'],
        modifiers: 'ctrl',
    },
    mousewheel: {
        enabled: true,
        zoomAtMousePosition: true,
        modifiers: 'ctrl',
        minScale: 0.5,
        maxScale: 3,
    },
    selecting: {
        enabled: true,
        rubberband: true,
        showNodeSelectionBox: true,
    },
    connecting: {
        router: 'manhattan',
        connector: {
            name: 'rounded',
            args: {
                radius: 8,
            },
        },
        anchor: 'center',
        connectionPoint: 'anchor',
        allowBlank: false,
        snap: true,
        createEdge() {
            return new X6.Shape.Edge({
                attrs: {
                    line: {
                        stroke: '#A2B1C3',
                        strokeWidth: 2,
                        targetMarker: {
                            name: 'block',
                            width: 12,
                            height: 8,
                        },
                    },
                },
                zIndex: 0,
            })
        },
        validateConnection({ targetMagnet }) {
            return !!targetMagnet
        }
    },
    highlighting: {
        magnetAdsorbed: {
            name: 'stroke',
            args: {
                attrs: {
                    fill: '#D06269',
                    stroke: '#D06269',
                },
            },
        },
    },
    resizing: true,
    rotating: true,
    keyboard: true,
    history: true,
    clipboard: true,
    minimap: {
        enabled: true,
        container: document.getElementById('minimap'),
        width: 198,
        height: 198,
        padding: 10,
    }
})

// 画布居中
graph.centerContent()

// 创建侧边栏
const stencil = new Stencil({
    title: '流程步骤',
    target: graph,
    stencilGraphWidth: 120,
    stencilGraphHeight: document.body.offsetHeight - 105,
    layoutOptions: {
        columns: 1,
        columnWidth: 80,
        rowHeight: 80,
        marginY: 20,
    }
})
// 拿到侧边栏实例
const stencilContainer = document.getElementById("sidebar")

// 渲染dom
stencilContainer.appendChild(stencil.container)

// 隐藏、显示节点
const showPorts = (ports, visible) => {
    for (let i = 0, len = ports.length; i < len; i = i + 1) {
        ports[i].style.visibility = visible ? 'visible' : 'hidden'
    }
}

// 初始化事件
const initEvents = (domName) => {
    const container = document.getElementById(domName)
    /*节点添加保存事件*/
    graph.on('node:added', ({ node, index, options }) => {
        const all_data = graph.toJSON().cells;
        var s_num = 0;
        $.each(all_data,function(index,obj){  //index:索引obj:循环的每个元素
            if(obj.shape==='node-start' || obj.data==='node-start'){
                s_num=s_num + 1
            }
        });
        if(s_num >=2){
            layer.msg('对不起，禁止添加两个起始节点，系统正在刷新！', function(){
                TFAPI.sReload();
            });
            return;
        }
        const xy = node.position()
        const wh = node.size()
        TFAPI.sApi('save');
        TFAPI.sPost(Tpflow_Server_Url+'?act=add',{"flow_id":Tpflow_Id,'data':{'process_name':node.attrs.text.text,'style':'{"width":'+wh.width+',"height":'+wh.height+',"color":"#2d6dcc"}','process_type':node.data,'setleft':xy.y,'settop':xy.x}});
    })

    /*节点鼠标移入事件*/
    graph.on('node:mouseenter', ({node}) => {
        const ports = container.querySelectorAll(
            '.x6-port-body',
        )
        showPorts(ports, true)
        node.addTools({
            name: 'button-remove',
            args: {
                offset: {
                    x: 35,
                    y: 15
                },
                onClick({ view }) {
                    if(confirm("你确定删除步骤吗？")) {
                        TFAPI.sPost(Tpflow_Server_Url+'?act=del',{"flow_id":Tpflow_Id,"id":node.data});
                    }
                },
            },
        })

    })
    graph.on('node:mouseleave', ({node}) => {
        const ports = container.querySelectorAll(
            '.x6-port-body',
        )
        showPorts(ports, false)
        node.removeTools()
    })

    /*连接线鼠标移入事件*/
    graph.on('edge:mouseenter', ({edge}) => {
        edge.addTools({
            name: 'button-remove',
            args: {
                x: 0,
                y: 0,
                offset: {
                    x: 0,
                    y: 0
                },
            },
        })
    })
    graph.on('edge:connected', ({ isNew, edge }) => {
        TFAPI.sApi('save');
    })
    graph.on('edge:removed', ({ isNew, edge }) => {
        TFAPI.sApi('save');
    })
    graph.on('edge:mouseleave', ({edge}) => {
        edge.removeTools()
    })
    /*双击节点事件*/
    graph.on('node:dblclick', ({e, x, y, node, view}) => {
        if(node.shape=='node-end'){
            layer.msg('结束节点');return;
        }
        var url = Tpflow_Server_Url + "?id=" + node.data + "&act=att";
        TFAPI.lopen("属性设计", url, 50, 60);
    })

}
const initKeyboard = () => {
    graph.bindKey(['ctrl+1', 'meta+1'], () => {
        const zoom = graph.zoom()
        if (zoom < 1.5) {
            graph.zoom(0.1)
        }
    })
}
// 初始化事件
initEvents('main')
// 初始化快捷键
initKeyboard()
