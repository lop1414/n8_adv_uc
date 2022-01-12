/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机9.7.2
 Source Server Type    : MySQL
 Source Server Version : 50732
 Source Host           : localhost:3306
 Source Schema         : n8_adv_uc

 Target Server Type    : MySQL
 Target Server Version : 50732
 File Encoding         : 65001

 Date: 11/01/2022 10:43:22
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for uc_creative_reports
-- ----------------------------
DROP TABLE IF EXISTS `uc_creative_reports`;
CREATE TABLE `uc_creative_reports` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(10) NOT NULL COMMENT '账户id',
  `adgroup_id` bigint(20) NOT NULL COMMENT '推广组id',
  `campaign_id` bigint(20) NOT NULL COMMENT '推广计划id',
  `creative_id` bigint(20) NOT NULL COMMENT '创意id',
  `stat_datetime` timestamp NULL DEFAULT NULL COMMENT '数据起始时间',
  `consume` int(11) NOT NULL DEFAULT '0' COMMENT '消耗',
  `srch` int(11) NOT NULL DEFAULT '0' COMMENT '展现',
  `click` int(11) NOT NULL DEFAULT '0' COMMENT '点击数',
  `binding_conversion` int(11) NOT NULL DEFAULT '0' COMMENT '转化数（回传时间）',
  `extends` text COMMENT '扩展字段',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stat_datetime_creative_id` (`stat_datetime`,`creative_id`) USING BTREE,
  KEY `creative_id` (`creative_id`) USING BTREE,
  KEY `adgroup_id` (`adgroup_id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='UC创意数据报表';

SET FOREIGN_KEY_CHECKS = 1;
