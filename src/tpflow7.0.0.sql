-- ----------------------------
-- Table structure for t_wf_entrust
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_entrust`;
CREATE TABLE `t_wf_entrust` (
  `id` int NOT NULL AUTO_INCREMENT,
  `flow_id` int NOT NULL COMMENT '运行id',
  `flow_process` int NOT NULL COMMENT '运行步骤id',
  `entrust_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '标题',
  `entrust_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL COMMENT '被授权人',
  `entrust_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL COMMENT '被授权人名称',
  `entrust_stime` int NOT NULL COMMENT '授权开始时间',
  `entrust_etime` int NOT NULL COMMENT '授权结束时间',
  `entrust_con` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '授权备注',
  `add_time` int DEFAULT NULL COMMENT '添加时间',
  `old_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL COMMENT '授权人',
  `old_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL COMMENT '授权人名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='委托授权表';

-- ----------------------------
-- Table structure for t_wf_entrust_rel
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_entrust_rel`;
CREATE TABLE `t_wf_entrust_rel` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `entrust_id` int NOT NULL COMMENT '授权id',
  `process_id` int NOT NULL COMMENT '步骤id',
  `status` int NOT NULL DEFAULT '0' COMMENT '状态0为新增，2为办结',
  `add_time` datetime DEFAULT NULL COMMENT '添加日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='流程授权关系表';

-- ----------------------------
-- Table structure for t_wf_event
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_event`;
CREATE TABLE `t_wf_event` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `act` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `code` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '代码',
  `uid` int DEFAULT NULL COMMENT ' 用户id',
  `uptime` int DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for t_wf_flow
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_flow`;
CREATE TABLE `t_wf_flow` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '流程类别',
  `flow_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '流程名称',
  `flow_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `sort_order` mediumint unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0不可用1正常',
  `is_del` tinyint unsigned NOT NULL DEFAULT '0',
  `uid` int DEFAULT NULL COMMENT '添加用户',
  `add_time` int DEFAULT NULL COMMENT '添加时间',
  `is_field` int DEFAULT '0' COMMENT '是否开启过滤',
  `field_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段名',
  `field_value` int DEFAULT '0' COMMENT '字段值',
  `tmp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT '' COMMENT '模板字段',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='*工作流表';

-- ----------------------------
-- Table structure for t_wf_flow_process
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_flow_process`;
CREATE TABLE `t_wf_flow_process` (
  `id` int NOT NULL AUTO_INCREMENT,
  `flow_id` int unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `process_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '步骤' COMMENT '步骤名称',
  `process_type` char(50) CHARACTER SET utf8mb4 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '步骤类型',
  `process_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '转交下一步骤号',
  `auto_person` tinyint unsigned NOT NULL DEFAULT '4' COMMENT '3自由选择|4指定人员|5指定角色|6事务接受',
  `auto_sponsor_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '4指定步骤主办人ids',
  `auto_sponsor_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '4指定步骤主办人text',
  `work_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '6事务接受',
  `work_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '6事务接受',
  `work_auto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `work_condition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `work_val` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `auto_role_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '5角色ids',
  `auto_role_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '5角色 text',
  `range_user_ids` text CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '3自由选择IDS',
  `range_user_text` text CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '3自由选择用户ID',
  `is_sing` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1允许|2不允许',
  `is_back` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1允许|2不允许',
  `out_condition` text CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '转出条件',
  `setleft` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '100' COMMENT '左 坐标',
  `settop` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT '100' COMMENT '上 坐标',
  `style` text CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '样式 序列化',
  `is_del` tinyint unsigned NOT NULL DEFAULT '0',
  `uptime` int unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `dateline` int unsigned NOT NULL DEFAULT '0',
  `wf_mode` int unsigned NOT NULL DEFAULT '0' COMMENT '0 单一线性，1，转出条件 2，同步模式',
  `wf_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL DEFAULT 'view' COMMENT '对应方法',
  `work_sql` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci,
  `work_msg` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci,
  `auto_xt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT '' COMMENT '2协同字段',
  `auto_xt_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT '' COMMENT '2协同字段',
  `is_time` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='工作流设计主表';

-- ----------------------------
-- Table structure for t_wf_kpi_data
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_kpi_data`;
CREATE TABLE `t_wf_kpi_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `k_node` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8_unicode_ci DEFAULT NULL,
  `k_uid` int NOT NULL COMMENT '用户id',
  `k_role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '角色id',
  `k_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL COMMENT '单据类别',
  `k_type_id` int NOT NULL COMMENT '单据id',
  `k_describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '描述',
  `k_mark` tinyint NOT NULL DEFAULT '1' COMMENT '绩效总分',
  `k_base` tinyint NOT NULL DEFAULT '1' COMMENT '基础分',
  `k_isout` tinyint NOT NULL DEFAULT '0' COMMENT '是否超时 0=未超时 1=超时',
  `k_year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '添加年',
  `k_month` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '添加月',
  `k_date` date DEFAULT NULL COMMENT '添加日期',
  `k_create_time` int DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='工作流用户绩效明细表';

-- ----------------------------
-- Table structure for t_wf_kpi_month
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_kpi_month`;
CREATE TABLE `t_wf_kpi_month` (
  `id` int NOT NULL AUTO_INCREMENT,
  `k_uid` int NOT NULL COMMENT '用户id',
  `k_role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '角色id',
  `k_mark` bigint NOT NULL DEFAULT '1' COMMENT '绩效总分',
  `k_time` int NOT NULL DEFAULT '1' COMMENT '基础分',
  `k_year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '添加年',
  `k_month` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '添加月',
  `k_create_time` int DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户绩效月度绩效';

-- ----------------------------
-- Table structure for t_wf_kpi_year
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_kpi_year`;
CREATE TABLE `t_wf_kpi_year` (
  `id` int NOT NULL AUTO_INCREMENT,
  `k_uid` int NOT NULL COMMENT '用户id',
  `k_role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '角色id',
  `k_mark` bigint NOT NULL DEFAULT '1' COMMENT '绩效总分',
  `k_time` int NOT NULL DEFAULT '1' COMMENT '总次数',
  `k_year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '添加年',
  `k_create_time` int DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='工作流绩效年度总表';

-- ----------------------------
-- Table structure for t_wf_run
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run`;
CREATE TABLE `t_wf_run` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `from_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '单据表，不带前缀',
  `from_id` int DEFAULT NULL,
  `uid` int unsigned NOT NULL DEFAULT '0',
  `flow_id` int unsigned NOT NULL DEFAULT '0' COMMENT '流程id 正常流程',
  `run_flow_id` int unsigned NOT NULL DEFAULT '0' COMMENT '流转到什么ID',
  `run_flow_process` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '流转到第几步',
  `endtime` int unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` int unsigned NOT NULL DEFAULT '0' COMMENT '状态，0流程中，1通过',
  `is_del` tinyint unsigned NOT NULL DEFAULT '0',
  `uptime` int unsigned NOT NULL DEFAULT '0',
  `dateline` int unsigned NOT NULL DEFAULT '0',
  `is_sing` int NOT NULL DEFAULT '0',
  `sing_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='工作流运行主表';

-- ----------------------------
-- Table structure for t_wf_run_log
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_log`;
CREATE TABLE `t_wf_run_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `from_id` int DEFAULT NULL COMMENT '单据ID',
  `from_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '单据表',
  `run_id` int unsigned NOT NULL DEFAULT '0' COMMENT '流转id',
  `run_flow` int unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8_general_ci NOT NULL COMMENT '日志内容',
  `dateline` int unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `btn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '提交操作信息',
  `art` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '附件日志',
  `work_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '事务日志',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `run_id` (`run_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='工作流日志表';

-- ----------------------------
-- Table structure for t_wf_run_process
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_process`;
CREATE TABLE `t_wf_run_process` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0',
  `run_id` int unsigned NOT NULL DEFAULT '0' COMMENT '当前流转id',
  `run_flow` int unsigned NOT NULL DEFAULT '0' COMMENT '属于那个流程的id',
  `run_flow_process` smallint unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '备注',
  `auto_person` tinyint DEFAULT NULL,
  `sponsor_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `sponsor_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `is_sing` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否已会签过',
  `is_back` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '被退回的 0否(默认) 1是',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '状态 0为未接收（默认），1为办理中 ,2为已转交,3为已结束4为已打回',
  `js_time` int unsigned NOT NULL DEFAULT '0' COMMENT '接收时间',
  `bl_time` int unsigned NOT NULL DEFAULT '0' COMMENT '办理时间',
  `is_del` tinyint unsigned NOT NULL DEFAULT '0',
  `updatetime` int unsigned NOT NULL DEFAULT '0',
  `dateline` int unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `wf_mode` int DEFAULT NULL,
  `wf_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL,
  `is_time` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `run_id` (`run_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `is_del` (`is_del`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='工作流运行步骤表';

-- ----------------------------
-- Table structure for t_wf_run_process_cc
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_process_cc`;
CREATE TABLE `t_wf_run_process_cc` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_id` int NOT NULL COMMENT '关联id',
  `from_table` varchar(255) DEFAULT NULL COMMENT '关联表',
  `uid` int DEFAULT NULL COMMENT '用户id',
  `run_id` varchar(255) DEFAULT NULL COMMENT '运行run 表id',
  `process_id` int DEFAULT NULL COMMENT '关联步骤id',
  `process_ccid` int DEFAULT NULL COMMENT '消息步骤id',
  `add_time` int DEFAULT NULL COMMENT '添加时间',
  `uptime` int DEFAULT NULL COMMENT '执行时间',
  `status` smallint NOT NULL DEFAULT '0' COMMENT '0 待确认 1，已确认',
  `auto_person` int DEFAULT NULL COMMENT '办理类别',
  `auto_ids` varchar(255) DEFAULT NULL COMMENT '办理ids',
  `user_ids` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='流程抄送表';

-- ----------------------------
-- Table structure for t_wf_run_process_msg
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_process_msg`;
CREATE TABLE `t_wf_run_process_msg` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int DEFAULT NULL COMMENT '用户id',
  `run_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '运行run 表id',
  `process_id` int DEFAULT NULL COMMENT '关联步骤id',
  `process_msgid` int DEFAULT NULL COMMENT '消息步骤id',
  `add_time` int DEFAULT NULL COMMENT '添加时间',
  `uptime` int DEFAULT NULL COMMENT '执行时间',
  `status` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for t_wf_run_sign
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_run_sign`;
CREATE TABLE `t_wf_run_sign` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0',
  `run_id` int unsigned NOT NULL DEFAULT '0',
  `run_flow` int unsigned NOT NULL DEFAULT '0' COMMENT '流程ID,子流程时区分run step',
  `run_flow_process` smallint unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '会签内容',
  `is_agree` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '审核意见：1同意；2不同意',
  `sign_att_id` int unsigned NOT NULL DEFAULT '0',
  `dateline` int unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `run_id` (`run_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='工作流会签记录表';

-- ----------------------------
-- Table structure for t_wf_workinfo
-- ----------------------------
DROP TABLE IF EXISTS `t_wf_workinfo`;
CREATE TABLE `t_wf_workinfo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bill_info` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '单据JSON',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '处理数据',
  `info` longtext CHARACTER SET utf8mb4 COLLATE utf8_general_ci COMMENT '处理结果',
  `datetime` datetime DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8_general_ci DEFAULT NULL COMMENT '类型',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='工作流实务信息表';
