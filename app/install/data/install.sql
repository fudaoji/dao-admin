
-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__admin`;
CREATE TABLE `__PREFIX__admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) NOT NULL DEFAULT '1' COMMENT '所属部门',
  `username` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '账号',
  `password` char(60) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(15) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机号',
  `realname` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '姓名',
  `ip` varchar(15) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '登录ip',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for admin_group
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__admin_group`;
CREATE TABLE `__PREFIX__admin_group` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '部门标识',
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` text NOT NULL,
  `sort` tinyint(3) NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '备注',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `title` (`title`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_group
-- ----------------------------
INSERT INTO `__PREFIX__admin_group` VALUES ('1', 'super_admin', '超级管理员', '1', '7,160,149,151,150,152,166,153,155,69,79,156,157,158,159,1,3,105,106,107,108,109,4,6,162,148', '0', '0', '1445158837', '拥有系统最高管理权限', '1626581117');

-- ----------------------------
-- Table structure for ky_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__admin_rule`;
CREATE TABLE `__PREFIX__admin_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '标识',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题名称',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级菜单',
  `icon` varchar(30) NOT NULL DEFAULT '' COMMENT '图标',
  `href` varchar(50) NOT NULL DEFAULT '' COMMENT '路径',
  `target` enum('_self','_blank') NOT NULL DEFAULT '_self' COMMENT '打开方式',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `sort` int(6) unsigned NOT NULL DEFAULT '0' COMMENT '数字越小越靠前',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1菜单 2权限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin_rule
-- ----------------------------
INSERT INTO `__PREFIX__admin_rule` VALUES (1, '', '系统管理', 0, '', '', '_self', 1, 0, 1626845872, 0, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (3, '', ' 管理员管理', 1, 'fa fa-users', '/admin/admin/index', '_self', 1, 0, 1605337009, 1, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (4, '', '菜单管理', 1, 'fa fa-align-justify', '/admin/adminrule/index', '_self', 1, 0, 1640017490, 3, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (107, '', '角色管理', 1, 'fa fa-align-center', '/admin/admingroup/index', '_self', 1, 1604904540, 1605337113, 2, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (148, '', '配置管理', 1, 'fa fa-cogs', '/admin/setting/index', '_self', 1, 1624803694, 1640366685, 4, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (151, '', '团长管理', 162, '', '/admin/manager/index', '_self', 1, 1625725311, 1640018227, 10, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (162, '', '用户管理', 169, 'fa fa-user', '', '_self', 1, 1626507555, 1640844683, 31, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (168, '', '渠道管理', 162, 'fa fa-chain', '/admin/channel/index', '_self', 1, 1640786676, 1649837988, 5, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (169, '', '运营管理', 0, '', '', '_self', 1, 1640844673, 1650466761, 21, 1);
INSERT INTO `__PREFIX__admin_rule` VALUES (176, '', '系统升级', 1, 'fa fa-cloud-upload', '/admin/upgrade/index', '_self', 1, 1657205545, 1657205566, 1, 1);

-- ----------------------------
-- Table structure for setting
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__setting`;
CREATE TABLE `__PREFIX__setting`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标识',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置值',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '站点配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dao_setting
-- ----------------------------
INSERT INTO `__PREFIX__setting` VALUES (1, 'site', '站点信息', '{\"project_title\":\"DaoAdmin\",\"logo\":\"https:\\/\\/devhhb.images.huihuiba.net\\/1-6379c2a74cd60.png\"}', 1590290640, 1668924108);
INSERT INTO `__PREFIX__setting` VALUES (2, 'upload', '附件设置', '{\"driver\":\"local\",\"upload_path\":\"uploads\",\"qiniu_ak\":\"zn9rSy52CirW07siksxMsLBo\",\"qiniu_sk\":\"GW-pw9TmFIhaFNKKEIzYeNYGt_1P\",\"qiniu_bucket\":\"dev-hhb\",\"qiniu_domain\":\"\",\"image_size\":\"3148000\",\"image_ext\":\"jpg,gif,png,jpeg\",\"file_size\":\"53000000\",\"file_ext\":\"jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,mp3,mp4,xls,xlsx,pdf,csv\",\"voice_size\":\"2048000\",\"voice_ext\":\"mp3,wma,wav,amr\",\"video_size\":\"50240000\",\"video_ext\":\"mp4,flv,mov\"}', 1590292316, 1668935267);
INSERT INTO `__PREFIX__setting` VALUES (7, 'sms', '短信设置', '{\"sms_account\":\"111111\",\"sms_pwd\":\"22222\",\"sms_type\":\"3333\"}', 0, 1640491477);