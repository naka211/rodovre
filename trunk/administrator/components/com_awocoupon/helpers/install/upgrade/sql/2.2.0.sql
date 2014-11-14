/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
CREATE TABLE IF NOT EXISTS #__awocoupon_image (
	`coupon_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`filename` varchar(255),
	PRIMARY KEY  (`coupon_id`)
);

ALTER TABLE #__awocoupon_config ADD COLUMN `is_json` TINYINT(1) AFTER `name`;
ALTER TABLE #__awocoupon_giftcert_order ADD COLUMN user_id INT NOT NULL AFTER estore;

/* PHP:awocouponinstall_UPGRADE_220(); */;
