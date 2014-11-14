/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
ALTER TABLE #__awocoupon_history ADD COLUMN `details` TEXT;

/* PHP:awocouponinstall_UPGRADE_222(); */;
