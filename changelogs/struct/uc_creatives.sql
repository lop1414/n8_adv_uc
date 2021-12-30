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

 Date: 30/12/2021 10:21:05
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for uc_creatives
-- ----------------------------
DROP TABLE IF EXISTS `uc_creatives`;
CREATE TABLE `uc_creatives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '创意id',
  `account_id` bigint(11) NOT NULL COMMENT '账户ID',
  `campaign_id` bigint(20) NOT NULL COMMENT '推广计划ID',
  `style` int(11) NOT NULL COMMENT '创意的样式组id',
  `style_type` int(11) NOT NULL COMMENT '物料模板id',
  `show_mode` tinyint(4) NOT NULL COMMENT '展现方式',
  `paused` tinyint(4) NOT NULL COMMENT '启停',
  `state` tinyint(4) NOT NULL COMMENT '物料推广状态',
  `extends` text COMMENT '扩展字段',
  `remark_status` varchar(50) NOT NULL DEFAULT '' COMMENT '备注状态',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=69583275 DEFAULT CHARSET=utf8 COMMENT='uc创意';

SET FOREIGN_KEY_CHECKS = 1;
