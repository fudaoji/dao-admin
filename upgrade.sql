-- ----------------------------
-- version 1.0.2
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
