/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
CREATE TABLE IF NOT EXISTS #__awocoupon_auto (
	`id` int(16) NOT NULL auto_increment,
	`coupon_id` varchar(32) NOT NULL default '',
	`ordering` INT NULL,
	`published` TINYINT NOT NULL DEFAULT 1,
	PRIMARY KEY  (`id`)
);
