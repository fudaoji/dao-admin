-- ----------------------------
-- version 1.0.4
-- ----------------------------
ALTER TABLE `__PREFIX__tenant_group` MODIFY COLUMN `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `status`;
ALTER TABLE `__PREFIX__tenant` MODIFY COLUMN `department_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门id' AFTER `company_id`;