--
-- 数据库修改sql
-- @date-from 10.22.2016
--
--
-- 修改pano_config两个字段默认值，满足接口需求
-- @author yuanjiang 10.22
--
alter table u_pano_config change footmark footmark tinyint(1) unsigned not null default 1;
alter table u_pano_config change comment comment tinyint(1) unsigned not null default 1;


/*
Author: 音频素材

Date: 2016-10-26 21:56:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `u_def_voice`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `u_def_voice` (
  `pk_voice` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `absolutelocation` varchar(255) NOT NULL,
  `flag_del` tinyint(1) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `name_uniqid` char(32) NOT NULL,
  PRIMARY KEY (`pk_voice`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;