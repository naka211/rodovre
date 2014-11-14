/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
ALTER TABLE #__awocoupon ADD COLUMN passcode VARCHAR(10) AFTER coupon_code;
ALTER TABLE #__awocoupon_history ADD COLUMN user_email VARCHAR(255) AFTER user_id;
UPDATE #__awocoupon SET passcode=SUBSTRING(MD5(CONCAT(UNIX_TIMESTAMP(),FLOOR(1+RAND()*1000),coupon_code)),1,6);
