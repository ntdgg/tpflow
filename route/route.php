<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



Route::get('index/wf/welcome','\workflow\Api@welcome');
//列表
Route::get('index/wf/wfindex','\workflow\Api@wfindex');
Route::get('index/wf/wfjk','\workflow\Api@wfjk');
Route::get('index/wf/super_user','\workflow\Api@super_user');//获取用户信息
Route::get('index/wf/super_role','\workflow\Api@super_role');//获取角色信息

//工作流添加
Route::get('index/wf/wfadd','\workflow\Api@wfadd');
Route::post('index/wf/wfadd','\workflow\Api@wfadd');
//工作流修改
Route::get('index/wf/wfedit/id/:id','\workflow\Api@wfedit');
Route::post('index/wf/wfedit/id/:id','\workflow\Api@wfedit');
//流程设计器
Route::get('index/wf/wfdesc/flow_id/:flow_id','\workflow\Api@wfdesc'); //设计界面
Route::post('index/wf/add_process','\workflow\Api@add_process'); //添加一个新流程
Route::post('index/wf/delete_process','\workflow\Api@delete_process'); //删除单个步骤
Route::post('index/wf/del_allprocess','\workflow\Api@del_allprocess'); //删除所有步骤
Route::post('index/wf/save_canvas','\workflow\Api@save_canvas'); //设计布局保存
Route::get('index/wf/wfchange','\workflow\Api@wfchange');//工作流启用关闭

//步骤属性设计
Route::get('index/wf/wfatt','\workflow\Api@wfatt'); //设计界面
Route::post('index/wf/save_attribute','\workflow\Api@save_attribute'); //步骤属性保存

//用户查询
Route::post('index/wf/super_get','\workflow\Api@super_get');//查询用户或者角色



