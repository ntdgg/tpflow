/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1_3306
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : tpflow

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-07-19 10:39:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wf_flow
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow`;
CREATE TABLE `wf_flow` (
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='*工作流表';

-- ----------------------------
-- Records of wf_flow
-- ----------------------------
INSERT INTO `wf_flow` VALUES ('3', 'news', '新闻审批工作流-多条件判断型', '新闻审批工作流-多条件判断型', '0', '0', '0', null, null);
INSERT INTO `wf_flow` VALUES ('4', 'news', '新闻审批工作流-直线型', '新闻审批工作流-直线型', '0', '0', '0', null, null);
INSERT INTO `wf_flow` VALUES ('5', 'news', '测试工作流添加', '2', '1', '0', '0', '1', '1522242396');
INSERT INTO `wf_flow` VALUES ('6', 'paper', '合同信息审批', '合同信息审批21', '2', '1', '0', '1', '1522242419');
INSERT INTO `wf_flow` VALUES ('7', 'news', '自由选择人员测试', '123', '1', '1', '0', '7', '1523428058');

-- ----------------------------
-- Table structure for wf_flow_process
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_process`;
CREATE TABLE `wf_flow_process` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `process_name` varchar(255) NOT NULL DEFAULT '' COMMENT '步骤名称',
  `process_type` char(10) NOT NULL DEFAULT '' COMMENT '步骤类型',
  `process_to` varchar(255) NOT NULL DEFAULT '' COMMENT '转交下一步骤号',
  `child_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'is_child 子流程id有return_step_to结束后继续父流程下一步',
  `child_relation` text COMMENT '[保留功能]父子流程字段映射关系',
  `child_after` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '子流程 结束后动作 0结束并更新父流程节点为结束  1结束并返回父流程步骤',
  `child_back_process` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '子流程结束返回的步骤id',
  `return_sponsor_ids` text COMMENT '[保留功能]主办人 子流程结束后下一步的主办人',
  `return_respon_ids` text COMMENT '[保留功能]经办人 子流程结束后下一步的经办人',
  `write_fields` text COMMENT '这个步骤可写的字段',
  `secret_fields` text COMMENT '这个步骤隐藏的字段',
  `lock_fields` text COMMENT '锁定不能更改宏控件的值',
  `check_fields` text COMMENT '字段验证规则',
  `auto_person` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '本步骤的自动选主办人规则0:为不自动选择1：流程发起人2：本部门主管3指定默认人4上级主管领导5. 一级部门主管6. 指定步骤主办人',
  `auto_unlock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许修改主办人auto_type>0 0不允许 1允许（默认）',
  `auto_sponsor_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人ids',
  `auto_sponsor_text` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人text',
  `auto_respon_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人ids',
  `auto_respon_text` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人text',
  `auto_role_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '制定默认角色ids',
  `auto_role_text` varchar(255) NOT NULL DEFAULT '' COMMENT '制定默认角色 text',
  `auto_process_sponsor` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '[保留功能]指定其中一个步骤的主办人处理',
  `range_user_ids` text COMMENT '本步骤的经办人授权范围ids',
  `range_user_text` text COMMENT '本步骤的经办人授权范围text',
  `range_dept_ids` text COMMENT '本步骤的经办部门授权范围',
  `range_dept_text` text COMMENT '本步骤的经办部门授权范围text',
  `range_role_ids` text COMMENT '本步骤的经办角色授权范围ids',
  `range_role_text` text COMMENT '本步骤的经办角色授权范围text',
  `receive_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0明确指定主办人1先接收者为主办人',
  `is_user_end` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '允许主办人在非最后步骤也可以办结流程',
  `is_userop_pass` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '经办人可以转交下一步',
  `is_sing` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会签选项0禁止会签1允许会签（默认） 2强制会签',
  `sign_look` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会签可见性0总是可见（默认）,1本步骤经办人之间不可见2针对其他步骤不可见',
  `is_back` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许回退0不允许（默认） 1允许退回上一步2允许退回之前步骤',
  `out_condition` text COMMENT '转出条件',
  `setleft` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '左 坐标',
  `settop` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上 坐标',
  `style` text COMMENT '样式 序列化',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_flow_process
-- ----------------------------
INSERT INTO `wf_flow_process` VALUES ('70', '4', '开始', 'is_one', '71', '0', '', '0', '0', '', '', '', '', '', '', '4', '1', '10', '新闻部经理', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '0', '1', '[]', '436', '215', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522375321', '0');
INSERT INTO `wf_flow_process` VALUES ('71', '4', '市场部审核', 'is_step', '72', '4', '', '2', '0', '', '', '', '', '', '', '4', '1', '11', '市场部经理', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '0', '1', '[]', '569', '404', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522375321', '0');
INSERT INTO `wf_flow_process` VALUES ('72', '4', '工程部审核', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '4', '1', '13', '总经理', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '0', '1', '[]', '778', '331', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522375321', '0');
INSERT INTO `wf_flow_process` VALUES ('73', '3', '开始', 'is_one', '74,75', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"74\":{\"condition\":[\"new_type= \'1\'\"],\"condition_desc\":\"\"},\"75\":{\"condition\":[\"new_type= \'2\'\"],\"condition_desc\":\"\"}}', '331', '187', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522483275', '0');
INSERT INTO `wf_flow_process` VALUES ('74', '3', '市场部', 'is_step', '76', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '600', '143', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522483275', '0');
INSERT INTO `wf_flow_process` VALUES ('75', '3', '工程部', 'is_step', '76,87', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '578', '333', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522483275', '0');
INSERT INTO `wf_flow_process` VALUES ('76', '3', '行政部确认', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '829', '207', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522483275', '0');
INSERT INTO `wf_flow_process` VALUES ('77', '6', '项目中心初审', 'is_one', '78,83,84,85', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '375', '234', '{\"width\":150,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522382480', '0');
INSERT INTO `wf_flow_process` VALUES ('78', '6', '新建步骤', 'is_step', '86', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '1115', '655', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522382480', '0');
INSERT INTO `wf_flow_process` VALUES ('79', '5', '新建步骤', 'is_one', '80,81', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"80\":{\"condition\":[\"uid= \'1\'\"],\"condition_desc\":\"\"},\"81\":{\"condition\":[\"uid= \'2\'\"],\"condition_desc\":\"\"}}', '14', '318', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('80', '5', '新建步骤', 'is_step', '82', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '251', '116', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('81', '5', '新建步骤', 'is_step', '82', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '116', '482', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('82', '5', '新建步骤', 'is_step', '89', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '362', '299', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('83', '6', '新建步骤', 'is_step', '86', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '736', '563', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522382480', '0');
INSERT INTO `wf_flow_process` VALUES ('84', '6', '新建步骤', 'is_step', '86', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '426', '361', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522382480', '0');
INSERT INTO `wf_flow_process` VALUES ('85', '6', '新建步骤', 'is_step', '86', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '151', '410', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522382480', '0');
INSERT INTO `wf_flow_process` VALUES ('86', '6', '新建步骤', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '392', '676', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522382480', '0');
INSERT INTO `wf_flow_process` VALUES ('87', '3', '总经理审核', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '0', '1', '[]', '582', '460', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1522483275', '0');
INSERT INTO `wf_flow_process` VALUES ('88', '5', '新建步骤', 'is_step', '91', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '921', '205', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('89', '5', '新建步骤', 'is_step', '88,90', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '550', '451', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('90', '5', '新建步骤', 'is_step', '91', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '907', '451', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('91', '5', '新建步骤', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '', '1116', '297', '{\"icon\":\"icon-star\",\"width\":\"120\",\"height\":\"30\",\"color\":\"#0e76a8\"}', '0', '1522484387', '0');
INSERT INTO `wf_flow_process` VALUES ('92', '7', '市场部确认', 'is_one', '93', '0', '', '1', '0', '', '', '', '', '', '', '4', '0', '7', '市场部员工1', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '0', '1', '[]', '554', '266', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1531962565', '0');
INSERT INTO `wf_flow_process` VALUES ('93', '7', '新闻部经理', 'is_step', '94', '0', '', '1', '0', '', '', '', '', '', '', '3', '0', '', '', '', '', '', '', '0', '12,11', '工程部经理,市场部经理', '', '', '', '', '0', '0', '0', '1', '0', '1', '[]', '891', '300', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1531962565', '0');
INSERT INTO `wf_flow_process` VALUES ('94', '7', '新建步骤', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '4', '0', '13', '总经理', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '0', '1', '[]', '1354', '248', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1531962565', '0');

-- ----------------------------
-- Table structure for wf_form
-- ----------------------------
DROP TABLE IF EXISTS `wf_form`;
CREATE TABLE `wf_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '表单名称',
  `name` varchar(255) DEFAULT NULL COMMENT '表名',
  `file` varchar(255) DEFAULT NULL COMMENT '生成文件',
  `menu` int(11) NOT NULL DEFAULT '0',
  `flow` int(11) NOT NULL DEFAULT '0',
  `ziduan` longtext,
  `uid` varchar(255) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `status` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_form
-- ----------------------------
INSERT INTO `wf_form` VALUES ('1', '合同审核', 'test', 'all', '0', '0', '{\"fields\":[{\"label\":\"字段名\",\"field_type\":\"text\",\"required\":true,\"field_options\":{\"size\":\"small\"},\"cid\":\"c2\",\"name\":\"user\"},{\"label\":\"选择框\",\"field_type\":\"checkboxes\",\"required\":true,\"field_options\":{\"options\":[{\"label\":\"1\",\"checked\":false},{\"label\":\"2\",\"checked\":false}]},\"cid\":\"c6\",\"name\":\"selcet\"},{\"label\":\"年龄\",\"field_type\":\"number\",\"required\":true,\"field_options\":{\"min\":\"1\",\"max\":\"90\",\"units\":\"年龄大小\"},\"cid\":\"c10\",\"name\":\"1\"}]}', '7', '1522721520', '1');
INSERT INTO `wf_form` VALUES ('2', '采购申请', 'cgcnt', 'all', '0', '0', '{\"fields\":[{\"label\":\"年龄\",\"field_type\":\"text\",\"required\":true,\"field_options\":{\"size\":\"medium\"},\"cid\":\"c2\",\"name\":\"age\",\"search\":\"yes\",\"lists\":\"yes\"},{\"label\":\"标题\",\"field_type\":\"text\",\"required\":true,\"field_options\":{\"size\":\"medium\"},\"cid\":\"c6\",\"name\":\"title\",\"lists\":\"yes\",\"type\":\"text\",\"search\":\"no\"},{\"label\":\"Untitled\",\"field_type\":\"text\",\"required\":true,\"field_options\":{\"size\":\"small\"},\"lists\":\"yes\",\"search\":\"yes\",\"type\":\"text\",\"cid\":\"c9\"}]}', '7', '1522810306', '0');
INSERT INTO `wf_form` VALUES ('3', '业务测试', 'yw', 'all', '0', '0', '{\"fields\":[{\"label\":\"业务测试\",\"field_type\":\"text\",\"required\":true,\"field_options\":{\"size\":\"small\"},\"lists\":\"yes\",\"search\":\"yes\",\"type\":\"text\",\"cid\":\"c2\",\"name\":\"name\"},{\"label\":\"业务名称\",\"field_type\":\"text\",\"required\":true,\"field_options\":{\"size\":\"small\"},\"lists\":\"yes\",\"search\":\"yes\",\"type\":\"text\",\"cid\":\"c6\",\"name\":\"test\"}]}', '7', '1523152256', '1');

-- ----------------------------
-- Table structure for wf_form_function
-- ----------------------------
DROP TABLE IF EXISTS `wf_form_function`;
CREATE TABLE `wf_form_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL,
  `sql` longtext,
  `name` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_form_function
-- ----------------------------
INSERT INTO `wf_form_function` VALUES ('2', '1', 'SELECT type FROM `leipi_news_type` ', '新闻类别', '7', '1522727302');

-- ----------------------------
-- Table structure for wf_menu
-- ----------------------------
DROP TABLE IF EXISTS `wf_menu`;
CREATE TABLE `wf_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_menu
-- ----------------------------
INSERT INTO `wf_menu` VALUES ('12', 'yw/index', '业务测试', '7', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for wf_news
-- ----------------------------
DROP TABLE IF EXISTS `wf_news`;
CREATE TABLE `wf_news` (
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_news
-- ----------------------------
INSERT INTO `wf_news` VALUES ('3', '13', '1522586765', '123', '1', '1', '&lt;p&gt;3123&lt;/p&gt;', null, '2', '1523444896');
INSERT INTO `wf_news` VALUES ('4', '7', '1523429045', '流程测试', '1', '1', '&lt;p&gt;1123132&lt;/p&gt;', null, '2', '1523436079');
INSERT INTO `wf_news` VALUES ('5', '7', '1523532395', '132', '1', '1', '&lt;p&gt;3123333333&lt;/p&gt;', null, '2', '1523532596');

-- ----------------------------
-- Table structure for wf_news_type
-- ----------------------------
DROP TABLE IF EXISTS `wf_news_type`;
CREATE TABLE `wf_news_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_news_type
-- ----------------------------
INSERT INTO `wf_news_type` VALUES ('1', '公司新闻', '1', '1516462457');
INSERT INTO `wf_news_type` VALUES ('2', '部门新闻', '1', null);
INSERT INTO `wf_news_type` VALUES ('3', '上级要闻', '1', null);
INSERT INTO `wf_news_type` VALUES ('4', '职称考试', '1', null);

-- ----------------------------
-- Table structure for wf_role
-- ----------------------------
DROP TABLE IF EXISTS `wf_role`;
CREATE TABLE `wf_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '后台组名',
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '是否激活 1：是 0：否',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序权重',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_role
-- ----------------------------
INSERT INTO `wf_role` VALUES ('15', '市场部', '0', '1', '0', '');
INSERT INTO `wf_role` VALUES ('16', '工程部', '0', '1', '0', '');
INSERT INTO `wf_role` VALUES ('17', '新闻部', '0', '1', '0', '');
INSERT INTO `wf_role` VALUES ('18', '新闻部经理', '0', '1', '0', '');
INSERT INTO `wf_role` VALUES ('19', '工程部经理', '0', '1', '0', '');
INSERT INTO `wf_role` VALUES ('20', '市场部经理', '0', '1', '0', '');
INSERT INTO `wf_role` VALUES ('21', '总经理', '0', '1', '0', '');

-- ----------------------------
-- Table structure for wf_role_user
-- ----------------------------
DROP TABLE IF EXISTS `wf_role_user`;
CREATE TABLE `wf_role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` smallint(6) unsigned NOT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_role_user
-- ----------------------------
INSERT INTO `wf_role_user` VALUES ('7', '15');
INSERT INTO `wf_role_user` VALUES ('8', '15');
INSERT INTO `wf_role_user` VALUES ('9', '17');
INSERT INTO `wf_role_user` VALUES ('10', '18');
INSERT INTO `wf_role_user` VALUES ('11', '20');
INSERT INTO `wf_role_user` VALUES ('12', '19');
INSERT INTO `wf_role_user` VALUES ('13', '21');

-- ----------------------------
-- Table structure for wf_run
-- ----------------------------
DROP TABLE IF EXISTS `wf_run`;
CREATE TABLE `wf_run` (
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
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0流程中，1通过,2回退',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `is_sing` int(11) NOT NULL DEFAULT '0',
  `sing_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `pid_flow_step` (`pid_flow_step`),
  KEY `cache_run_id` (`cache_run_id`),
  KEY `uid` (`uid`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run
-- ----------------------------
INSERT INTO `wf_run` VALUES ('5', '0', 'news', '4', '0', '0', '7', '7', '0', '4', '7', '94', '', '1523436079', '1', '0', '0', '1523434156', '0', null);
INSERT INTO `wf_run` VALUES ('6', '0', 'news', '3', '0', '0', '13', '7', '0', '3', '7', '94', '', '1523444896', '1', '0', '0', '1523436227', '0', null);
INSERT INTO `wf_run` VALUES ('7', '0', 'news', '5', '0', '0', '7', '7', '0', '5', '7', '94', '', '1523532596', '1', '0', '0', '1523532403', '0', null);
INSERT INTO `wf_run` VALUES ('8', '0', 'news', '5', '0', '0', '7', '7', '0', '5', '7', '92', '', '0', '0', '0', '0', '1523532423', '0', null);
INSERT INTO `wf_run` VALUES ('9', '0', 'news', '5', '0', '0', '7', '7', '0', '5', '7', '92', '', '0', '0', '0', '0', '1523532488', '0', null);
INSERT INTO `wf_run` VALUES ('10', '0', 'news', '5', '0', '0', '7', '7', '0', '5', '7', '92', '', '0', '0', '0', '0', '1523532532', '0', null);
INSERT INTO `wf_run` VALUES ('11', '0', 'news', '5', '0', '0', '7', '7', '0', '5', '7', '92', '', '0', '0', '0', '0', '1523532549', '0', null);

-- ----------------------------
-- Table structure for wf_run_cache
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_cache`;
CREATE TABLE `wf_run_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 缓存run工作的全部流程模板步骤等信息,确保修改流程后工作依然不变',
  `form_id` int(10) unsigned NOT NULL DEFAULT '0',
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `run_form` text COMMENT '模板信息',
  `run_flow` text COMMENT '流程信息',
  `run_flow_process` text COMMENT '流程步骤信息 ',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `run_id` (`run_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run_cache
-- ----------------------------
INSERT INTO `wf_run_cache` VALUES ('3', '3', '3', '3', '', '{\"id\":3,\"uid\":13,\"add_time\":1522586765,\"new_title\":\"123\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;3123&lt;\\/p&gt;\",\"new_user\":null,\"status\":0,\"uptime\":null}', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10\",\"auto_sponsor_text\":\"\\u65b0\\u95fb\\u90e8\\u7ecf\\u7406\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1522375321,\"dateline\":0}', '0', '0', '1522586819');
INSERT INTO `wf_run_cache` VALUES ('4', '4', '4', '4', '', '{\"id\":4,\"uid\":7,\"add_time\":1523429045,\"new_title\":\"\\u6d41\\u7a0b\\u6d4b\\u8bd5\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;1123132&lt;\\/p&gt;\",\"new_user\":null,\"status\":0,\"uptime\":null}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523428076,\"dateline\":0}', '0', '0', '1523429056');
INSERT INTO `wf_run_cache` VALUES ('5', '5', '4', '4', '', '{\"id\":4,\"uid\":7,\"add_time\":1523429045,\"new_title\":\"\\u6d41\\u7a0b\\u6d4b\\u8bd5\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;1123132&lt;\\/p&gt;\",\"new_user\":null,\"status\":0,\"uptime\":1523429056}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523428076,\"dateline\":0}', '0', '0', '1523434156');
INSERT INTO `wf_run_cache` VALUES ('6', '6', '3', '3', '', '{\"id\":3,\"uid\":13,\"add_time\":1522586765,\"new_title\":\"123\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;3123&lt;\\/p&gt;\",\"new_user\":null,\"status\":0,\"uptime\":1522586819}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523428076,\"dateline\":0}', '0', '0', '1523436227');
INSERT INTO `wf_run_cache` VALUES ('7', '7', '5', '5', '', '{\"id\":5,\"uid\":7,\"add_time\":1523532395,\"new_title\":\"132\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;3123333333&lt;\\/p&gt;\",\"new_user\":null,\"status\":0,\"uptime\":null}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523531489,\"dateline\":0}', '0', '0', '1523532403');
INSERT INTO `wf_run_cache` VALUES ('8', '8', '5', '5', '', '{\"id\":5,\"uid\":7,\"add_time\":1523532395,\"new_title\":\"132\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;3123333333&lt;\\/p&gt;\",\"new_user\":null,\"status\":1,\"uptime\":1523532403}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523531489,\"dateline\":0}', '0', '0', '1523532423');
INSERT INTO `wf_run_cache` VALUES ('9', '9', '5', '5', '', '{\"id\":5,\"uid\":7,\"add_time\":1523532395,\"new_title\":\"132\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;3123333333&lt;\\/p&gt;\",\"new_user\":null,\"status\":1,\"uptime\":1523532423}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523531489,\"dateline\":0}', '0', '0', '1523532488');
INSERT INTO `wf_run_cache` VALUES ('10', '10', '5', '5', '', '{\"id\":5,\"uid\":7,\"add_time\":1523532395,\"new_title\":\"132\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;3123333333&lt;\\/p&gt;\",\"new_user\":null,\"status\":1,\"uptime\":1523532488}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523531489,\"dateline\":0}', '0', '0', '1523532532');
INSERT INTO `wf_run_cache` VALUES ('11', '11', '5', '5', '', '{\"id\":5,\"uid\":7,\"add_time\":1523532395,\"new_title\":\"132\",\"new_type\":1,\"new_top\":1,\"new_con\":\"&lt;p&gt;3123333333&lt;\\/p&gt;\",\"new_user\":null,\"status\":1,\"uptime\":1523532532}', '{\"id\":92,\"flow_id\":7,\"process_name\":\"\\u5e02\\u573a\\u90e8\\u786e\\u8ba4\",\"process_type\":\"is_one\",\"process_to\":\"93\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":554,\"settop\":266,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1523531489,\"dateline\":0}', '0', '0', '1523532549');

-- ----------------------------
-- Table structure for wf_run_log
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_log`;
CREATE TABLE `wf_run_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `from_id` int(11) DEFAULT NULL,
  `from_table` varchar(255) DEFAULT NULL,
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID,子流程时区分run step',
  `content` text NOT NULL COMMENT '日志内容',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `btn` varchar(255) DEFAULT NULL,
  `art` longtext,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run_log
-- ----------------------------
INSERT INTO `wf_run_log` VALUES ('11', '13', '3', 'news', '3', '0', '123123', '1522586819', 'Send', null);
INSERT INTO `wf_run_log` VALUES ('12', '10', '3', 'news', '3', '0', '123', '1522588939', 'ok', null);
INSERT INTO `wf_run_log` VALUES ('13', '11', '3', 'news', '3', '0', '123', '1522589191', 'ok', null);
INSERT INTO `wf_run_log` VALUES ('14', '7', '4', 'news', '4', '0', '123', '1523429056', 'Send', null);
INSERT INTO `wf_run_log` VALUES ('15', '7', '4', 'news', '5', '0', '1', '1523434156', 'Send', null);
INSERT INTO `wf_run_log` VALUES ('16', '7', '4', 'news', '5', '0', '指定工程部', '1523435178', 'ok', null);
INSERT INTO `wf_run_log` VALUES ('17', '12', '4', 'news', '5', '0', '123', '1523436010', 'ok', null);
INSERT INTO `wf_run_log` VALUES ('18', '13', '4', 'news', '5', '0', 'end', '1523436079', 'ok', null);
INSERT INTO `wf_run_log` VALUES ('19', '13', '3', 'news', '6', '0', '1', '1523436227', 'Send', null);
INSERT INTO `wf_run_log` VALUES ('20', '7', '3', 'news', '6', '0', '市场部办理', '1523436301', 'ok', null);
INSERT INTO `wf_run_log` VALUES ('21', '11', '3', 'news', '6', '0', '123', '1523436417', 'ok', null);
INSERT INTO `wf_run_log` VALUES ('22', '13', '3', 'news', '6', '0', '111', '1523444896', 'ok', '20180411\\45411a0668b6d18950a6b1b6b028feb0.png');
INSERT INTO `wf_run_log` VALUES ('23', '7', '5', 'news', '11', '0', '123', '1523532549', 'Send', '');
INSERT INTO `wf_run_log` VALUES ('24', '7', '5', 'news', '7', '0', '123', '1523532560', 'ok', '');
INSERT INTO `wf_run_log` VALUES ('25', '12', '5', 'news', '7', '0', '123', '1523532585', 'ok', '20180412\\c81bd0af75f1f25ede1d36321d863990.png');
INSERT INTO `wf_run_log` VALUES ('26', '13', '5', 'news', '7', '0', '12312', '1523532596', 'ok', '');

-- ----------------------------
-- Table structure for wf_run_process
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_process`;
CREATE TABLE `wf_run_process` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属于那个流程的id',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `parent_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上一步流程',
  `parent_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上一步骤号',
  `run_child` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始转入子流程run_id 如果转入子流程，则在这里也记录',
  `remark` text COMMENT '备注',
  `is_receive_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否先接收人为主办人',
  `auto_person` tinyint(4) DEFAULT NULL,
  `sponsor_text` varchar(255) DEFAULT NULL,
  `sponsor_ids` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run_process
-- ----------------------------
INSERT INTO `wf_run_process` VALUES ('11', '7', '5', '7', '92', '0', '0', '0', '指定工程部', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1523434156', '1523435178', '0', '0', '0', '1523434156');
INSERT INTO `wf_run_process` VALUES ('12', '7', '5', '7', '93', '0', '0', '0', '123', '0', '3', '工程部经理', '12', '0', '0', '0', '2', '1523435178', '1523436010', '0', '0', '0', '1523435178');
INSERT INTO `wf_run_process` VALUES ('13', '12', '5', '7', '94', '0', '0', '0', 'end', '0', '4', '总经理', '13', '0', '0', '0', '2', '1523436010', '1523436079', '0', '0', '0', '1523436010');
INSERT INTO `wf_run_process` VALUES ('14', '13', '6', '7', '92', '0', '0', '0', '市场部办理', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1523436227', '1523436301', '0', '0', '0', '1523436227');
INSERT INTO `wf_run_process` VALUES ('15', '7', '6', '7', '93', '0', '0', '0', '123', '0', '3', '市场部经理', '11', '0', '0', '0', '2', '1523436301', '1523436417', '0', '0', '0', '1523436301');
INSERT INTO `wf_run_process` VALUES ('16', '11', '6', '7', '94', '0', '0', '0', '111', '0', '4', '总经理', '13', '0', '0', '0', '2', '1523436417', '1523444896', '0', '0', '0', '1523436417');
INSERT INTO `wf_run_process` VALUES ('17', '7', '7', '7', '92', '0', '0', '0', '123', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1523532403', '1523532560', '0', '0', '0', '1523532403');
INSERT INTO `wf_run_process` VALUES ('18', '7', '8', '7', '92', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1523532423', '0', '0', '0', '0', '1523532423');
INSERT INTO `wf_run_process` VALUES ('19', '7', '9', '7', '92', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1523532488', '0', '0', '0', '0', '1523532488');
INSERT INTO `wf_run_process` VALUES ('20', '7', '10', '7', '92', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1523532532', '0', '0', '0', '0', '1523532532');
INSERT INTO `wf_run_process` VALUES ('21', '7', '11', '7', '92', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1523532549', '0', '0', '0', '0', '1523532549');
INSERT INTO `wf_run_process` VALUES ('22', '7', '7', '7', '93', '0', '0', '0', '123', '0', '3', '工程部经理', '12', '0', '0', '0', '2', '1523532560', '1523532585', '0', '0', '0', '1523532560');
INSERT INTO `wf_run_process` VALUES ('23', '12', '7', '7', '94', '0', '0', '0', '12312', '0', '4', '总经理', '13', '0', '0', '0', '2', '1523532585', '1523532596', '0', '0', '0', '1523532585');

-- ----------------------------
-- Table structure for wf_run_sign
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_sign`;
CREATE TABLE `wf_run_sign` (
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
-- Records of wf_run_sign
-- ----------------------------

-- ----------------------------
-- Table structure for wf_user
-- ----------------------------
DROP TABLE IF EXISTS `wf_user`;
CREATE TABLE `wf_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `role` smallint(6) unsigned NOT NULL COMMENT '组ID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 1:启用 0:禁止',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `last_login_time` int(11) unsigned NOT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(15) DEFAULT NULL COMMENT '最后登录IP',
  `login_count` int(11) DEFAULT '0',
  `last_location` varchar(100) DEFAULT NULL COMMENT '最后登录位置',
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of wf_user
-- ----------------------------
INSERT INTO `wf_user` VALUES ('7', '市场部员工1', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '15', '0', '1', '1522372036', '127.0.0.1', '0', '新建用户', '1522372036');
INSERT INTO `wf_user` VALUES ('8', '工程部员工1', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '15', '0', '1', '1522372556', '127.0.0.1', '0', '新建用户', '1522372556');
INSERT INTO `wf_user` VALUES ('9', '新闻部员工1', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '17', '0', '1', '1522376353', '127.0.0.1', '0', '新建用户', '1522376353');
INSERT INTO `wf_user` VALUES ('10', '新闻部经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '18', '0', '1', '1522376372', '127.0.0.1', '0', '新建用户', '1522376372');
INSERT INTO `wf_user` VALUES ('11', '市场部经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '20', '0', '1', '1522376385', '127.0.0.1', '0', '新建用户', '1522376385');
INSERT INTO `wf_user` VALUES ('12', '工程部经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '19', '0', '1', '1522376401', '127.0.0.1', '0', '新建用户', '1522376401');
INSERT INTO `wf_user` VALUES ('13', '总经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '21', '0', '1', '1522376413', '127.0.0.1', '0', '新建用户', '1522376413');
