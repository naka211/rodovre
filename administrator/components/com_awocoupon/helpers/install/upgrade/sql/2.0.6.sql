/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
ALTER TABLE `#__awocoupon_vm_giftcert_product` ADD COLUMN `vendor_name` VARCHAR(255) AFTER expiration_type;
ALTER TABLE `#__awocoupon_vm_giftcert_product` ADD COLUMN `vendor_email` VARCHAR(255) AFTER vendor_name;
