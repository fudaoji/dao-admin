-- ----------------------------
-- Table structure for dao_crontab
-- ----------------------------
CREATE TABLE `__PREFIX__crontab`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '为0表示系统',
  `module` varchar(50) NOT NULL DEFAULT 'admin' COMMENT '模块或应用',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '请求链接',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
