/*
 *+------------------
 * Tpflow 5.0 数据库文件
 *+------------------
 * Copyright (c) 2006~2025 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- 授权权限表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_entrust`;
CREATE TABLE `t_wf_entrust` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flow_id` int(11) NOT NULL COMMENT '运行id',
  `flow_process` int(11) NOT NULL COMMENT '运行步骤id',
  `entrust_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `entrust_user` varchar(255) NOT NULL COMMENT '被授权人',
  `entrust_name` varchar(255) NOT NULL COMMENT '被授权人名称',
  `entrust_stime` int(11) NOT NULL COMMENT '授权开始时间',
  `entrust_etime` int(11) NOT NULL COMMENT '授权结束时间',
  `entrust_con` longtext COMMENT '授权备注',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `old_user` varchar(255) NOT NULL COMMENT '授权人',
  `old_name` varchar(255) NOT NULL COMMENT '授权人名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='委托授权表';

-- ----------------------------
-- 授权关系表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_entrust_rel`;
CREATE TABLE `t_wf_entrust_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `entrust_id` int(11) NOT NULL COMMENT '授权id',
  `process_id` int(11) NOT NULL COMMENT '步骤id',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态0为新增，2为办结',
  `add_time` datetime DEFAULT NULL COMMENT '添加日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='流程授权关系表';

-- ----------------------------
-- 流程信息表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_flow`;
CREATE TABLE `t_wf_flow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '流程类别',
  `flow_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '流程名称',
  `flow_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `sort_order` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0不可用1正常',
  `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) DEFAULT NULL COMMENT '添加用户',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `field_name` varchar(255) DEFAULT NULL COMMENT '过滤字段名',
  `field_value` varchar(255) DEFAULT NULL COMMENT '过滤字段值',
  `is_field` int(11) DEFAULT '0' COMMENT '是否过滤',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='*工作流表';

-- ----------------------------
-- 流程步骤表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_flow_process`;
CREATE TABLE `t_wf_flow_process` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `process_name` varchar(255) NOT NULL DEFAULT '步骤' COMMENT '步骤名称',
  `process_type` char(10) NOT NULL DEFAULT '' COMMENT '步骤类型',
  `process_to` varchar(255) NOT NULL DEFAULT '' COMMENT '转交下一步骤号',
  `auto_person` tinyint(1) unsigned NOT NULL DEFAULT '4' COMMENT '3自由选择|4指定人员|5指定角色|6事务接受',
  `auto_sponsor_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '4指定步骤主办人ids',
  `auto_sponsor_text` varchar(255) NOT NULL DEFAULT '' COMMENT '4指定步骤主办人text',
  `work_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '6事务接受',
  `work_text` varchar(255) NOT NULL DEFAULT '' COMMENT '6事务接受',
  `auto_role_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '5角色ids',
  `auto_role_text` varchar(255) NOT NULL DEFAULT '' COMMENT '5角色 text',
  `range_user_ids` text COMMENT '3自由选择IDS',
  `range_user_text` text COMMENT '3自由选择用户ID',
  `is_sing` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1允许|2不允许',
  `is_back` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1允许|2不允许',
  `out_condition` text COMMENT '转出条件',
  `setleft` smallint(5) unsigned NOT NULL DEFAULT '100' COMMENT '左 坐标',
  `settop` smallint(5) unsigned NOT NULL DEFAULT '100' COMMENT '上 坐标',
  `style` text COMMENT '样式 序列化',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `uptime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `wf_mode` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 单一线性，1，转出条件 2，同步模式',
  `wf_action` varchar(255) NOT NULL DEFAULT 'view' COMMENT '对应方法',
  `work_sql` longtext,
  `work_msg` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


-- ----------------------------
-- 节点运行主表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run`;
CREATE TABLE `t_wf_run` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_table` varchar(255) DEFAULT NULL COMMENT '单据表，不带前缀',
  `from_id` int(11) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程id 正常流程',
  `run_flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流转到什么ID',
  `run_flow_process` varchar(255) DEFAULT NULL COMMENT '流转到第几步',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0流程中，1通过',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `uptime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `is_sing` int(11) NOT NULL DEFAULT '0',
  `sing_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 运行日志表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_log`;
CREATE TABLE `t_wf_run_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `from_id` int(11) DEFAULT NULL COMMENT '单据ID',
  `from_table` varchar(255) DEFAULT NULL COMMENT '单据表',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `content` text NOT NULL COMMENT '日志内容',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `btn` varchar(255) DEFAULT NULL COMMENT '提交操作信息',
  `art` longtext COMMENT '附件日志',
  `work_info` varchar(255) DEFAULT NULL COMMENT '事务日志',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 运行步骤表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_process`;
CREATE TABLE `t_wf_run_process` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属于那个流程的id',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `remark` text COMMENT '备注',
  `auto_person` tinyint(4) DEFAULT NULL,
  `sponsor_text` varchar(255) DEFAULT NULL,
  `sponsor_ids` varchar(255) DEFAULT NULL,
  `is_sing` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已会签过',
  `is_back` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '被退回的 0否(默认) 1是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0为未接收（默认），1为办理中 ,2为已转交,3为已结束4为已打回',
  `js_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接收时间',
  `bl_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '办理时间',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `wf_mode` int(11) DEFAULT NULL,
  `wf_action` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`),
  KEY `status` (`status`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 会签记录表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_sign`;
CREATE TABLE `t_wf_run_sign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID,子流程时区分run step',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `content` text COMMENT '会签内容',
  `is_agree` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核意见：1同意；2不同意',
  `sign_att_id` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 工作流事务信息表
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_workinfo`;
CREATE TABLE `t_wf_workinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_info` longtext COMMENT '单据JSON',
  `data` longtext COMMENT '处理数据',
  `info` longtext COMMENT '处理结果',
  `datetime` datetime DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL COMMENT '类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

