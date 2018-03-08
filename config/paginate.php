<?php
// +----------------------------------------------------------------------
// | 分页
// +----------------------------------------------------------------------
return [
   'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
        'page_size'=>10, //页码数量
        'page_button'=>[
            'total_rows'=>true, //是否显示总条数
            'turn_page'=>true, //上下页按钮
            'turn_group'=>true, //上下组按钮
            'first_page'=>true, //首页
            'last_page'=>true  //尾页
        ]
];
