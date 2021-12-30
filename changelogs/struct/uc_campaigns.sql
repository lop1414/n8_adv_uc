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

 Date: 30/12/2021 10:20:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for uc_campaigns
-- ----------------------------
DROP TABLE IF EXISTS `uc_campaigns`;
CREATE TABLE `uc_campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '推广计划ID',
  `account_id` bigint(20) NOT NULL COMMENT '账户ID',
  `adgroup_id` bigint(20) NOT NULL COMMENT '推广组ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `type` tinyint(4) NOT NULL COMMENT '标的物类型',
  `paused` tinyint(4) NOT NULL COMMENT '启停',
  `opt_target` tinyint(4) NOT NULL COMMENT '优化目标',
  `delivery` tinyint(4) NOT NULL COMMENT '投放方式',
  `delivery_mode` tinyint(4) NOT NULL COMMENT '投放模式',
  `budget` int(11) NOT NULL COMMENT '预算',
  `charge_type` tinyint(4) NOT NULL COMMENT '计费方式',
  `enable_anxt` tinyint(4) NOT NULL COMMENT '启用安心投',
  `anxt_status` tinyint(4) NOT NULL COMMENT '安心投状态',
  `show_mode` tinyint(4) NOT NULL COMMENT '展现方式',
  `extends` text COMMENT '扩展字段',
  `remark_status` varchar(50) NOT NULL DEFAULT '' COMMENT '备注状态',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE,
  KEY `adgroup_id` (`adgroup_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1308026725 DEFAULT CHARSET=utf8 COMMENT='uc推广计划信息';

SET FOREIGN_KEY_CHECKS = 1;
