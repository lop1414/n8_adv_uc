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

 Date: 30/12/2021 10:20:14
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for channel_campaign_logs
-- ----------------------------
DROP TABLE IF EXISTS `channel_campaign_logs`;
CREATE TABLE `channel_campaign_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_campaign_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '渠道计划关联id',
  `campaign_id` bigint(20) NOT NULL COMMENT '推广计划id',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
  `platform` varchar(50) NOT NULL DEFAULT '' COMMENT '平台',
  `extends` text COMMENT '扩展信息',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `channel_id` (`channel_id`) USING BTREE,
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='渠道-推广计划关联日志表';

SET FOREIGN_KEY_CHECKS = 1;
