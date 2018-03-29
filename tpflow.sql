/*
Navicat MySQL Data Transfer

Source Server         : pms
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : tpflow

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-03-30 07:28:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for leipi_flow
-- ----------------------------
DROP TABLE IF EXISTS `leipi_flow`;
CREATE TABLE `leipi_flow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL COMMENT '流程类别',
  `flow_name` varchar(255) NOT NULL DEFAULT '' COMMENT '流程名称',
  `flow_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `sort_order` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不可用1正常',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_flow
-- ----------------------------
INSERT INTO `leipi_flow` VALUES ('3', 'news', '新闻审批工作流-多条件判断型', '新闻审批工作流-多条件判断型', '0', '0', '0', null, null);
INSERT INTO `leipi_flow` VALUES ('4', 'news', '新闻审批工作流-直线型', '新闻审批工作流-直线型', '0', '0', '0', null, null);
INSERT INTO `leipi_flow` VALUES ('5', 'news', '测试工作流添加', '2', '1', '0', '0', '1', '1522242396');
INSERT INTO `leipi_flow` VALUES ('6', 'paper', '合同信息审批', '合同信息审批21', '2', '0', '0', '1', '1522242419');

-- ----------------------------
-- Table structure for leipi_flow_process
-- ----------------------------
DROP TABLE IF EXISTS `leipi_flow_process`;
CREATE TABLE `leipi_flow_process` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `process_name` varchar(255) NOT NULL DEFAULT '' COMMENT '步骤名称',
  `process_type` char(10) NOT NULL DEFAULT '' COMMENT '步骤类型',
  `process_to` varchar(255) NOT NULL DEFAULT '' COMMENT '转交下一步骤号',
  `child_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'is_child 子流程id有return_step_to结束后继续父流程下一步',
  `child_relation` text NOT NULL COMMENT '[保留功能]父子流程字段映射关系',
  `child_after` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '子流程 结束后动作 0结束并更新父流程节点为结束  1结束并返回父流程步骤',
  `child_back_process` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '子流程结束返回的步骤id',
  `return_sponsor_ids` text NOT NULL COMMENT '[保留功能]主办人 子流程结束后下一步的主办人',
  `return_respon_ids` text NOT NULL COMMENT '[保留功能]经办人 子流程结束后下一步的经办人',
  `write_fields` text NOT NULL COMMENT '这个步骤可写的字段',
  `secret_fields` text NOT NULL COMMENT '这个步骤隐藏的字段',
  `lock_fields` text NOT NULL COMMENT '锁定不能更改宏控件的值',
  `check_fields` text NOT NULL COMMENT '字段验证规则',
  `auto_person` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '本步骤的自动选主办人规则0:为不自动选择1：流程发起人2：本部门主管3指定默认人4上级主管领导5. 一级部门主管6. 指定步骤主办人',
  `auto_unlock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许修改主办人auto_type>0 0不允许 1允许（默认）',
  `auto_sponsor_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人ids',
  `auto_sponsor_text` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人text',
  `auto_respon_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人ids',
  `auto_respon_text` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人text',
  `auto_role_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '制定默认角色ids',
  `auto_role_text` varchar(255) NOT NULL DEFAULT '' COMMENT '制定默认角色 text',
  `auto_process_sponsor` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '[保留功能]指定其中一个步骤的主办人处理',
  `range_user_ids` text NOT NULL COMMENT '本步骤的经办人授权范围ids',
  `range_user_text` text NOT NULL COMMENT '本步骤的经办人授权范围text',
  `range_dept_ids` text NOT NULL COMMENT '本步骤的经办部门授权范围',
  `range_dept_text` text NOT NULL COMMENT '本步骤的经办部门授权范围text',
  `range_role_ids` text NOT NULL COMMENT '本步骤的经办角色授权范围ids',
  `range_role_text` text NOT NULL COMMENT '本步骤的经办角色授权范围text',
  `receive_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0明确指定主办人1先接收者为主办人',
  `is_user_end` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '允许主办人在非最后步骤也可以办结流程',
  `is_userop_pass` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '经办人可以转交下一步',
  `is_sing` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会签选项0禁止会签1允许会签（默认） 2强制会签',
  `sign_look` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会签可见性0总是可见（默认）,1本步骤经办人之间不可见2针对其他步骤不可见',
  `is_back` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许回退0不允许（默认） 1允许退回上一步2允许退回之前步骤',
  `out_condition` text NOT NULL COMMENT '转出条件',
  `setleft` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '左 坐标',
  `settop` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上 坐标',
  `style` text NOT NULL COMMENT '样式 序列化',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_flow_process
-- ----------------------------
INSERT INTO `leipi_flow_process` VALUES ('70', '4', '开始', 'is_one', '71', '0', '', '1', '0', '', '', '', '', '', '', '4', '1', '10,11,12', '测试1,测试2,测试3', '10,11,12', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"71\":{\"condition\":[],\"condition_desc\":\"\"}}', '436', '215', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521769266', '0');
INSERT INTO `leipi_flow_process` VALUES ('71', '4', '市场部审核', 'is_step', '72', '4', '', '2', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"72\":{\"condition\":[],\"condition_desc\":\"\"}}', '569', '404', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521769266', '0');
INSERT INTO `leipi_flow_process` VALUES ('72', '4', '工程部审核', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '778', '331', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521769266', '0');
INSERT INTO `leipi_flow_process` VALUES ('73', '3', '开始', 'is_one', '74,75', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"74\":{\"condition\":[\"new_type= \'1\'\"],\"condition_desc\":\"\"},\"75\":{\"condition\":[\"new_type= \'2\'\"],\"condition_desc\":\"\"}}', '331', '187', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');
INSERT INTO `leipi_flow_process` VALUES ('74', '3', '市场部', 'is_step', '76', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '600', '143', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');
INSERT INTO `leipi_flow_process` VALUES ('75', '3', '工程部', 'is_step', '76', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '578', '333', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');
INSERT INTO `leipi_flow_process` VALUES ('76', '3', '行政部确认', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '829', '207', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');
INSERT INTO `leipi_flow_process` VALUES ('77', '6', '项目中心初审', 'is_one', '78', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '375', '234', '{\"width\":150,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522246153', '0');
INSERT INTO `leipi_flow_process` VALUES ('78', '6', '新建步骤', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '650', '326', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522246154', '0');
INSERT INTO `leipi_flow_process` VALUES ('79', '5', '新建步骤', 'is_one', '80,81', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"80\":{\"condition\":[\"uid= \'1\'\"],\"condition_desc\":\"\"},\"81\":{\"condition\":[\"uid= \'2\'\"],\"condition_desc\":\"\"}}', '255', '249', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522246614', '0');
INSERT INTO `leipi_flow_process` VALUES ('80', '5', '新建步骤', 'is_step', '82', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '575', '117', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522246614', '0');
INSERT INTO `leipi_flow_process` VALUES ('81', '5', '新建步骤', 'is_step', '82', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '591', '337', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522246614', '0');
INSERT INTO `leipi_flow_process` VALUES ('82', '5', '新建步骤', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '845', '265', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522246614', '0');

-- ----------------------------
-- Table structure for leipi_foreign_test
-- ----------------------------
DROP TABLE IF EXISTS `leipi_foreign_test`;
CREATE TABLE `leipi_foreign_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_foreign_test
-- ----------------------------
INSERT INTO `leipi_foreign_test` VALUES ('13', '52', '32333', '0', '1407556818', '1407338095');
INSERT INTO `leipi_foreign_test` VALUES ('14', '52', '55555', '0', '1407557049', '1407556896');
INSERT INTO `leipi_foreign_test` VALUES ('15', '52', 'q23', '0', '1408597541', '1408597541');
INSERT INTO `leipi_foreign_test` VALUES ('16', '54', '22', '0', '1410971141', '1410970367');
INSERT INTO `leipi_foreign_test` VALUES ('17', '54', 'ww', '0', '1410971511', '1410971183');

-- ----------------------------
-- Table structure for leipi_news
-- ----------------------------
DROP TABLE IF EXISTS `leipi_news`;
CREATE TABLE `leipi_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `add_time` int(11) DEFAULT NULL COMMENT '新增时间',
  `new_title` varchar(255) DEFAULT NULL COMMENT '新闻标题',
  `new_type` int(11) DEFAULT NULL COMMENT '新闻类别',
  `new_top` int(11) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `new_con` longtext COMMENT '新闻内容',
  `new_user` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '-1回退修改0 保存中1流程中 2通过',
  `uptime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_news
-- ----------------------------
INSERT INTO `leipi_news` VALUES ('10', '1', '1522328414', '业务测试2', '1', '1', '&lt;p&gt;			&amp;lt;p&amp;gt;测试下呗&amp;lt;/p&amp;gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;1112313&lt;/p&gt;', null, '1', '1522329203');

-- ----------------------------
-- Table structure for leipi_news_type
-- ----------------------------
DROP TABLE IF EXISTS `leipi_news_type`;
CREATE TABLE `leipi_news_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_news_type
-- ----------------------------
INSERT INTO `leipi_news_type` VALUES ('1', '公司新闻', '1', '1516462457');
INSERT INTO `leipi_news_type` VALUES ('2', '部门新闻', '1', null);
INSERT INTO `leipi_news_type` VALUES ('3', '上级要闻', '1', null);
INSERT INTO `leipi_news_type` VALUES ('4', '职称考试', '1', null);

-- ----------------------------
-- Table structure for leipi_run
-- ----------------------------
DROP TABLE IF EXISTS `leipi_run`;
CREATE TABLE `leipi_run` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'work_run父流转公文ID 值大于0则这个是子流程，完成后或者要返回父流程',
  `from_table` varchar(255) DEFAULT NULL COMMENT '单据表，不带前缀',
  `from_id` int(11) DEFAULT NULL,
  `pid_flow_step` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父pid的flow_id中的第几步骤进入的,取回这个work_flow_step的child_over决定结束子流程的动作',
  `cache_run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '多个子流程时pid无法识别cache所以加这个字段pid>0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程id 正常流程',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程分类ID即公文分类ID',
  `run_name` varchar(255) DEFAULT '' COMMENT '公文名称',
  `run_flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流转到什么流程 最新流程，查询优化，进入子流程时将简化查询，子流程与父流程同步',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '流转到第几步',
  `att_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '公文附件ids',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0流程中，1通过,2回退',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `pid_flow_step` (`pid_flow_step`),
  KEY `cache_run_id` (`cache_run_id`),
  KEY `uid` (`uid`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=767 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run
-- ----------------------------
INSERT INTO `leipi_run` VALUES ('764', '0', 'news', '10', '0', '0', '1', '4', '0', '10', '4', '70', '', '1522329011', '1', '0', '0', '1522328930');
INSERT INTO `leipi_run` VALUES ('765', '0', 'news', '10', '0', '0', '1', '4', '0', '10', '4', '70', '', '1522365590', '1', '0', '0', '1522329203');
INSERT INTO `leipi_run` VALUES ('766', '0', 'news', '10', '0', '0', '1', '4', '0', '10', '4', '71', '', '0', '0', '0', '0', '1522365590');

-- ----------------------------
-- Table structure for leipi_run_cache
-- ----------------------------
DROP TABLE IF EXISTS `leipi_run_cache`;
CREATE TABLE `leipi_run_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 缓存run工作的全部流程模板步骤等信息,确保修改流程后工作依然不变',
  `form_id` int(10) unsigned NOT NULL DEFAULT '0',
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `run_form` text NOT NULL COMMENT '模板信息',
  `run_flow` text NOT NULL COMMENT '流程信息',
  `run_flow_process` text NOT NULL COMMENT '流程步骤信息 ',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `run_id` (`run_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1928 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run_cache
-- ----------------------------
INSERT INTO `leipi_run_cache` VALUES ('1926', '764', '10', '10', '', '{\"id\":10,\"uid\":1,\"add_time\":1522328414,\"new_title\":\"\\u4e1a\\u52a1\\u6d4b\\u8bd5\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;\\u6d4b\\u8bd5\\u4e0b\\u5457&lt;\\/p&gt;\",\"new_user\":null,\"status\":0,\"uptime\":null}', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1521769266,\"dateline\":0}', '0', '0', '1522328930');
INSERT INTO `leipi_run_cache` VALUES ('1927', '765', '10', '10', '', '{\"id\":10,\"uid\":1,\"add_time\":1522328414,\"new_title\":\"\\u4e1a\\u52a1\\u6d4b\\u8bd52\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;\\t\\t\\t&amp;lt;p&amp;gt;\\u6d4b\\u8bd5\\u4e0b\\u5457&amp;lt;\\/p&amp;gt;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;1112313&lt;\\/p&gt;\",\"new_user\":null,\"status\":0,\"uptime\":1522329011}', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1521769266,\"dateline\":0}', '0', '0', '1522329203');

-- ----------------------------
-- Table structure for leipi_run_log
-- ----------------------------
DROP TABLE IF EXISTS `leipi_run_log`;
CREATE TABLE `leipi_run_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `from_id` int(11) DEFAULT NULL,
  `from_table` varchar(255) DEFAULT NULL,
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID,子流程时区分run step',
  `content` text NOT NULL COMMENT '日志内容',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT 'ip2long最后登陆ip',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run_log
-- ----------------------------
INSERT INTO `leipi_run_log` VALUES ('16', '1', '0', '工作流发起', '764', '0', '10', '', '1522328930');
INSERT INTO `leipi_run_log` VALUES ('17', '1', '0', '工作流发起', '765', '0', '10', '', '1522329203');
INSERT INTO `leipi_run_log` VALUES ('18', '1', '10', 'news', '766', '0', '审批意见', '', '1522365590');

-- ----------------------------
-- Table structure for leipi_run_process
-- ----------------------------
DROP TABLE IF EXISTS `leipi_run_process`;
CREATE TABLE `leipi_run_process` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属于那个流程的id',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `parent_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上一步流程',
  `parent_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上一步骤号',
  `run_child` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始转入子流程run_id 如果转入子流程，则在这里也记录',
  `remark` text NOT NULL COMMENT '备注',
  `is_receive_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否先接收人为主办人',
  `is_sponsor` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否步骤主办人 0否(默认) 1是',
  `is_singpost` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已会签过',
  `is_back` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '被退回的 0否(默认) 1是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0为未接收（默认），1为办理中 ,2为已转交,3为已结束4为已打回',
  `js_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接收时间',
  `bl_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '办理时间',
  `jj_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '转交时间,最后一步等同办结时间',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`),
  KEY `status` (`status`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=1515 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run_process
-- ----------------------------
INSERT INTO `leipi_run_process` VALUES ('1512', '1', '764', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1522328930', '1522328930', '0', '0', '0', '1522328930');
INSERT INTO `leipi_run_process` VALUES ('1513', '1', '765', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1522329203', '1522329203', '0', '0', '0', '1522329203');
INSERT INTO `leipi_run_process` VALUES ('1514', '1', '766', '4', '71', '0', '0', '0', '', '0', '1', '0', '0', '1', '1522365590', '1522365590', '0', '0', '0', '1522365590');

-- ----------------------------
-- Table structure for leipi_run_sign
-- ----------------------------
DROP TABLE IF EXISTS `leipi_run_sign`;
CREATE TABLE `leipi_run_sign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID,子流程时区分run step',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `content` text NOT NULL COMMENT '会签内容',
  `is_agree` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核意见：1同意；2不同意',
  `sign_att_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sign_look` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '步骤设置的会签可见性,0总是可见（默认）,1本步骤经办人之间不可见2针对其他步骤不可见',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run_sign
-- ----------------------------
