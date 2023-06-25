-- ----------------------------
-- version 1.0.5 若手动更新请务必替换__PREFIX__为你的真实表前缀
-- ----------------------------
CREATE TABLE `__PREFIX__media_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `company_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='素材分组';

ALTER TABLE `__PREFIX__media_file` ADD COLUMN `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分组id' ;
ALTER TABLE `__PREFIX__media_image` ADD COLUMN `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分组id' ;
ALTER TABLE `__PREFIX__media_text` ADD COLUMN `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分组id' ;
ALTER TABLE `__PREFIX__media_link` ADD COLUMN `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分组id' ;
ALTER TABLE `__PREFIX__media_video` ADD COLUMN `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分组id';
ALTER TABLE `__PREFIX__media_voice` ADD COLUMN `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分组id' ;