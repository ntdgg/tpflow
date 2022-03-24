<?php
/**
*+------------------
* Tpflow 配置文件夹
*+------------------ 
*/
return [
	'version'=>'6.0.2',//当前版本
	'database'=>config('database.connections.mysql.database'),//数据库名称
	'prefix'=>config('database.connections.mysql.prefix'),//数据库前缀
	'int_url'=>'/gadmin',//使用工作流的模块名称
	'gateway_mode' =>2,//1,默认使用Tp的助手函数
    'start_flow'=>['gongdan','lunwen','zhuanli'],//流程发起控制，填写 table 则只有填写人可以发起工作流
	'gateway_action' =>'\\sys\\Gateway',//自定义方法返回数据 命名空间 中的GetUserInfo
	'user_id' =>'softId',//用户的session名称 
	'role_id' =>'sfotRoleId',//用户角色的session名称 
	'work_table'=>'[work]',//特定的表前缀，用于接入工作流的实体表
	/*用户信息配置*/
	'user' => [
		'db'=>'soft_user', //表名
		'key'=>'id', //主键
		'getfield'=>'username',//获取用户名称
		'field'=>'id,username',//查询筛选字段 用于设计器的选人
		'searchwhere'=>'username'//查询筛选字段 用于设计器where匹配
	],
	'wf_url' => [
		'wfdo'=>"/gadmin/wf/wfdo.html",
		'start'=>"/gadmin/wf/wfstart.html", //表名
		'wfapi'=>"/gadmin/wf/wfapi.html", //表名
		'designapi'=>"/gadmin/wf/designapi.html", //表名
	],
	/*角色信息配置*/
	'role' => [
		'db'=>'soft_role', //表名
		'key'=>'id', //主键
		'getfield'=>'name',//获取用户名称
		'field'=>'id,name as username',//查询筛选字段 用于设计器的选人
		'searchwhere'=>'name'//查询筛选字段 用于设计器where匹配
	],
	/*工作流类别信息配置*/
	'wf_type_mode'=>0,//工作流类别模式 0为数据库驱动，1自定义模式
	'wf_type_data' => [
		['name'=>'news','title'=>'新闻'], //业务表=>业务名称
	],
	'static_url'=>'/static/work/',//资源目录
	'view_return'=>1,//1、直接从lib类库中返回 2、直接返回JSON数据，需要自行进行数据处理
	'wf_bill_mode'=>1,//工作流读取单据信息，系统自带模式,2、自定义模式
	'wf_bill_namespace'=>'',
	'wf_db_mode'=>1,//工作ORM驱动，系统自带模式Think-ORM,2、自定义ORM
	'wf_db_namespace'=>'',
	'wf_work_namespace'=>'\\bill\\',//事务驱动命名空间
	'wf_access_control'=>3,//工作流权限控制（终止和去审），1、所有人均可终止；2、单据发起人可以终止；3、指定uid可以终止；
	'wf_access_control_uid' => [1,2,3],//可以控制
	'wf_upload_file' => '/gadmin/common/upload',//附件上传接口
	'msg_api' => '\\sys\\Msg',//消息推送接口
    'kpi_open'=>1,//是否开启绩效
    'kpi_out'=>300,//办理超时不得分，单位为分钟
    'kpi_base'=>1, //基础得分
    'wf_action'=>'\\sys\\Wfaction' //流程方法
];

