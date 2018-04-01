<?php
/**
*+------------------
* 配置文件夹
*+------------------ 
*/
namespace workflow;

//用户定义文件夹
$flowstatus = ['-1'=>'未发起','0'=>'运行中','1'=>'已核准'];

//用户邮件处理类,如果不需要可注释$email数组
//必需实现service/inheritance/InterfaceEmail接口
$email['default']['class'] = "MailService";//类名
$email['default']['path'] = BEASE_URL . "/msg/mailservice.php";//类路径
