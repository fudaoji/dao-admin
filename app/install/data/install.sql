-- ----------------------------
-- Table structure for dao_admin
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__admin`;
CREATE TABLE `__PREFIX__admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int(10) NOT NULL DEFAULT 1 COMMENT '所属部门',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '账号',
  `password` char(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `realname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录ip',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `last_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '新增时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '最后修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dao_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__admin_group`;
CREATE TABLE `__PREFIX__admin_group`  (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '部门标识',
  `title` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sort` tinyint(3) NOT NULL DEFAULT 0,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dao_admin_group
-- ----------------------------
INSERT INTO `__PREFIX__admin_group` VALUES (1, 'super_admin', '超管', 1, '', 0, 0, 1445158837, '超管', 1672716767);

-- ----------------------------
-- Table structure for dao_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__admin_rule`;
CREATE TABLE `__PREFIX__admin_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标识',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题名称',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级菜单',
  `icon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `href` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '路径',
  `target` enum('_self','_blank') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '_self' COMMENT '打开方式',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  `sort` int(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数字越小越靠前',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1菜单 2权限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dao_admin_rule
-- ----------------------------
BEGIN;
INSERT INTO `__PREFIX__admin_rule` VALUES ('1', '', '系统管理', '0', '', '', '_self', '1', '0', '1626845872', '0', '1'), ('3', '', ' 管理员管理', '1', 'fa fa-users', '/admin/admin/index', '_self', '1', '0', '1671515233', '10', '1'), ('4', '', '菜单管理', '1', 'fa fa-align-justify', '', '_self', '1', '0', '1670806437', '3', '1'), ('107', '', '角色管理', '1', 'fa fa-align-center', '/admin/admingroup/index', '_self', '1', '1604904540', '1671515255', '9', '1'), ('148', '', '配置管理', '1', 'fa fa-cogs', '/admin/setting/index', '_self', '1', '1624803694', '1640366685', '4', '1'), ('151', '', '客户列表', '162', '', '/admin/tenant/index', '_self', '1', '1625725311', '1670806765', '10', '1'), ('162', '', '客户管理', '169', 'fa fa-user', '', '_self', '1', '1626507555', '1670806741', '31', '1'), ('168', '', '官方市场', '169', 'fa fa-window-restore', '', '_self', '1', '1640786676', '1673925185', '5', '1'), ('169', '', '运营管理', '0', '', '', '_self', '1', '1640844673', '1650466761', '21', '1'), ('176', '', '系统升级', '1', 'fa fa-cloud-upload', '/admin/upgrade/index', '_self', '1', '1657205545', '1671515219', '0', '1'), ('177', '', '数据库', '1', 'fa fa-database', '', '_self', '1', '1670220905', '1670220905', '1', '1'), ('178', '', '数据表', '177', '', '/admin/database/tables', '_self', '1', '1670220940', '1670220940', '1', '1'), ('179', '', '运营后台', '4', '', '/admin/adminrule/index', '_self', '1', '1670806427', '1670806427', '5', '1'), ('180', '', '商家后台', '4', '', '/admin/tenantrule/index', '_self', '1', '1670806468', '1670806598', '1', '1'), ('181', '', '应用管理', '169', 'fa fa-cubes', '', '_self', '1', '1670928509', '1670928509', '30', '1'), ('182', '', '应用分类', '181', '', '/admin/appcate/index', '_self', '1', '1670928538', '1670928538', '1', '1'), ('183', '', '应用列表', '181', '', '/admin/app/index', '_self', '1', '1671095201', '1671148827', '20', '1'), ('184', '', '客户应用', '162', '', '/admin/tenantapp/index', '_self', '1', '1671162684', '1671162684', '1', '1'), ('185', '', '应用商店', '168', '', '/admin/appstore/index', '_self', '1', '1673925222', '1673925222', '10', '1'), ('186', '', '应用升级', '168', '', '/admin/appstore/upgrade', '_self', '1', '1673925265', '1673925265', '5', '1'), ('187', '', '客户钱包', '162', '', '/admin/tenantwallet/index', '_self', '1', '1678842427', '1678842427', '1', '1'), ('188', '', '财务管理', '0', '', '', '_self', '1', '1679036468', '1679036468', '10', '1'), ('189', '', '订单管理', '188', 'fa fa-money', '', '_self', '1', '1679036491', '1679036491', '10', '1'), ('190', '', '应用订单', '189', '', '/admin/orderapp/index', '_self', '1', '1679036524', '1679036524', '10', '1');
COMMIT;

-- ----------------------------
-- Table structure for dao_app
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__app`;
CREATE TABLE `__PREFIX__app`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '应用名称',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标识名',
  `desc` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '简介',
  `version` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '版本号',
  `author` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '作者姓名',
  `logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'LOGO',
  `admin_url` varchar(160) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '后台入口',
  `admin_url_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '管理后台：1使用系统 2自建后台',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'mp' COMMENT '支持平台',
  `cates` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类标签',
  `tenant_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '客户管理入口',
  `tenant_url_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '管理后台：1使用系统后台   2自建后台',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `cates`(`cates`(191)) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用表' ROW_FORMAT = Compact;


-- ----------------------------
-- Table structure for dao_app_cate
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__app_cate`;
CREATE TABLE `__PREFIX__app_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序，数字越大越靠前',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用分类表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of dao_app_cate
-- ----------------------------
INSERT INTO `__PREFIX__app_cate` VALUES (1, '涨粉', 2, 1594997635, 1604326895, 1);
INSERT INTO `__PREFIX__app_cate` VALUES (2, '营销', 3, 1594997967, 1604326895, 1);
INSERT INTO `__PREFIX__app_cate` VALUES (3, '商城', 40, 1594997977, 1670928804, 1);
INSERT INTO `__PREFIX__app_cate` VALUES (4, '游戏', 10, 1594997996, 1670928830, 1);
INSERT INTO `__PREFIX__app_cate` VALUES (5, '节日', 5, 1594998006, 1604326896, 1);
INSERT INTO `__PREFIX__app_cate` VALUES (6, '共享', 0, 1597459141, 1604326894, 1);
INSERT INTO `__PREFIX__app_cate` VALUES (7, '工具', 50, 1604326937, 1670928791, 1);

-- ----------------------------
-- Table structure for dao_app_info
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__app_info`;
CREATE TABLE `__PREFIX__app_info`  (
  `id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应用ID（即app中的id）',
  `detail` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '应用介绍',
  `sale_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际销量',
  `sale_num_show` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '虚拟销量',
  `price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '售价',
  `old_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '原价',
  `snapshot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '应用快照',
  `config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '应用配置',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用详细信息' ROW_FORMAT = Compact;


-- ----------------------------
-- Table structure for dao_media_file
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__media_file`;
CREATE TABLE `__PREFIX__media_file`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `company_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家id',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文本内容',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片url',
  `create_time` int(10) NOT NULL DEFAULT 0,
  `update_time` int(10) NOT NULL DEFAULT 0,
  `size` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小（用于判断上传的图片是否小于微信素材库的限制2M）',
  `ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '格式',
  `location` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'qiniu',
  PRIMARY KEY (`id`, `company_id`) USING BTREE,
  INDEX `company_id`(`company_id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件素材库' ROW_FORMAT = Compact PARTITION BY HASH (company_id)
PARTITIONS 10
(PARTITION `p0` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p1` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p2` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p3` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p4` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p5` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p6` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p7` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p8` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p9` MAX_ROWS = 0 MIN_ROWS = 0 )
;


-- ----------------------------
-- Table structure for dao_media_image
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__media_image`;
CREATE TABLE `__PREFIX__media_image`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `company_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家id',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文本内容',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片url',
  `create_time` int(10) NOT NULL DEFAULT 0,
  `update_time` int(10) NOT NULL DEFAULT 0,
  `size` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小（用于判断上传的图片是否小于微信素材库的限制2M）',
  `ext` enum('bmp','jpg','jpeg','png','gif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'jpg' COMMENT '图片格式',
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '位置Local，Qiniu',
  PRIMARY KEY (`id`, `company_id`) USING BTREE,
  INDEX `company_id`(`company_id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '图片素材库' ROW_FORMAT = Compact PARTITION BY HASH (company_id)
PARTITIONS 10
(PARTITION `p0` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p1` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p2` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p3` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p4` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p5` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p6` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p7` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p8` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p9` MAX_ROWS = 0 MIN_ROWS = 0 )
;


-- ----------------------------
-- Table structure for dao_media_link
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__media_link`;
CREATE TABLE `__PREFIX__media_link`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `company_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商户id',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '跳转url',
  `image_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `create_time` int(10) NOT NULL DEFAULT 0,
  `update_time` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `company_id`) USING BTREE,
  INDEX `admin_id`(`company_id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分享链接素材库' ROW_FORMAT = Compact PARTITION BY HASH (company_id)
PARTITIONS 10
(PARTITION `p0` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p1` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p2` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p3` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p4` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p5` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p6` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p7` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p8` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p9` ENGINE = InnoDB MAX_ROWS = 0 MIN_ROWS = 0 )
;


-- ----------------------------
-- Table structure for dao_media_text
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__media_text`;
CREATE TABLE `__PREFIX__media_text`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文本内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `company_id`) USING BTREE,
  INDEX `uid`(`company_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文本素材' ROW_FORMAT = Compact PARTITION BY HASH (company_id)
PARTITIONS 10
(PARTITION `p0` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p1` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p2` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p3` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p4` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p5` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p6` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p7` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p8` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p9` MAX_ROWS = 0 MIN_ROWS = 0 )
;

-- ----------------------------
-- Table structure for dao_media_video
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__media_video`;
CREATE TABLE `__PREFIX__media_video`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `company_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家id',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件名称',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'url',
  `create_time` int(10) NOT NULL DEFAULT 0,
  `update_time` int(10) NOT NULL DEFAULT 0,
  `size` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小',
  `ext` enum('bmp','jpg','jpeg','png','gif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'jpg' COMMENT '后缀',
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '位置Local，Qiniu',
  PRIMARY KEY (`id`, `company_id`) USING BTREE,
  INDEX `company_id`(`company_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '视频素材库' ROW_FORMAT = Compact PARTITION BY HASH (company_id)
PARTITIONS 10
(PARTITION `p0` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p1` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p2` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p3` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p4` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p5` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p6` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p7` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p8` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p9` MAX_ROWS = 0 MIN_ROWS = 0 )
;

-- ----------------------------
-- Table structure for dao_media_voice
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__media_voice`;
CREATE TABLE `__PREFIX__media_voice`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `company_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家id',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件名称',
  `url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'url',
  `create_time` int(10) NOT NULL DEFAULT 0,
  `update_time` int(10) NOT NULL DEFAULT 0,
  `size` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小',
  `ext` enum('bmp','jpg','jpeg','png','gif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'jpg' COMMENT '后缀',
  `location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '位置Local，Qiniu',
  PRIMARY KEY (`id`, `company_id`) USING BTREE,
  INDEX `company_id`(`company_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '‌音频素材库' ROW_FORMAT = Compact PARTITION BY HASH (company_id)
PARTITIONS 10
(PARTITION `p0` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p1` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p2` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p3` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p4` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p5` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p6` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p7` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p8` MAX_ROWS = 0 MIN_ROWS = 0 ,
PARTITION `p9` MAX_ROWS = 0 MIN_ROWS = 0 )
;

-- ----------------------------
-- Table structure for dao_setting
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__setting`;
CREATE TABLE `__PREFIX__setting`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标识',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置值',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '站点配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dao_setting
-- ----------------------------
INSERT INTO `__PREFIX__setting` VALUES (1, 'site', '站点信息', '{\"project_title\":\"DaoAdmin\",\"logo\":\"https:\\/\\/devhhb.images.huihuiba.net\\/1-6379c2a74cd60.png\",\"slogan\":\"您成功路上的好伙伴\",\"beian\":\"闽ICP备19014014号-4\"}', 1590290640, 1671013534);
INSERT INTO `__PREFIX__setting` VALUES (2, 'upload', '附件设置', '{\"driver\":\"local\",\"upload_path\":\"uploads\",\"qiniu_ak\":\"zn9rSy52CirWb9vljGwT\",\"qiniu_sk\":\"GW-pleiv9TmFIhaFNKKEIzYeNYGt_1P\",\"qiniu_bucket\":\"dev\",\"qiniu_domain\":\"https:\\/\\/devhhb.images.net\",\"image_size\":\"3148000\",\"image_ext\":\"jpg,gif,png,jpeg\",\"file_size\":\"53000000\",\"file_ext\":\"jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,mp3,mp4,xls,xlsx,pdf,csv\",\"voice_size\":\"2048000\",\"voice_ext\":\"mp3,wma,wav,amr\",\"video_size\":\"50240000\",\"video_ext\":\"mp4,flv,mov\"}', 1590292316, 1671515096);
INSERT INTO `__PREFIX__setting` VALUES (7, 'sms', '短信设置', '{\"sms_account\":\"111111\",\"sms_pwd\":\"22222\",\"sms_type\":\"3333\"}', 0, 1640491477);

-- ----------------------------
-- Table structure for dao_tenant
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__tenant`;
CREATE TABLE `__PREFIX__tenant`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT '所属角色',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '账号',
  `password` char(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `realname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '登录ip',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `last_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '新增时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '最后修改时间',
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属公司ID',
  `department_id` int(10) NOT NULL COMMENT '部门id',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dao_tenant
-- ----------------------------
INSERT INTO `__PREFIX__tenant` VALUES (1, 2, 'daoadmin', '$2y$10$CHfgi.EiONWl2rrDd69rIuhKLCTBsHJoJyIbL03GgiXs7Dq152NNS', '', '', 'daoadmin', '175.24.234.211', 1, 1673274605, 1671062319, 1673274605, 0, 0);

-- ----------------------------
-- Table structure for dao_tenant_app
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__tenant_app`;
CREATE TABLE `__PREFIX__tenant_app`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商户id',
  `app_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '应用标识',
  `deadline` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '到期时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '客户-应用关联表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dao_tenant_department
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__tenant_department`;
CREATE TABLE `__PREFIX__tenant_department`  (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` tinyint(3) NOT NULL DEFAULT 0,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `company_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属公司ID',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '租户角色' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dao_tenant_group
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__tenant_group`;
CREATE TABLE `__PREFIX__tenant_group`  (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色标识',
  `title` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sort` tinyint(3) NOT NULL DEFAULT 0,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `company_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属公司ID',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '租户角色' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for dao_tenant_rule
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__tenant_rule`;
CREATE TABLE `__PREFIX__tenant_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标识',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题名称',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级菜单',
  `icon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `href` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '路径',
  `target` enum('_self','_blank') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '_self' COMMENT '打开方式',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后更新时间',
  `sort` int(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数字越小越靠前',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1菜单 2权限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dao_tenant_rule
-- ----------------------------
BEGIN;
INSERT INTO `__PREFIX__tenant_rule` VALUES ('1', '', '系统管理', '0', '', '', '_self', '1', '0', '1626845872', '0', '1'), ('3', '', '员工管理', '4', '', '/tenant/tenant/index', '_self', '1', '0', '1671015946', '10', '1'), ('4', '', '组织架构', '1', 'fa fa-object-ungroup', '', '_self', '1', '0', '1671015931', '3', '1'), ('107', '', '角色管理', '4', '', '/tenant/tenantgroup/index', '_self', '1', '1604904540', '1671015979', '2', '1'), ('148', '', '配置管理', '1', 'fa fa-cogs', '/tenant/setting/index', '_self', '1', '1624803694', '1671339575', '4', '1'), ('151', '', '应用管理', '176', 'fa fa-cubes', '', '_self', '1', '1625725311', '1671063145', '10', '1'), ('162', '', '素材管理', '169', 'fa fa-archive', '/tenant/mediaimage/index', '_self', '1', '1626507555', '1672303131', '10', '1'), ('168', '', '部门管理', '4', '', '/tenant/department/index', '_self', '1', '1640786676', '1671016069', '5', '1'), ('169', '', '运营管理', '0', '', '', '_self', '1', '1640844673', '1650466761', '21', '1'), ('176', '', '应用中心', '0', 'fa fa-cloud-upload', '', '_self', '1', '1657205545', '1671063065', '10', '1'), ('177', '', '我的应用', '151', '', '/tenant/apps/index', '_self', '1', '1671063241', '1671063241', '10', '1'), ('178', '', '应用市场', '176', 'fa fa-cart-plus', '', '_self', '1', '1671527760', '1671527760', '5', '1'), ('179', '', '应用采购', '178', '', '/tenant/apps/store', '_self', '1', '1671527878', '1671527878', '10', '1'), ('180', '', '过期应用', '151', '', '/tenant/apps/overtime', '_self', '1', '1672295990', '1672295990', '1', '1'), ('181', '', '财务管理', '0', '', '', '_self', '1', '1678864689', '1678864704', '5', '1'), ('182', '', '账号资产', '181', 'fa fa-money', '', '_self', '1', '1678864755', '1678864755', '1', '1'), ('183', '', '钱包', '182', '', '/tenant/wallet/log', '_self', '1', '1678864816', '1678865800', '1', '1'), ('184', '', '订单管理', '181', 'fa fa-first-order', '', '_self', '1', '1678864853', '1678864853', '1', '1'), ('185', '', '应用订单', '184', '', '/tenant/orderapp/index', '_self', '1', '1678864882', '1678864882', '1', '1');
COMMIT;

-- ----------------------------
-- Table structure for dao_tenant_setting
-- ----------------------------
DROP TABLE IF EXISTS `__PREFIX__tenant_setting`;
CREATE TABLE `__PREFIX__tenant_setting`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `company_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '公司id',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标识',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`, `company_id`) USING BTREE COMMENT '同个租户标识不能重复'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '租户配置表' ROW_FORMAT = Dynamic;

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
);

-- ----------------------------
-- version 1.1.0
-- ----------------------------
CREATE TABLE `__PREFIX__timer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '为0表示系统',
  `module` varchar(50) NOT NULL DEFAULT 'admin' COMMENT '模块或应用',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '处理器的命名空间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `seconds` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '间隔秒数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `__PREFIX__tenant_wallet` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id',
  `money` decimal(10,2) unsigned NOT NULL COMMENT '可用余额',
  `total` decimal(10,2) unsigned NOT NULL COMMENT '总金额',
  `frozen` decimal(10,2) unsigned NOT NULL COMMENT '冻结金额',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='商户钱包';
CREATE TABLE `__PREFIX__tenant_wallet_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0支出  1收入',
  `money` decimal(10,2) unsigned NOT NULL COMMENT '金额',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发生时间',
  `module` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'addon' COMMENT '所属模块',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`company_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='钱包明细';
CREATE TABLE `__PREFIX__order_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '交易单号',
  `app_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '应用标识',
  `tenant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '员工id',
  `company_id` int(10) NOT NULL COMMENT '商户id',
  `body` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '购买详情',
  `total` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单总额（单位：分）',
  `amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单实付金额',
  `wallet` decimal(10,2) unsigned NOT NULL COMMENT '钱包支付',
  `channel` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'WX_NATIVE' COMMENT '支付类型,默认微信支付',
  `client_ip` char(15) CHARACTER SET utf8 NOT NULL DEFAULT '127.0.0.1' COMMENT '客户端ip',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1:支付成功 0：待支付  2：已退款',
  `transaction_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '支付平台订单号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `month` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '时长',
  `refund_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '退款号',
  `refund_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `user_id` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='应用采购订单';