<?php
/**
*+------------------
* Tpflow 配置文件夹
*+------------------
* Copyright (c) 2006~2018 http://cojz8.com All rights reserved.
*+------------------
* Author: guoguo(1838188896@qq.com)
*+------------------ 
*/
namespace workflow;

//用户定义文件夹
$flowstatus = ['-1'=>'未发起','0'=>'运行中','1'=>'已核准'];

//用户邮件处理类,如果不需要可注释$email数组
//必需实现service/inheritance/InterfaceEmail接口
$email['default']['class'] = "MailService";//类名
$email['default']['path'] = BEASE_URL . "/msg/mailservice.php";//类路径


//用户自定义表
//[type=>['表名','主键'，'getfield','field','searchwhere']]
$user_table  =  ['user'=>['user','id','username','id as id,username as username','username'],'role'=>['role','id','name','id as id,name as username','name']];
