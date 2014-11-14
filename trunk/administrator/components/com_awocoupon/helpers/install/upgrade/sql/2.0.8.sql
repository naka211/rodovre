/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

RENAME TABLE 
	#__awocoupon_vm TO #__awocoupon,
	#__awocoupon_vm_config TO #__awocoupon_config,
	#__awocoupon_vm_license TO #__awocoupon_license,
	#__awocoupon_vm_children TO #__awocoupon_children,
	#__awocoupon_vm_product TO #__awocoupon_product,
	#__awocoupon_vm_category TO #__awocoupon_category,
	#__awocoupon_vm_manufacturer TO #__awocoupon_manufacturer,
	#__awocoupon_vm_vendor TO #__awocoupon_vendor,
	#__awocoupon_vm_shipping TO #__awocoupon_shipping,
	#__awocoupon_vm_user TO #__awocoupon_user,
	#__awocoupon_vm_usergroup TO #__awocoupon_usergroup,
	#__awocoupon_vm_history TO #__awocoupon_history,
	#__awocoupon_vm_profile TO #__awocoupon_profile,
	#__awocoupon_vm_giftcert_product TO #__awocoupon_giftcert_product,
	#__awocoupon_vm_giftcert_code TO #__awocoupon_giftcert_code,
	#__awocoupon_vm_giftcert_order TO #__awocoupon_giftcert_order
	;

/* PHP:awocouponinstall_UPGRADE_208(); */;


ALTER TABLE `#__awocoupon` ADD COLUMN `estore` enum('virtuemart','redshop') NOT NULL AFTER id;
ALTER TABLE `#__awocoupon_history` ADD COLUMN `estore` enum('virtuemart','redshop') NOT NULL AFTER id;
ALTER TABLE `#__awocoupon_giftcert_product` ADD COLUMN `estore` enum('virtuemart','redshop') NOT NULL AFTER id;
ALTER TABLE `#__awocoupon_giftcert_code` ADD COLUMN `estore` enum('virtuemart','redshop') NOT NULL AFTER id;
ALTER TABLE `#__awocoupon_giftcert_order` ADD COLUMN `estore` enum('virtuemart','redshop') NOT NULL AFTER order_id;
