/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
ALTER TABLE #__awocoupon_vm MODIFY parent_type ENUM('first','lowest','highest','all','allonly');
ALTER TABLE #__awocoupon_vm_giftcert_product ADD COLUMN `coupon_template_id` INT NOT NULL AFTER `product_id`;
ALTER TABLE #__awocoupon_vm_giftcert_product DROP COLUMN `coupon_value`;
ALTER TABLE #__awocoupon_vm_giftcert_product DROP COLUMN `exclude_giftcert`;
DELETE FROM #__awocoupon_vm_giftcert_product;
ALTER TABLE #__awocoupon_vm_profile DROP COLUMN `min_code_len`;
ALTER TABLE #__awocoupon_vm_profile DROP COLUMN `max_code_len`;
