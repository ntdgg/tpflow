const {Stencil} = X6.Addon
const {Rect, Circle, Polygon} = X6.Shape

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
        snap: {
            radius: 20,
        },
        createEdge() {
            return new X6.Shape.Edge({
                attrs: {
                    line: {
                        stroke: '#000',
                        strokeWidth: 1,
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
        validateConnection({targetMagnet}) {
            return !!targetMagnet
        },
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
    },
    getDropNode(node) {
        console.log(node);
        const size = node.size()
        return node.clone().size(size.width, size.height)
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
    graph.on('node:mouseenter', () => {
        const ports = container.querySelectorAll(
            '.x6-port-body',
        )
        showPorts(ports, true)
    })
    graph.on('node:mouseleave', () => {
        const ports = container.querySelectorAll(
            '.x6-port-body',
        )
        showPorts(ports, false)
    })
    graph.on('edge:connected', ({ isNew, edge }) => {
        console.log(isNew)
        console.log(edge)
    })

}

const initKeyboard = () => {
    // copy cut paste
    graph.bindKey(['meta+c', 'ctrl+c'], () => {
        const cells = graph.getSelectedCells()
        if (cells.length) {
            graph.copy(cells)
        }
        return false
    })
    graph.bindKey(['meta+x', 'ctrl+x'], () => {
        const cells = graph.getSelectedCells()
        if (cells.length) {
            graph.cut(cells)
        }
        return false
    })
    graph.bindKey(['meta+v', 'ctrl+v'], () => {
        if (!graph.isClipboardEmpty()) {
            const cells = graph.paste({offset: 32})
            graph.cleanSelection()
            graph.select(cells)
        }
        return false
    })

    //undo redo
    graph.bindKey(['meta+z', 'ctrl+z'], () => {
        if (graph.history.canUndo()) {
            graph.history.undo()
        }
        return false
    })
    graph.bindKey(['meta+shift+z', 'ctrl+shift+z'], () => {
        if (graph.history.canRedo()) {
            graph.history.redo()
        }
        return false
    })

    // select all
    graph.bindKey(['meta+a', 'ctrl+a'], () => {
        const nodes = graph.getNodes()
        if (nodes) {
            graph.select(nodes)
        }
    })

    //delete
    graph.bindKey('backspace', () => {
        const cells = graph.getSelectedCells()
        if (cells.length) {
            graph.removeCells(cells)
        }
    })

    // zoom
    graph.bindKey(['ctrl+1', 'meta+1'], () => {
        const zoom = graph.zoom()
        if (zoom < 1.5) {
            graph.zoom(0.1)
        }
    })
    graph.bindKey(['ctrl+2', 'meta+2'], () => {
        const zoom = graph.zoom()
        if (zoom > 0.5) {
            graph.zoom(-0.1)
        }
    })
}


// 初始化事件
initEvents('main')
// 初始化快捷键
initKeyboard()
