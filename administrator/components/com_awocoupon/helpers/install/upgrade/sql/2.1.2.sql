/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
CREATE TABLE IF NOT EXISTS #__awocoupon_lang_text (
	`id` int(16) NOT NULL auto_increment,
	`elem_id` int(16) NOT NULL,
	`lang` varchar(32) NOT NULL default '',
	`text` TEXT,
	PRIMARY KEY  (`id`)
);

ALTER TABLE #__awocoupon_profile ADD COLUMN `email_subject_lang_id` INT AFTER bcc_admin;
ALTER TABLE #__awocoupon_profile ADD COLUMN `email_body_lang_id` INT AFTER email_subject_lang_id;


/* PHP:awocouponinstall_UPGRADE_212(); */;
