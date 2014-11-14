/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 

/* PHP:awocouponinstall_UPGRADE_215(); */;

ALTER TABLE #__awocoupon MODIFY function_type enum('coupon','shipping','buy_x_get_y','parent','giftcert') NOT NULL DEFAULT 'coupon';
UPDATE #__awocoupon SET function_type='shipping' WHERE function_type2='shipping';
UPDATE #__awocoupon SET function_type='buy_x_get_y' WHERE function_type2='buy_x_get_y';
UPDATE #__awocoupon SET function_type='parent' WHERE function_type2='parent';
