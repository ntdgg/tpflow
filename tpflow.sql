/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1_3306
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : tpflow

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-03-28 11:27:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `leipi_flow`
-- ----------------------------
DROP TABLE IF EXISTS `leipi_flow`;
CREATE TABLE `leipi_flow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL COMMENT '流程类别',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类',
  `form_id` int(10) unsigned NOT NULL DEFAULT '0',
  `flow_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '流程类型0为固定流程1自由流程',
  `flow_name` varchar(255) NOT NULL DEFAULT '' COMMENT '流程名称',
  `flow_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `sort_order` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不可用1正常',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_flow
-- ----------------------------
INSERT INTO `leipi_flow` VALUES ('1', 'tb', '0', '0', '0', '22', '333', '0', '0', '0', '0', '0');
INSERT INTO `leipi_flow` VALUES ('2', 'news', '0', '0', '0', '2222432231111', '33322222311111111111111111', '0', '0', '0', '1416927387', '1416927207');
INSERT INTO `leipi_flow` VALUES ('3', 'news', '0', '52', '0', '测试流程', '测试流程测试流程', '0', '0', '0', '1416930127', '1416927611');
INSERT INTO `leipi_flow` VALUES ('4', 'news', '0', '53', '0', '请假流程', '请假流程', '0', '0', '0', '1418223212', '1416927633');

-- ----------------------------
-- Table structure for `leipi_flow_process`
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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_flow_process
-- ----------------------------
INSERT INTO `leipi_flow_process` VALUES ('70', '4', '开始', 'is_one', '71', '0', '', '1', '0', '', '', '', '', '', '', '4', '1', '10,11,12', '测试1,测试2,测试3', '10,11,12', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"71\":{\"condition\":[],\"condition_desc\":\"\"}}', '436', '215', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521769266', '0');
INSERT INTO `leipi_flow_process` VALUES ('71', '4', '市场部审核', 'is_step', '72', '4', '', '2', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"72\":{\"condition\":[],\"condition_desc\":\"\"}}', '569', '404', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521769266', '0');
INSERT INTO `leipi_flow_process` VALUES ('72', '4', '工程部审核', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '778', '331', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521769266', '0');
INSERT INTO `leipi_flow_process` VALUES ('73', '3', '开始', 'is_one', '74,75', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '{\"74\":{\"condition\":[\"new_type= \'1\'\"],\"condition_desc\":\"\"},\"75\":{\"condition\":[\"new_type= \'2\'\"],\"condition_desc\":\"\"}}', '331', '187', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');
INSERT INTO `leipi_flow_process` VALUES ('74', '3', '市场部', 'is_step', '76', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '600', '143', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');
INSERT INTO `leipi_flow_process` VALUES ('75', '3', '工程部', 'is_step', '76', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '578', '333', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');
INSERT INTO `leipi_flow_process` VALUES ('76', '3', '结束', 'is_step', '', '0', '', '1', '0', '', '', '', '', '', '', '0', '1', '', '', '', '', '', '', '0', '', '', '', '', '', '', '0', '0', '0', '1', '1', '1', '[]', '829', '207', '{\"width\":120,\"height\":30,\"color\":\"#0e76a8\",\"icon\":\"icon-star\"}', '0', '1521687318', '0');

-- ----------------------------
-- Table structure for `leipi_foreign_test`
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
-- Table structure for `leipi_form`
-- ----------------------------
DROP TABLE IF EXISTS `leipi_form`;
CREATE TABLE `leipi_form` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) NOT NULL DEFAULT '' COMMENT '表单名称',
  `form_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '表单描述',
  `content` text NOT NULL COMMENT '表单原html模板未经处理的',
  `content_parse` text NOT NULL COMMENT '表单替换的模板 经过处理',
  `content_data` text NOT NULL COMMENT '表单中的字段数据',
  `fields` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '字段总数',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'is_del',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_form
-- ----------------------------
INSERT INTO `leipi_form` VALUES ('52', '示例表', '示例', '<p style=\"text-align: center;\"><br/></p><p style=\"text-align: center;\"><span style=\"font-size: 24px;\">示例表</span></p><table class=\"table table-bordered\"><tbody><tr class=\"firstRow\"><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">文本框</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"227\"><input style=\"text-align: left; width: 150px;\" title=\"文本框\" value=\"雷劈网\" name=\"data_12\" orgheight=\"\" orgwidth=\"150\" orgalign=\"left\" orgfontsize=\"\" orghide=\"0\" leipiplugins=\"text\" orgtype=\"text\"/></td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">下拉菜单</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\">{|-<span leipiplugins=\"select\"><select name=\"data_13\" title=\"下拉菜单\" leipiplugins=\"select\" size=\"1\" orgwidth=\"150\" style=\"width: 150px;\"><option value=\"下拉\">下拉</option><option value=\"菜单\">菜单</option></select>&nbsp;&nbsp;</span>-|}</td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">单选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"41\">{|-<span leipiplugins=\"radios\" name=\"data_14\" title=\"单选\"><input type=\"radio\" name=\"data_14\" value=\"单选2\"  checked=\"checked\"/>单选2&nbsp;<input type=\"radio\" name=\"data_14\" value=\"单选1\"  />单选1&nbsp;</span>-|}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">复选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"312\">{|-<span leipiplugins=\"checkboxs\" title=\"复选\"><input type=\"checkbox\" name=\"data_16\" value=\"复选2\" />复选2&nbsp;<input type=\"checkbox\" name=\"data_15\" value=\"复选1\" />复选1&nbsp;<input type=\"checkbox\" name=\"data_17\" value=\"复选3\" />复选3&nbsp;</span>-|}</td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">宏控件</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"41\"><input name=\"data_18\" type=\"text\" value=\"{macros}\" title=\"宏控件\" leipiplugins=\"macros\" orgtype=\"sys_date_cn\" orghide=\"0\" orgfontsize=\"12\" orgwidth=\"150\" style=\"font-size: 12px; width: 150px;\"/></td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">二维码</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\"><img name=\"data_19\" title=\"雷劈网\" value=\"http://www.leipi.org\" orgtype=\"url\" leipiplugins=\"qrcode\" src=\"/Public/js/ueditor/formdesign/images/qrcode.gif\" orgwidth=\"40\" orgheight=\"40\" style=\"width: 40px; height: 40px;\"/></td></tr></tbody></table><p><textarea title=\"多行文本\" name=\"data_20\" leipiplugins=\"textarea\" value=\"\" orgrich=\"0\" orgfontsize=\"12\" orgwidth=\"600\" orgheight=\"80\" style=\"font-size:12px;width:600px;height:80px;\"></textarea></p><p><img name=\"data_21\" title=\"进度条\" leipiplugins=\"progressbar\" orgvalue=\"20\" orgsigntype=\"progress-info\" src=\"/Public/js/ueditor/formdesign/images/progressbar.gif\"/></p>', '<p style=\"text-align: center;\"><br/></p><p style=\"text-align: center;\"><span style=\"font-size: 24px;\">示例表</span></p><table class=\"table table-bordered\"><tbody><tr class=\"firstRow\"><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">文本框</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"227\">{data_12}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">下拉菜单</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\">{data_13}</td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">单选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"41\">{data_14}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">复选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"312\">{checkboxs_0}</td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">宏控件</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"41\">{data_18}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">二维码</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\">{data_19}</td></tr></tbody></table><p>{data_20}</p><p>{data_21}</p>', 'a:8:{i:0;a:12:{s:5:\"style\";s:31:\"text-align: left; width: 150px;\";s:5:\"title\";s:9:\"文本框\";s:5:\"value\";s:9:\"雷劈网\";s:4:\"name\";s:7:\"data_12\";s:9:\"orgheight\";s:0:\"\";s:8:\"orgwidth\";s:3:\"150\";s:8:\"orgalign\";s:4:\"left\";s:11:\"orgfontsize\";s:0:\"\";s:7:\"orghide\";s:1:\"0\";s:12:\"leipiplugins\";s:4:\"text\";s:7:\"orgtype\";s:4:\"text\";s:7:\"content\";s:205:\"<input style=\"text-align: left; width: 150px;\" title=\"文本框\" value=\"雷劈网\" name=\"data_12\" orgheight=\"\" orgwidth=\"150\" orgalign=\"left\" orgfontsize=\"\" orghide=\"0\" leipiplugins=\"text\" orgtype=\"text\"/>\";}i:1;a:8:{s:12:\"leipiplugins\";s:6:\"select\";s:4:\"name\";s:7:\"data_13\";s:5:\"title\";s:12:\"下拉菜单\";s:4:\"size\";s:1:\"1\";s:8:\"orgwidth\";s:3:\"150\";s:5:\"style\";s:13:\"width: 150px;\";s:5:\"value\";s:13:\"下拉,菜单\";s:7:\"content\";s:244:\"<span leipiplugins=\"select\"><select name=\"data_13\" title=\"下拉菜单\" leipiplugins=\"select\" size=\"1\" orgwidth=\"150\" style=\"width: 150px;\"><option value=\"下拉\">下拉</option><option value=\"菜单\">菜单</option></select>&nbsp;&nbsp;</span>\";}i:2;a:6:{s:12:\"leipiplugins\";s:6:\"radios\";s:4:\"name\";s:7:\"data_14\";s:5:\"title\";s:6:\"单选\";s:5:\"value\";s:15:\"单选2,单选1\";s:7:\"content\";s:216:\"<span leipiplugins=\"radios\" name=\"data_14\" title=\"单选\"><input type=\"radio\" name=\"data_14\" value=\"单选2\"  checked=\"checked\"/>单选2&nbsp;<input type=\"radio\" name=\"data_14\" value=\"单选1\"  />单选1&nbsp;</span>\";s:7:\"options\";a:2:{i:0;a:4:{s:4:\"type\";s:5:\"radio\";s:4:\"name\";s:7:\"data_14\";s:5:\"value\";s:7:\"单选2\";s:7:\"checked\";s:7:\"checked\";}i:1;a:3:{s:4:\"type\";s:5:\"radio\";s:4:\"name\";s:7:\"data_14\";s:5:\"value\";s:7:\"单选1\";}}}i:3;a:8:{s:12:\"leipiplugins\";s:9:\"checkboxs\";s:5:\"title\";s:6:\"复选\";s:10:\"orgchecked\";s:11:\"orgchecked0\";s:10:\"parse_name\";s:11:\"checkboxs_0\";s:4:\"name\";s:23:\"data_16,data_15,data_17\";s:5:\"value\";s:23:\"复选2,复选1,复选3\";s:7:\"content\";s:260:\"<span leipiplugins=\"checkboxs\" title=\"复选\"><input type=\"checkbox\" name=\"data_16\" value=\"复选2\" />复选2&nbsp;<input type=\"checkbox\" name=\"data_15\" value=\"复选1\" />复选1&nbsp;<input type=\"checkbox\" name=\"data_17\" value=\"复选3\" />复选3&nbsp;</span>\";s:7:\"options\";a:3:{i:0;a:3:{s:4:\"name\";s:7:\"data_16\";s:5:\"value\";s:7:\"复选2\";s:4:\"type\";s:8:\"checkbox\";}i:1;a:3:{s:4:\"name\";s:7:\"data_15\";s:5:\"value\";s:7:\"复选1\";s:4:\"type\";s:8:\"checkbox\";}i:2;a:3:{s:4:\"name\";s:7:\"data_17\";s:5:\"value\";s:7:\"复选3\";s:4:\"type\";s:8:\"checkbox\";}}}i:4;a:11:{s:4:\"name\";s:7:\"data_18\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:8:\"{macros}\";s:5:\"title\";s:9:\"宏控件\";s:12:\"leipiplugins\";s:6:\"macros\";s:7:\"orgtype\";s:11:\"sys_date_cn\";s:7:\"orghide\";s:1:\"0\";s:11:\"orgfontsize\";s:2:\"12\";s:8:\"orgwidth\";s:3:\"150\";s:5:\"style\";s:30:\"font-size: 12px; width: 150px;\";s:7:\"content\";s:197:\"<input name=\"data_18\" type=\"text\" value=\"{macros}\" title=\"宏控件\" leipiplugins=\"macros\" orgtype=\"sys_date_cn\" orghide=\"0\" orgfontsize=\"12\" orgwidth=\"150\" style=\"font-size: 12px; width: 150px;\"/>\";}i:5;a:10:{s:4:\"name\";s:7:\"data_19\";s:5:\"title\";s:9:\"雷劈网\";s:5:\"value\";s:20:\"http://www.leipi.org\";s:7:\"orgtype\";s:3:\"url\";s:12:\"leipiplugins\";s:6:\"qrcode\";s:3:\"src\";s:47:\"/Public/js/ueditor/formdesign/images/qrcode.gif\";s:8:\"orgwidth\";s:2:\"40\";s:9:\"orgheight\";s:2:\"40\";s:5:\"style\";s:26:\"width: 40px; height: 40px;\";s:7:\"content\";s:222:\"<img name=\"data_19\" title=\"雷劈网\" value=\"http://www.leipi.org\" orgtype=\"url\" leipiplugins=\"qrcode\" src=\"/Public/js/ueditor/formdesign/images/qrcode.gif\" orgwidth=\"40\" orgheight=\"40\" style=\"width: 40px; height: 40px;\"/>\";}i:6;a:10:{s:5:\"title\";s:12:\"多行文本\";s:4:\"name\";s:7:\"data_20\";s:12:\"leipiplugins\";s:8:\"textarea\";s:5:\"value\";s:0:\"\";s:7:\"orgrich\";s:1:\"0\";s:11:\"orgfontsize\";s:2:\"12\";s:8:\"orgwidth\";s:3:\"600\";s:9:\"orgheight\";s:2:\"80\";s:5:\"style\";s:39:\"font-size:12px;width:600px;height:80px;\";s:7:\"content\";s:197:\"<textarea title=\"多行文本\" name=\"data_20\" leipiplugins=\"textarea\" value=\"\" orgrich=\"0\" orgfontsize=\"12\" orgwidth=\"600\" orgheight=\"80\" style=\"font-size:12px;width:600px;height:80px;\"></textarea>\";}i:7;a:7:{s:4:\"name\";s:7:\"data_21\";s:5:\"title\";s:9:\"进度条\";s:12:\"leipiplugins\";s:11:\"progressbar\";s:8:\"orgvalue\";s:2:\"20\";s:11:\"orgsigntype\";s:13:\"progress-info\";s:3:\"src\";s:52:\"/Public/js/ueditor/formdesign/images/progressbar.gif\";s:7:\"content\";s:167:\"<img name=\"data_21\" title=\"进度条\" leipiplugins=\"progressbar\" orgvalue=\"20\" orgsigntype=\"progress-info\" src=\"/Public/js/ueditor/formdesign/images/progressbar.gif\"/>\";}}', '21', '0', '1408597636', '1407338057');
INSERT INTO `leipi_form` VALUES ('53', '请假申请', '但', '<p style=\"text-align: center;\"><br/></p><p style=\"text-align: center;\"><span style=\"font-size: 24px;\">示例表</span></p><table class=\"table table-bordered\"><tbody><tr class=\"firstRow\"><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">文本框</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"227\"><input style=\"text-align: left; width: 150px;\" title=\"文本框\" value=\"雷劈网\" name=\"data_1\" orgheight=\"\" orgwidth=\"150\" orgalign=\"left\" orgfontsize=\"\" orghide=\"0\" leipiplugins=\"text\" orgtype=\"text\"/></td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">下拉菜单</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\">{|-<span leipiplugins=\"select\"><select name=\"data_2\" title=\"下拉菜单\" leipiplugins=\"select\" size=\"1\" orgwidth=\"150\" style=\"width: 150px;\"><option value=\"下拉\">下拉</option><option value=\"菜单\" selected=\"selected\">菜单</option></select>&nbsp;&nbsp;</span>-|}</td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">单选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"41\">{|-<span leipiplugins=\"radios\" name=\"data_3\" title=\"单选\"><input type=\"radio\" name=\"data_3\" value=\"单选1\"  />单选1&nbsp;<input type=\"radio\" name=\"data_3\" value=\"单选2\"  />单选2&nbsp;<input type=\"radio\" name=\"data_3\" value=\"3wq2\"  checked=\"checked\"/>3wq2&nbsp;<input type=\"radio\" name=\"data_3\" value=\"sdf\"  />sdf&nbsp;</span>-|}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">复选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"312\">{|-<span leipiplugins=\"checkboxs\" title=\"复选\"><input type=\"checkbox\" name=\"data_4\" value=\"复选1\" />复选1&nbsp;<input type=\"checkbox\" name=\"data_5\" value=\"复选2\" />复选2&nbsp;<input type=\"checkbox\" name=\"data_6\" value=\"复选3\" checked=\"checked\"/>复选3&nbsp;</span>-|}</td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">宏控件</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"41\"><input name=\"data_7\" type=\"text\" value=\"{macros}\" title=\"宏控件\" leipiplugins=\"macros\" orgtype=\"sys_date_cn\" orghide=\"0\" orgfontsize=\"12\" orgwidth=\"150\" style=\"font-size: 12px; width: 150px;\"/></td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">二维码</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\"><img name=\"data_8\" title=\"雷劈网\" value=\"http://www.leipi.org\" orgtype=\"url\" leipiplugins=\"qrcode\" src=\"/Public/js/ueditor/formdesign/images/qrcode.gif\" orgwidth=\"40\" orgheight=\"40\" style=\"width: 40px; height: 40px;\"/></td></tr></tbody></table><p><textarea title=\"多行文本\" name=\"data_9\" leipiplugins=\"textarea\" value=\"\" orgrich=\"0\" orgfontsize=\"12\" orgwidth=\"600\" orgheight=\"80\" style=\"font-size:12px;width:600px;height:80px;\"></textarea></p><p><img name=\"data_10\" title=\"进度条\" leipiplugins=\"progressbar\" orgvalue=\"20\" orgsigntype=\"progress-info\" src=\"/Public/js/ueditor/formdesign/images/progressbar.gif\"/></p>', '<p style=\"text-align: center;\"><br/></p><p style=\"text-align: center;\"><span style=\"font-size: 24px;\">示例表</span></p><table class=\"table table-bordered\"><tbody><tr class=\"firstRow\"><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">文本框</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"227\">{data_1}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">下拉菜单</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\"><span leipiplugins=\"select\">{data_2}&nbsp;&nbsp;</span></td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">单选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"41\">{data_3}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">复选</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"312\">{checkboxs_0}</td></tr><tr><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\">宏控件</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"41\">{data_7}</td><td valign=\"top\" style=\"word-break: break-all; border-color: rgb(221, 221, 221);\" width=\"85\">二维码</td><td valign=\"top\" style=\"border-color: rgb(221, 221, 221);\" width=\"312\">{data_8}</td></tr></tbody></table><p>{data_9}</p><p>{data_10}</p>', 'a:8:{i:0;a:12:{s:5:\"style\";s:31:\"text-align: left; width: 150px;\";s:5:\"title\";s:9:\"文本框\";s:5:\"value\";s:9:\"雷劈网\";s:4:\"name\";s:6:\"data_1\";s:9:\"orgheight\";s:0:\"\";s:8:\"orgwidth\";s:3:\"150\";s:8:\"orgalign\";s:4:\"left\";s:11:\"orgfontsize\";s:0:\"\";s:7:\"orghide\";s:1:\"0\";s:12:\"leipiplugins\";s:4:\"text\";s:7:\"orgtype\";s:4:\"text\";s:7:\"content\";s:204:\"<input style=\"text-align: left; width: 150px;\" title=\"文本框\" value=\"雷劈网\" name=\"data_1\" orgheight=\"\" orgwidth=\"150\" orgalign=\"left\" orgfontsize=\"\" orghide=\"0\" leipiplugins=\"text\" orgtype=\"text\"/>\";}i:1;a:9:{s:4:\"name\";s:6:\"data_2\";s:5:\"title\";s:12:\"下拉菜单\";s:12:\"leipiplugins\";s:6:\"select\";s:4:\"size\";s:1:\"1\";s:8:\"orgwidth\";s:3:\"150\";s:5:\"style\";s:13:\"width: 150px;\";s:5:\"value\";s:13:\"下拉,菜单\";s:8:\"selected\";s:8:\"selected\";s:7:\"content\";s:216:\"<select name=\"data_2\" title=\"下拉菜单\" leipiplugins=\"select\" size=\"1\" orgwidth=\"150\" style=\"width: 150px;\"><option value=\"下拉\">下拉</option><option value=\"菜单\" selected=\"selected\">菜单</option></select>\";}i:2;a:7:{s:12:\"leipiplugins\";s:6:\"radios\";s:4:\"name\";s:6:\"data_3\";s:5:\"title\";s:6:\"单选\";s:10:\"orgchecked\";s:11:\"orgchecked0\";s:5:\"value\";s:24:\"单选1,单选2,3wq2,sdf\";s:7:\"content\";s:331:\"<span leipiplugins=\"radios\" name=\"data_3\" title=\"单选\"><input type=\"radio\" name=\"data_3\" value=\"单选1\"  />单选1&nbsp;<input type=\"radio\" name=\"data_3\" value=\"单选2\"  />单选2&nbsp;<input type=\"radio\" name=\"data_3\" value=\"3wq2\"  checked=\"checked\"/>3wq2&nbsp;<input type=\"radio\" name=\"data_3\" value=\"sdf\"  />sdf&nbsp;</span>\";s:7:\"options\";a:4:{i:0;a:3:{s:4:\"name\";s:6:\"data_3\";s:5:\"value\";s:7:\"单选1\";s:4:\"type\";s:5:\"radio\";}i:1;a:3:{s:4:\"name\";s:6:\"data_3\";s:5:\"value\";s:7:\"单选2\";s:4:\"type\";s:5:\"radio\";}i:2;a:4:{s:4:\"name\";s:6:\"data_3\";s:5:\"value\";s:4:\"3wq2\";s:7:\"checked\";s:7:\"checked\";s:4:\"type\";s:5:\"radio\";}i:3;a:3:{s:4:\"name\";s:6:\"data_3\";s:5:\"value\";s:3:\"sdf\";s:4:\"type\";s:5:\"radio\";}}}i:3;a:7:{s:12:\"leipiplugins\";s:9:\"checkboxs\";s:5:\"title\";s:6:\"复选\";s:10:\"parse_name\";s:11:\"checkboxs_0\";s:4:\"name\";s:20:\"data_4,data_5,data_6\";s:5:\"value\";s:23:\"复选1,复选2,复选3\";s:7:\"content\";s:274:\"<span leipiplugins=\"checkboxs\" title=\"复选\"><input type=\"checkbox\" name=\"data_4\" value=\"复选1\" />复选1&nbsp;<input type=\"checkbox\" name=\"data_5\" value=\"复选2\" />复选2&nbsp;<input type=\"checkbox\" name=\"data_6\" value=\"复选3\" checked=\"checked\"/>复选3&nbsp;</span>\";s:7:\"options\";a:3:{i:0;a:3:{s:4:\"type\";s:8:\"checkbox\";s:4:\"name\";s:6:\"data_4\";s:5:\"value\";s:7:\"复选1\";}i:1;a:3:{s:4:\"type\";s:8:\"checkbox\";s:4:\"name\";s:6:\"data_5\";s:5:\"value\";s:7:\"复选2\";}i:2;a:4:{s:4:\"type\";s:8:\"checkbox\";s:4:\"name\";s:6:\"data_6\";s:5:\"value\";s:7:\"复选3\";s:7:\"checked\";s:7:\"checked\";}}}i:4;a:11:{s:4:\"name\";s:6:\"data_7\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:8:\"{macros}\";s:5:\"title\";s:9:\"宏控件\";s:12:\"leipiplugins\";s:6:\"macros\";s:7:\"orgtype\";s:11:\"sys_date_cn\";s:7:\"orghide\";s:1:\"0\";s:11:\"orgfontsize\";s:2:\"12\";s:8:\"orgwidth\";s:3:\"150\";s:5:\"style\";s:30:\"font-size: 12px; width: 150px;\";s:7:\"content\";s:196:\"<input name=\"data_7\" type=\"text\" value=\"{macros}\" title=\"宏控件\" leipiplugins=\"macros\" orgtype=\"sys_date_cn\" orghide=\"0\" orgfontsize=\"12\" orgwidth=\"150\" style=\"font-size: 12px; width: 150px;\"/>\";}i:5;a:10:{s:4:\"name\";s:6:\"data_8\";s:5:\"title\";s:9:\"雷劈网\";s:5:\"value\";s:20:\"http://www.leipi.org\";s:7:\"orgtype\";s:3:\"url\";s:12:\"leipiplugins\";s:6:\"qrcode\";s:3:\"src\";s:47:\"/Public/js/ueditor/formdesign/images/qrcode.gif\";s:8:\"orgwidth\";s:2:\"40\";s:9:\"orgheight\";s:2:\"40\";s:5:\"style\";s:26:\"width: 40px; height: 40px;\";s:7:\"content\";s:221:\"<img name=\"data_8\" title=\"雷劈网\" value=\"http://www.leipi.org\" orgtype=\"url\" leipiplugins=\"qrcode\" src=\"/Public/js/ueditor/formdesign/images/qrcode.gif\" orgwidth=\"40\" orgheight=\"40\" style=\"width: 40px; height: 40px;\"/>\";}i:6;a:10:{s:5:\"title\";s:12:\"多行文本\";s:4:\"name\";s:6:\"data_9\";s:12:\"leipiplugins\";s:8:\"textarea\";s:5:\"value\";s:0:\"\";s:7:\"orgrich\";s:1:\"0\";s:11:\"orgfontsize\";s:2:\"12\";s:8:\"orgwidth\";s:3:\"600\";s:9:\"orgheight\";s:2:\"80\";s:5:\"style\";s:39:\"font-size:12px;width:600px;height:80px;\";s:7:\"content\";s:196:\"<textarea title=\"多行文本\" name=\"data_9\" leipiplugins=\"textarea\" value=\"\" orgrich=\"0\" orgfontsize=\"12\" orgwidth=\"600\" orgheight=\"80\" style=\"font-size:12px;width:600px;height:80px;\"></textarea>\";}i:7;a:7:{s:4:\"name\";s:7:\"data_10\";s:5:\"title\";s:9:\"进度条\";s:12:\"leipiplugins\";s:11:\"progressbar\";s:8:\"orgvalue\";s:2:\"20\";s:11:\"orgsigntype\";s:13:\"progress-info\";s:3:\"src\";s:52:\"/Public/js/ueditor/formdesign/images/progressbar.gif\";s:7:\"content\";s:167:\"<img name=\"data_10\" title=\"进度条\" leipiplugins=\"progressbar\" orgvalue=\"20\" orgsigntype=\"progress-info\" src=\"/Public/js/ueditor/formdesign/images/progressbar.gif\"/>\";}}', '10', '0', '1417850584', '1407601538');
INSERT INTO `leipi_form` VALUES ('54', 'test', 'test', '<p style=\"text-align: center;\">表单</p><table><tbody><tr class=\"firstRow\"><td style=\"word-break: break-all;\" valign=\"top\" width=\"216\">用户名<br/></td><td valign=\"top\" width=\"216\"><input orgtype=\"text\" orgwidth=\"150\" orgalign=\"left\" style=\"text-align: left; width: 150px;\" orghide=\"0\" leipiplugins=\"text\" value=\"\" title=\"用户名\" name=\"data_1\" type=\"text\"/></td><td style=\"word-break: break-all;\" valign=\"top\" width=\"216\">年龄<br/></td><td valign=\"top\" width=\"216\"><input orgtype=\"text\" orgwidth=\"150\" orgalign=\"left\" style=\"text-align: left; width: 150px;\" orghide=\"0\" leipiplugins=\"text\" value=\"\" title=\"年龄\" name=\"data_2\" type=\"text\"/></td></tr><tr><td rowspan=\"1\" colspan=\"1\" valign=\"top\">{|-<span leipiplugins=\"radios\" name=\"data_7\" title=\"22\"><input type=\"radio\" name=\"data_7\" value=\"33\"  />33&nbsp;<input type=\"radio\" name=\"data_7\" value=\"44\"  />44&nbsp;</span>-|}</td><td rowspan=\"1\" colspan=\"1\" valign=\"top\"><br/></td><td rowspan=\"1\" colspan=\"1\" valign=\"top\"><br/></td><td rowspan=\"1\" colspan=\"1\" valign=\"top\"><br/></td></tr></tbody></table><p><input name=\"data_8\" leipiplugins=\"listctrl\" type=\"text\" value=\"{列表控件}\" readonly=\"readonly\" title=\"列表控件1\" orgtitle=\"11`22`33`\" orgcoltype=\"text`int`textarea`\" orgunit=\"个`个`个`\" orgsum=\"0`1`0`\" orgcolvalue=\"```\" orgwidth=\"500px\" style=\"width: 500px;\"/></p>', '<p style=\"text-align: center;\">表单</p><table><tbody><tr class=\"firstRow\"><td style=\"word-break: break-all;\" valign=\"top\" width=\"216\">用户名<br/></td><td valign=\"top\" width=\"216\">{data_1}</td><td style=\"word-break: break-all;\" valign=\"top\" width=\"216\">年龄<br/></td><td valign=\"top\" width=\"216\">{data_2}</td></tr><tr><td rowspan=\"1\" colspan=\"1\" valign=\"top\">{data_7}</td><td rowspan=\"1\" colspan=\"1\" valign=\"top\"><br/></td><td rowspan=\"1\" colspan=\"1\" valign=\"top\"><br/></td><td rowspan=\"1\" colspan=\"1\" valign=\"top\"><br/></td></tr></tbody></table><p>{data_8}</p>', 'a:4:{i:0;a:11:{s:7:\"orgtype\";s:4:\"text\";s:8:\"orgwidth\";s:3:\"150\";s:8:\"orgalign\";s:4:\"left\";s:5:\"style\";s:31:\"text-align: left; width: 150px;\";s:7:\"orghide\";s:1:\"0\";s:12:\"leipiplugins\";s:4:\"text\";s:5:\"value\";s:0:\"\";s:5:\"title\";s:9:\"用户名\";s:4:\"name\";s:6:\"data_1\";s:4:\"type\";s:4:\"text\";s:7:\"content\";s:179:\"<input orgtype=\"text\" orgwidth=\"150\" orgalign=\"left\" style=\"text-align: left; width: 150px;\" orghide=\"0\" leipiplugins=\"text\" value=\"\" title=\"用户名\" name=\"data_1\" type=\"text\"/>\";}i:1;a:11:{s:7:\"orgtype\";s:4:\"text\";s:8:\"orgwidth\";s:3:\"150\";s:8:\"orgalign\";s:4:\"left\";s:5:\"style\";s:31:\"text-align: left; width: 150px;\";s:7:\"orghide\";s:1:\"0\";s:12:\"leipiplugins\";s:4:\"text\";s:5:\"value\";s:0:\"\";s:5:\"title\";s:6:\"年龄\";s:4:\"name\";s:6:\"data_2\";s:4:\"type\";s:4:\"text\";s:7:\"content\";s:176:\"<input orgtype=\"text\" orgwidth=\"150\" orgalign=\"left\" style=\"text-align: left; width: 150px;\" orghide=\"0\" leipiplugins=\"text\" value=\"\" title=\"年龄\" name=\"data_2\" type=\"text\"/>\";}i:2;a:6:{s:12:\"leipiplugins\";s:6:\"radios\";s:4:\"name\";s:6:\"data_7\";s:5:\"title\";s:2:\"22\";s:5:\"value\";s:5:\"33,44\";s:7:\"content\";s:172:\"<span leipiplugins=\"radios\" name=\"data_7\" title=\"22\"><input type=\"radio\" name=\"data_7\" value=\"33\"  />33&nbsp;<input type=\"radio\" name=\"data_7\" value=\"44\"  />44&nbsp;</span>\";s:7:\"options\";a:2:{i:0;a:3:{s:4:\"type\";s:5:\"radio\";s:4:\"name\";s:6:\"data_7\";s:5:\"value\";s:2:\"33\";}i:1;a:3:{s:4:\"type\";s:5:\"radio\";s:4:\"name\";s:6:\"data_7\";s:5:\"value\";s:2:\"44\";}}}i:3;a:14:{s:4:\"name\";s:6:\"data_8\";s:12:\"leipiplugins\";s:8:\"listctrl\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:14:\"{列表控件}\";s:8:\"readonly\";s:8:\"readonly\";s:5:\"title\";s:13:\"列表控件1\";s:8:\"orgtitle\";s:9:\"11`22`33`\";s:10:\"orgcoltype\";s:18:\"text`int`textarea`\";s:7:\"orgunit\";s:12:\"个`个`个`\";s:6:\"orgsum\";s:6:\"0`1`0`\";s:11:\"orgcolvalue\";s:3:\"```\";s:8:\"orgwidth\";s:5:\"500px\";s:5:\"style\";s:13:\"width: 500px;\";s:7:\"content\";s:272:\"<input name=\"data_8\" leipiplugins=\"listctrl\" type=\"text\" value=\"{列表控件}\" readonly=\"readonly\" title=\"列表控件1\" orgtitle=\"11`22`33`\" orgcoltype=\"text`int`textarea`\" orgunit=\"个`个`个`\" orgsum=\"0`1`0`\" orgcolvalue=\"```\" orgwidth=\"500px\" style=\"width: 500px;\"/>\";}}', '8', '0', '1419990904', '1408069450');

-- ----------------------------
-- Table structure for `leipi_form_data_52`
-- ----------------------------
DROP TABLE IF EXISTS `leipi_form_data_52`;
CREATE TABLE `leipi_form_data_52` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `foreign_id` int(10) unsigned NOT NULL DEFAULT '0',
  `data_1` varchar(255) NOT NULL DEFAULT '',
  `data_2` varchar(255) NOT NULL DEFAULT '',
  `data_3` varchar(255) NOT NULL DEFAULT '',
  `data_5` varchar(255) NOT NULL DEFAULT '',
  `data_6` varchar(255) NOT NULL DEFAULT '',
  `data_7` varchar(255) NOT NULL DEFAULT '',
  `data_8` varchar(255) NOT NULL DEFAULT '',
  `data_9` varchar(255) NOT NULL DEFAULT '',
  `data_10` text NOT NULL,
  `data_11` varchar(255) NOT NULL DEFAULT '',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `data_12` varchar(255) NOT NULL DEFAULT '',
  `data_13` varchar(255) NOT NULL DEFAULT '',
  `data_14` varchar(255) NOT NULL DEFAULT '',
  `data_15` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data_16` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data_17` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data_18` varchar(255) NOT NULL DEFAULT '',
  `data_19` varchar(255) NOT NULL DEFAULT '',
  `data_20` text NOT NULL,
  `data_21` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `foreign_id` (`foreign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_form_data_52
-- ----------------------------
INSERT INTO `leipi_form_data_52` VALUES ('1', '0', '13', '雷劈网', '菜单', '单选2', '0', '1', '1', '2014年8月7日', 'a:2:{s:5:\"value\";s:28:\"http://formdesign.leipi.org/\";s:10:\"qrcode_url\";s:45:\"/Uploads/90e1157fb525881f97148e8e9740ed5e.png\";}', '夺3333', '50', '0', '1407556818', '1407338095', '2222', '下拉', '单选2', '1', '0', '1', '2014年8月9日', 'a:2:{s:5:\"value\";s:3:\"333\";s:10:\"qrcode_url\";s:45:\"/Uploads/310dcbbf4cce62f762a2aaa148d556bd.png\";}', '顶替', '20');
INSERT INTO `leipi_form_data_52` VALUES ('2', '0', '14', '', '', '', '', '', '', '', '', '', '', '0', '1407557049', '1407556896', '雷劈网5', '菜单', '单选2', '1', '0', '1', '2014年8月8日', 'a:2:{s:5:\"value\";s:20:\"http://form/index.ph\";s:10:\"qrcode_url\";s:45:\"/Uploads/1a4363a0562a619d95fa661d80c19e01.png\";}', '顶替333333', '50');
INSERT INTO `leipi_form_data_52` VALUES ('3', '0', '15', '', '', '', '', '', '', '', '', '', '', '0', '1408597541', '1408597541', '雷劈网33', '下拉', '单选2', '1', '1', '1', '2014年8月21日', 'a:2:{s:5:\"value\";s:0:\"\";s:10:\"qrcode_url\";s:0:\"\";}', '2332', '20');

-- ----------------------------
-- Table structure for `leipi_form_data_53`
-- ----------------------------
DROP TABLE IF EXISTS `leipi_form_data_53`;
CREATE TABLE `leipi_form_data_53` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `foreign_id` int(10) unsigned NOT NULL DEFAULT '0',
  `data_1` varchar(255) NOT NULL DEFAULT '',
  `data_2` varchar(255) NOT NULL DEFAULT '',
  `data_3` varchar(255) NOT NULL DEFAULT '',
  `data_4` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data_5` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data_6` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `data_7` varchar(255) NOT NULL DEFAULT '',
  `data_8` varchar(255) NOT NULL DEFAULT '',
  `data_9` text NOT NULL,
  `data_10` varchar(255) NOT NULL DEFAULT '',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `foreign_id` (`foreign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_form_data_53
-- ----------------------------

-- ----------------------------
-- Table structure for `leipi_form_data_54`
-- ----------------------------
DROP TABLE IF EXISTS `leipi_form_data_54`;
CREATE TABLE `leipi_form_data_54` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `foreign_id` int(10) unsigned NOT NULL DEFAULT '0',
  `data_1` varchar(255) NOT NULL DEFAULT '',
  `data_2` varchar(255) NOT NULL DEFAULT '',
  `data_3` varchar(255) NOT NULL DEFAULT '',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `data_4` varchar(255) NOT NULL DEFAULT '',
  `data_5` varchar(255) NOT NULL DEFAULT '',
  `data_6` varchar(255) NOT NULL DEFAULT '',
  `data_7` varchar(255) NOT NULL DEFAULT '',
  `data_8` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `foreign_id` (`foreign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_form_data_54
-- ----------------------------
INSERT INTO `leipi_form_data_54` VALUES ('1', '0', '16', '11', '33', '', '0', '1410971141', '1410970367', '', '', '', '44', 'a:2:{s:4:\"list\";a:4:{i:0;a:3:{i:0;s:2:\"11\";i:1;d:22;i:2;s:2:\"33\";}i:1;a:3:{i:0;s:1:\"3\";i:1;d:3;i:2;s:1:\"2\";}i:2;a:3:{i:0;s:2:\"23\";i:1;d:4;i:2;s:3:\"334\";}i:3;a:3:{i:0;s:1:\"4\";i:1;d:3;i:2;s:2:\"44\";}}s:3:\"sum\";a:1:{i:1;s:2:\"22\";}}');
INSERT INTO `leipi_form_data_54` VALUES ('2', '0', '17', 'sdf', 'sdf', '', '0', '1410971511', '1410971183', '', '', '', '44', 'a:2:{s:4:\"list\";a:3:{i:0;a:3:{i:0;s:2:\"2f\";i:1;d:33;i:2;s:2:\"4w\";}i:1;a:3:{i:0;s:2:\"3f\";i:1;d:43;i:2;s:2:\"4f\";}i:2;a:3:{i:0;s:4:\"asdf\";i:1;d:0;i:2;s:3:\"sdf\";}}s:3:\"sum\";a:1:{i:1;s:2:\"76\";}}');

-- ----------------------------
-- Table structure for `leipi_news`
-- ----------------------------
DROP TABLE IF EXISTS `leipi_news`;
CREATE TABLE `leipi_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `add_time` int(11) DEFAULT NULL,
  `new_title` varchar(255) DEFAULT NULL,
  `new_type` int(11) DEFAULT NULL,
  `new_top` int(11) NOT NULL DEFAULT '0',
  `new_con` longtext,
  `new_user` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_news
-- ----------------------------
INSERT INTO `leipi_news` VALUES ('1', '1', '1520592850', '11', '1', '1', '<p>213</p>', null);
INSERT INTO `leipi_news` VALUES ('2', '1', '1520592878', '11', '1', '1', '<p>			</p><p><br/></p><p>213123123333333333333333333333333333333333333333333333333333333333333</p><p><br/></p><p>213123123333333333333333333333333333333333333333333333333333333333333</p><p><br/></p><p>213123123333333333333333333333333333333333333333333333333333333333333</p><p><br/></p><p>213123123333333333333333333333333333333333333333333333333333333333333</p><p><br/></p><p>213123123333333333333333333333333333333333333333333333333333333333333</p><p><br/></p><p>213123123333333333333333333333333333333333333333333333333333333333333</p><p><br/></p><p>213123123333333333333333333333333333333333333333333333333333333333333</p><p><br/></p><p><br/></p><p>			</p>', null);
INSERT INTO `leipi_news` VALUES ('3', '1', '1521686878', '1审批流测试', '1', '1', '<p>1审批流测试</p>', null);
INSERT INTO `leipi_news` VALUES ('4', '1', '1521700414', '工作测试', '1', '1', '<p>123</p>', null);
INSERT INTO `leipi_news` VALUES ('5', '1', '1522202968', '123', '1', '1', '<p>123123</p>', null);

-- ----------------------------
-- Table structure for `leipi_news_type`
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
-- Table structure for `leipi_run`
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
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0流程中，1通过',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `pid_flow_step` (`pid_flow_step`),
  KEY `cache_run_id` (`cache_run_id`),
  KEY `uid` (`uid`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=751 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run
-- ----------------------------
INSERT INTO `leipi_run` VALUES ('737', '0', 'news', '1', '0', '0', '1', '4', '0', '1', '4', '70', '', '1521638592', '1', '0', '0', '1520940259');
INSERT INTO `leipi_run` VALUES ('740', '0', 'news', '1', '0', '0', '1', '4', '0', '1', '4', '71', '', '0', '1', '0', '0', '1521639850');
INSERT INTO `leipi_run` VALUES ('741', '0', 'news', '1', '0', '0', '1', '4', '0', '1', '4', '72', '', '0', '1', '0', '0', '1521640379');
INSERT INTO `leipi_run` VALUES ('742', '0', 'news', '2', '0', '0', '1', '4', '0', '2', '4', '70', '', '0', '1', '0', '0', '1521642650');
INSERT INTO `leipi_run` VALUES ('743', '0', 'news', '2', '0', '0', '1', '4', '0', '2', '4', '71', '', '1521642735', '1', '0', '0', '1521642708');
INSERT INTO `leipi_run` VALUES ('744', '0', 'news', '2', '0', '0', '1', '4', '0', '2', '4', '72', '', '1521642780', '1', '0', '0', '1521642735');
INSERT INTO `leipi_run` VALUES ('745', '0', 'news', '3', '0', '0', '1', '4', '0', '3', '4', '70', '', '1521686934', '1', '0', '0', '1521686892');
INSERT INTO `leipi_run` VALUES ('747', '0', 'news', '3', '0', '0', '1', '4', '0', '3', '4', '71', '', '1521686959', '1', '0', '0', '1521686934');
INSERT INTO `leipi_run` VALUES ('748', '0', 'news', '3', '0', '0', '1', '4', '0', '3', '4', '72', '', '1521686967', '1', '0', '0', '1521686959');
INSERT INTO `leipi_run` VALUES ('749', '0', 'news', '4', '0', '0', '1', '4', '0', '4', '4', '70', '', '0', '0', '0', '0', '1521701308');
INSERT INTO `leipi_run` VALUES ('750', '0', 'news', '5', '0', '0', '1', '3', '0', '5', '3', '73', '', '0', '0', '0', '0', '1522202980');

-- ----------------------------
-- Table structure for `leipi_run_cache`
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
) ENGINE=MyISAM AUTO_INCREMENT=1922 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run_cache
-- ----------------------------
INSERT INTO `leipi_run_cache` VALUES ('1914', '735', '1', '4', '', '[{\"flow_name\":\"\\u8bf7\\u5047\\u6d41\\u7a0b\",\"id\":4}]', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1520862041,\"dateline\":0}', '0', '0', '1520938558');
INSERT INTO `leipi_run_cache` VALUES ('1915', '736', '1', '1', '', '[{\"id\":1}]', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1520862041,\"dateline\":0}', '0', '0', '1520940104');
INSERT INTO `leipi_run_cache` VALUES ('1916', '737', '1', '1', '', '[{\"id\":1}]', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1520862041,\"dateline\":0}', '0', '0', '1520940259');
INSERT INTO `leipi_run_cache` VALUES ('1917', '742', '2', '2', '', '[{\"id\":2,\"uid\":1,\"add_time\":1520592878,\"new_title\":\"11\",\"new_type\":1,\"new_top\":1,\"new_con\":\"<p>\\t\\t\\t<\\/p><p><br\\/><\\/p><p>213123123333333333333333333333333333333333333333333333333333333333333<\\/p><p><br\\/><\\/p><p>213123123333333333333333333333333333333333333333333333333333333333333<\\/p><p><br\\/><\\/p><p>213123123333333333333333333333333333333333333333333333333333333333333<\\/p><p><br\\/><\\/p><p>213123123333333333333333333333333333333333333333333333333333333333333<\\/p><p><br\\/><\\/p><p>213123123333333333333333333333333333333333333333333333333333333333333<\\/p><p><br\\/><\\/p><p>213123123333333333333333333333333333333333333333333333333333333333333<\\/p><p><br\\/><\\/p><p>213123123333333333333333333333333333333333333333333333333333333333333<\\/p><p><br\\/><\\/p><p><br\\/><\\/p><p>\\t\\t\\t<\\/p>\",\"new_user\":null}]', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1520862041,\"dateline\":0}', '0', '0', '1521642650');
INSERT INTO `leipi_run_cache` VALUES ('1918', '745', '3', '3', '', '[{\"id\":3,\"uid\":1,\"add_time\":1521686878,\"new_title\":\"1\\u5ba1\\u6279\\u6d41\\u6d4b\\u8bd5\",\"new_type\":1,\"new_top\":1,\"new_con\":\"<p>1\\u5ba1\\u6279\\u6d41\\u6d4b\\u8bd5<\\/p>\",\"new_user\":null}]', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1520862041,\"dateline\":0}', '0', '0', '1521686892');
INSERT INTO `leipi_run_cache` VALUES ('1919', '746', '3', '3', '', '[{\"id\":3,\"uid\":1,\"add_time\":1521686878,\"new_title\":\"1\\u5ba1\\u6279\\u6d41\\u6d4b\\u8bd5\",\"new_type\":1,\"new_top\":1,\"new_con\":\"<p>1\\u5ba1\\u6279\\u6d41\\u6d4b\\u8bd5<\\/p>\",\"new_user\":null}]', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1520862041,\"dateline\":0}', '0', '0', '1521686896');
INSERT INTO `leipi_run_cache` VALUES ('1920', '749', '4', '4', '', '[{\"id\":4,\"uid\":1,\"add_time\":1521700414,\"new_title\":\"\\u5de5\\u4f5c\\u6d4b\\u8bd5\",\"new_type\":1,\"new_top\":1,\"new_con\":\"<p>123<\\/p>\",\"new_user\":null}]', '{\"id\":70,\"flow_id\":4,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"71\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":4,\"auto_unlock\":1,\"auto_sponsor_ids\":\"10,11,12\",\"auto_sponsor_text\":\"\\u6d4b\\u8bd51,\\u6d4b\\u8bd52,\\u6d4b\\u8bd53\",\"auto_respon_ids\":\"10,11,12\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"71\\\":{\\\"condition\\\":[],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":436,\"settop\":215,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1520862041,\"dateline\":0}', '0', '0', '1521701308');
INSERT INTO `leipi_run_cache` VALUES ('1921', '750', '5', '5', '', '[{\"id\":5,\"uid\":1,\"add_time\":1522202968,\"new_title\":\"123\",\"new_type\":1,\"new_top\":1,\"new_con\":\"<p>123123<\\/p>\",\"new_user\":null}]', '{\"id\":73,\"flow_id\":3,\"process_name\":\"\\u5f00\\u59cb\",\"process_type\":\"is_one\",\"process_to\":\"74,75\",\"child_id\":0,\"child_relation\":\"\",\"child_after\":1,\"child_back_process\":0,\"return_sponsor_ids\":\"\",\"return_respon_ids\":\"\",\"write_fields\":\"\",\"secret_fields\":\"\",\"lock_fields\":\"\",\"check_fields\":\"\",\"auto_person\":0,\"auto_unlock\":1,\"auto_sponsor_ids\":\"\",\"auto_sponsor_text\":\"\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":\"\",\"range_dept_text\":\"\",\"range_role_ids\":\"\",\"range_role_text\":\"\",\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":1,\"is_back\":1,\"out_condition\":\"{\\\"74\\\":{\\\"condition\\\":[\\\"\'new_type\' = \'1\'\\\"],\\\"condition_desc\\\":\\\"\\\"},\\\"75\\\":{\\\"condition\\\":[\\\"\'new_type\' = \'2\'\\\"],\\\"condition_desc\\\":\\\"\\\"}}\",\"setleft\":331,\"settop\":187,\"style\":\"{\\\"width\\\":120,\\\"height\\\":30,\\\"color\\\":\\\"#0e76a8\\\",\\\"icon\\\":\\\"icon-star\\\"}\",\"is_del\":0,\"updatetime\":1521687318,\"dateline\":0}', '0', '0', '1522202980');

-- ----------------------------
-- Table structure for `leipi_run_log`
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run_log
-- ----------------------------
INSERT INTO `leipi_run_log` VALUES ('1', '1', '1', 'news', '0', '0', '流程发起', '', '0');
INSERT INTO `leipi_run_log` VALUES ('2', '1', '0', '工作流发起', '750', '0', '5', '', '1522202980');

-- ----------------------------
-- Table structure for `leipi_run_process`
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
) ENGINE=InnoDB AUTO_INCREMENT=1499 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of leipi_run_process
-- ----------------------------
INSERT INTO `leipi_run_process` VALUES ('1484', '1', '735', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1520938558', '1520938558', '0', '0', '0', '1520938558');
INSERT INTO `leipi_run_process` VALUES ('1485', '1', '736', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1520940104', '1520940104', '0', '0', '0', '1520940104');
INSERT INTO `leipi_run_process` VALUES ('1486', '1', '737', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1520940259', '1520940259', '0', '0', '0', '1520940259');
INSERT INTO `leipi_run_process` VALUES ('1487', '1', '739', '4', '71', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521639732', '1521639732', '0', '0', '0', '1521639732');
INSERT INTO `leipi_run_process` VALUES ('1488', '1', '740', '4', '71', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521639850', '1521639850', '0', '0', '0', '1521639850');
INSERT INTO `leipi_run_process` VALUES ('1489', '1', '741', '4', '72', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521640379', '1521640379', '0', '0', '0', '1521640379');
INSERT INTO `leipi_run_process` VALUES ('1490', '1', '742', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521642650', '1521642650', '0', '0', '0', '1521642650');
INSERT INTO `leipi_run_process` VALUES ('1491', '1', '743', '4', '71', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521642708', '1521642708', '0', '0', '0', '1521642708');
INSERT INTO `leipi_run_process` VALUES ('1492', '1', '744', '4', '72', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521642735', '1521642735', '0', '0', '0', '1521642735');
INSERT INTO `leipi_run_process` VALUES ('1493', '1', '745', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521686892', '1521686892', '0', '0', '0', '1521686892');
INSERT INTO `leipi_run_process` VALUES ('1494', '1', '746', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521686896', '1521686896', '0', '0', '0', '1521686896');
INSERT INTO `leipi_run_process` VALUES ('1495', '1', '747', '4', '71', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521686934', '1521686934', '0', '0', '0', '1521686934');
INSERT INTO `leipi_run_process` VALUES ('1496', '1', '748', '4', '72', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521686959', '1521686959', '0', '0', '0', '1521686959');
INSERT INTO `leipi_run_process` VALUES ('1497', '1', '749', '4', '70', '0', '0', '0', '', '0', '1', '0', '0', '1', '1521701308', '1521701308', '0', '0', '0', '1521701308');
INSERT INTO `leipi_run_process` VALUES ('1498', '1', '750', '3', '73', '0', '0', '0', '', '0', '1', '0', '0', '1', '1522202980', '1522202980', '0', '0', '0', '1522202980');

-- ----------------------------
-- Table structure for `leipi_run_sign`
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
