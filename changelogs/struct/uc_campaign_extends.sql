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

 Date: 30/12/2021 10:20:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for uc_campaign_extends
-- ----------------------------
DROP TABLE IF EXISTS `uc_campaign_extends`;
CREATE TABLE `uc_campaign_extends` (
  `campaign_id` bigint(20) NOT NULL COMMENT '推广计划id',
  `convert_callback_strategy_id` int(11) NOT NULL DEFAULT '0' COMMENT '回传策略id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`campaign_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='uc推广计划信息扩展表';

SET FOREIGN_KEY_CHECKS = 1;
