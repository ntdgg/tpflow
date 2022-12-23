const ports = {
    groups: {
        top: {
            position: 'top', attrs: {
                circle: {
                    r: 4, magnet: true, stroke: '#D06269', strokeWidth: 1, fill: '#fff', style: {
                        visibility: 'hidden',
                    },
                },
            },
        }, right: {
            position: 'right', attrs: {
                circle: {
                    r: 4, magnet: true, stroke: '#D06269', strokeWidth: 1, fill: '#fff', style: {
                        visibility: 'hidden',
                    },
                },
            },
        }, bottom: {
            position: 'bottom', attrs: {
                circle: {
                    r: 4, magnet: true, stroke: '#D06269', strokeWidth: 1, fill: '#fff', style: {
                        visibility: 'hidden',
                    },
                },
            },
        }, left: {
            position: 'left', attrs: {
                circle: {
                    r: 4, magnet: true, stroke: '#D06269', strokeWidth: 1, fill: '#fff', style: {
                        visibility: 'hidden',
                    },
                },
            },
        },
    }, items: [{
        group: 'top', id: 't1'
    }, /* {
            group: 'right',
            id: 'r1'
        },*/
        {
            group: 'bottom', id: 'b1'
        }, /*  {
      group: 'left',
      id: 'l1'
  }*/],
}
// 起始节点
const start_data = {
    inherit: 'rect', width: 60, height: 45, attrs: {
        rect: {fill: '#fff', stroke: '#252B3A', strokeWidth: 2, rx: 15, ry: 15},
        text: {text: '开始', fill: 'black', fontSize: 13},
    }, data: 'node-start', ports: {
        ...ports, items: [{
            group: 'bottom', id: 'b1'
        }]
    }
}
const startNode = new Rect(start_data)
X6.Graph.registerNode('node-start', start_data);
// 流程节点
const flow_data = {
    inherit: 'rect', width: 65, height: 45,
    attrs: {
        body: {rx: 6, ry: 6, stroke: '#5F95FF', fill: '#fff', strokeWidth: 2},
        label: {fontSize: 12, fill: '#262626',},text: {text: '步骤', fill: 'black', fontSize: 13},
    }, data: 'node-flow', ports: {...ports}
}

const flowNode = new Rect(flow_data)
X6.Graph.registerNode('node-flow', flow_data);
// 判断节点
const gateway_node = {
    inherit: 'polygon', width: 65, height: 65, label: '网关', attrs: {
        body: {
            fill: '#FFF', stroke: 'rgb(255, 213, 145)', refPoints: '0,10 10,0 20,10 10,20',
        },
    }, data: 'node-gateway', ports: {...ports}
}
const judgeNode = new Polygon(gateway_node)
X6.Graph.registerNode('node-gateway', gateway_node);
// 链接节点


const msg_node = {
    inherit: 'rect', width: 50, height: 50, attrs: {
        body: {
            stroke: '#873bf4',
            fill: '#fff',
            rx: 15,
            ry: 15,
        },
        text: {text: '消息', fill: 'black', fontSize: 13},
    }, data: 'node-msg', ports: {...ports, items: [{
            group: 'top', id: 't1'
        }]}
}
const msgNode = new Rect(msg_node)
X6.Graph.registerNode('node-msg',msg_node);

const cc_node = {
    inherit: 'rect', width: 50, height: 50, attrs: {
        body: {
            stroke: '#47C769',
            fill: '#fff',
            rx: 0,
            ry: 0,
        },
        text: {text: '抄送', fill: 'black', fontSize: 13},
    }, data: 'node-cc', ports: {...ports, items: [{
            group: 'top', id: 't1'
        }]}
}
const ccNode = new Rect(cc_node)
X6.Graph.registerNode('node-cc',cc_node);


const end_node = {
    inherit: 'circle', width: 60, height: 60, label: '结束', attrs: {
        body: {
            fill: 'rgb(255,255,255)', stroke: 'rgb(241,17,17)',
        }
    }, data: 'node-end', ports: {
        ...ports, items: [{
            group: 'top', id: 't1'
        }]
    }
}
const linkNode = new Circle(end_node)
X6.Graph.registerNode('node-end', end_node);

const link_node = {
    inherit: 'edge', router: {
        name: 'manhattan'
    }, attrs: {
        line: {
            stroke: '#A2B1C3', strokeWidth: 2, targetMarker: {
                name: 'classic', size: 10,
            }
        }
    }, labels: [{
        attrs: {
            body: {
                stroke: '#5F95FF'
            }
        }
    }]
}

X6.Graph.registerEdge('link_node', link_node);