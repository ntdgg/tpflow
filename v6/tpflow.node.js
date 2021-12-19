const ports = {
    groups: {
        top: {
            position: 'top',
            attrs: {
                circle: {
                    r: 4,
                    magnet: true,
                    stroke: '#D06269',
                    strokeWidth: 1,
                    fill: '#fff',
                    style: {
                        visibility: 'hidden',
                    },
                },
            },
        },
        right: {
            position: 'right',
            attrs: {
                circle: {
                    r: 4,
                    magnet: true,
                    stroke: '#D06269',
                    strokeWidth: 1,
                    fill: '#fff',
                    style: {
                        visibility: 'hidden',
                    },
                },
            },
        },
        bottom: {
            position: 'bottom',
            attrs: {
                circle: {
                    r: 4,
                    magnet: true,
                    stroke: '#D06269',
                    strokeWidth: 1,
                    fill: '#fff',
                    style: {
                        visibility: 'hidden',
                    },
                },
            },
        },
        left: {
            position: 'left',
            attrs: {
                circle: {
                    r: 4,
                    magnet: true,
                    stroke: '#D06269',
                    strokeWidth: 1,
                    fill: '#fff',
                    style: {
                        visibility: 'hidden',
                    },
                },
            },
        },
    },
    items: [
        {
            group: 'top',
        },
        {
            group: 'right',
        },
        {
            group: 'bottom',
        },
        {
            group: 'left',
        },
    ],
}
// 起始节点
const start_data = {
    inherit: 'rect',
    width: 65,
    height: 40,
    attrs: {
        rect: {fill: 'rgba(230,237,244)', stroke: 'rgb(159,184,236)', strokeWidth: 2, rx: 25, ry: 25},
        text: {text: '开始', fill: 'black', fontSize: 13},
    },
    ports: {...ports}
}
const startNode = new Rect(start_data)
      X6.Graph.registerNode('node-start',  start_data);
// 流程节点
const flow_data ={
    inherit: 'rect',
    width: 60,
    height: 40,
    attrs: {
        rect: {fill: 'rgba(230,237,244)', stroke: 'rgb(159,184,236)', strokeWidth: 2},
        text: {text: '节点', fill: 'black', fontSize: 13},
    },
    ports: {...ports}
}
const flowNode = new Rect(flow_data)
      X6.Graph.registerNode('node-flow',  flow_data);
// 判断节点
const gateway_node= {
    inherit: 'polygon',
    width: 60,
    height: 60,
    label: '网关',
    attrs: {
    body: {
        fill: 'rgba(230,237,244)',
            stroke: 'rgb(159,184,236)',
            refPoints: '0,10 10,0 20,10 10,20',
    },
},
    ports: {...ports}
}

const judgeNode = new Polygon(gateway_node)
X6.Graph.registerNode('node-gateway',  gateway_node);
// 链接节点

const end_node = {
    inherit: 'circle',
    width: 60,
    height: 60,
    label: '结束',
    attrs: {
        body: {
            fill: 'rgba(230,237,244)',
            stroke: 'rgb(159,184,236)',
        }
    },
    ports: {...ports}
}
const linkNode = new Circle(end_node)
X6.Graph.registerNode('node-end',  end_node);
