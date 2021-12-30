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

 Date: 30/12/2021 10:18:52
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for clicks
-- ----------------------------
DROP TABLE IF EXISTS `clicks`;
CREATE TABLE `clicks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `click_source` varchar(50) NOT NULL DEFAULT '' COMMENT '来源',
  `adgroup_id` varchar(100) NOT NULL DEFAULT '' COMMENT '推广组id',
  `campaign_id` varchar(100) NOT NULL COMMENT '计划id',
  `creative_id` varchar(100) NOT NULL DEFAULT '' COMMENT '创意id',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
  `muid` varchar(100) NOT NULL DEFAULT '' COMMENT '安卓为IMEI, IOS为IDFA',
  `android_id` varchar(100) NOT NULL DEFAULT '' COMMENT '安卓id',
  `oaid` varchar(100) NOT NULL DEFAULT '' COMMENT 'Android Q及更高版本的设备号',
  `os` varchar(50) NOT NULL DEFAULT '' COMMENT '操作系统平台',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `ua` varchar(1024) NOT NULL DEFAULT '' COMMENT 'user agent',
  `click_at` timestamp NULL DEFAULT NULL COMMENT '点击时间',
  `callback_url` text NOT NULL COMMENT '转化回调地址',
  `callback_param` text COMMENT '转化回调参数',
  `extends` text NOT NULL COMMENT '扩展字段',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `muid` (`muid`) USING BTREE,
  KEY `oaid` (`oaid`) USING BTREE,
  KEY `ip` (`ip`) USING BTREE,
  KEY `click_at` (`click_at`) USING BTREE,
  KEY `channel_id` (`channel_id`) USING BTREE,
  KEY `campaign_id` (`campaign_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='点击表';

SET FOREIGN_KEY_CHECKS = 1;
