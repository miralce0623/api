/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : mydb

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2018-01-17 18:49:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `content` text,
  `create_at` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`article_id`,`user_id`),
  KEY `fk_article_user_idx` (`user_id`),
  CONSTRAINT `fk_article_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article
-- ----------------------------
INSERT INTO `article` VALUES ('5', '文章标题', '文章内容', '1516160173', '3');
INSERT INTO `article` VALUES ('6', '文章标题1', '文章内容1', '1516160219', '3');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` char(32) NOT NULL,
  `create_at` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_id` (`user_id`),
  KEY `create_at` (`create_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'demodemo', 'demodemo', '0');
INSERT INTO `user` VALUES ('2', 'admin', 'f6e96294f614d99d4c79bcc109f6598c', '0');
INSERT INTO `user` VALUES ('3', 'admin1', 'd7ef352189c5e41f01951cd597a6196b', '1516154685');
