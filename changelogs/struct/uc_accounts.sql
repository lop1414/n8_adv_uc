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

 Date: 30/12/2021 10:20:36
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for uc_accounts
-- ----------------------------
DROP TABLE IF EXISTS `uc_accounts`;
CREATE TABLE `uc_accounts` (
  `account_id` bigint(11) NOT NULL COMMENT '账户id',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `token` varchar(50) NOT NULL COMMENT 'token',
  `rebate` int(10) DEFAULT NULL COMMENT '反点',
  `password` varchar(255) NOT NULL COMMENT '账户密码',
  `parent_id` varchar(50) DEFAULT NULL COMMENT '父级id',
  `status` varchar(50) NOT NULL DEFAULT '' COMMENT '状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `extends` text COMMENT '扩展字段',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`account_id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账户信息';

SET FOREIGN_KEY_CHECKS = 1;
