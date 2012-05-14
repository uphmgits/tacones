
ALTER TABLE `engine4_marketplace_marketplaces`
	ADD `weight` float NULL DEFAULT '0'  AFTER `business_email`,
	ADD `length` float NULL DEFAULT '0' AFTER `weight`,
	ADD `width` float NULL DEFAULT '0' AFTER `length`,
	ADD `height` float NULL DEFAULT '0' AFTER `width`,
COMMENT=''
REMOVE PARTITIONING;
