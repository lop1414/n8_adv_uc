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

 Date: 30/12/2021 10:20:43
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for uc_adgroups
-- ----------------------------
DROP TABLE IF EXISTS `uc_adgroups`;
CREATE TABLE `uc_adgroups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '推广组id',
  `account_id` bigint(20) NOT NULL COMMENT '账户id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `objective_type` int(4) NOT NULL COMMENT '标的物类型',
  `paused` tinyint(4) NOT NULL,
  `budget` int(11) NOT NULL COMMENT '计划预算',
  `remark_status` varchar(50) NOT NULL DEFAULT '' COMMENT '备注状态',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `account_id` (`account_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=117269000 DEFAULT CHARSET=utf8 COMMENT='uc推广组信息';

SET FOREIGN_KEY_CHECKS = 1;
