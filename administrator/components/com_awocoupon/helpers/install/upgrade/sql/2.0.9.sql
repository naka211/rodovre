/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
 CREATE TABLE IF NOT EXISTS #__awocoupon_asset1 (
	`id` int(16) NOT NULL auto_increment,
	`coupon_id` varchar(32) NOT NULL default '',
	`asset_type` enum('coupon','product','category','manufacturer','vendor','shipping') NOT NULL,
	`asset_id` INT NOT NULL,
	`order_by` INT NULL,
	PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS #__awocoupon_asset2 (
	`id` int(16) NOT NULL auto_increment,
	`coupon_id` varchar(32) NOT NULL default '',
	`asset_type` enum('coupon','product','category','manufacturer','vendor','shipping') NOT NULL,
	`asset_id` INT NOT NULL,
	`order_by` INT NULL,
	PRIMARY KEY  (`id`)
);

ALTER TABLE `#__awocoupon` MODIFY `function_type2` enum('product','category','manufacturer','vendor','shipping','parent','buy_x_get_y') AFTER `function_type`;
INSERT INTO #__awocoupon_asset1 (coupon_id,asset_type,asset_id)
		SELECT coupon_id,"product",product_id 
		  FROM #__awocoupon_product p
		  JOIN #__awocoupon c ON c.id=p.coupon_id
		 WHERE function_type2="product";
INSERT INTO #__awocoupon_asset2 (coupon_id,asset_type,asset_id)
		SELECT coupon_id,"product",product_id 
		  FROM #__awocoupon_product p
		  JOIN #__awocoupon c ON c.id=p.coupon_id
		 WHERE function_type2="shipping";
INSERT INTO #__awocoupon_asset1 (coupon_id,asset_type,asset_id) SELECT coupon_id,"category",category_id FROM #__awocoupon_category;
INSERT INTO #__awocoupon_asset1 (coupon_id,asset_type,asset_id) SELECT coupon_id,"manufacturer",manufacturer_id FROM #__awocoupon_manufacturer;
INSERT INTO #__awocoupon_asset1 (coupon_id,asset_type,asset_id) SELECT coupon_id,"vendor",vendor_id FROM #__awocoupon_vendor;
INSERT INTO #__awocoupon_asset1 (coupon_id,asset_type,asset_id) SELECT coupon_id,"shipping",shipping_rate_id FROM #__awocoupon_shipping;
INSERT INTO #__awocoupon_asset1 (coupon_id,asset_type,asset_id,order_by) SELECT parent_coupon_id,"coupon",coupon_id,order_by FROM #__awocoupon_children;




DROP TABLE IF EXISTS #__awocoupon_children;
DROP TABLE IF EXISTS #__awocoupon_product;
DROP TABLE IF EXISTS #__awocoupon_category;
DROP TABLE IF EXISTS #__awocoupon_manufacturer;
DROP TABLE IF EXISTS #__awocoupon_vendor;
DROP TABLE IF EXISTS #__awocoupon_shipping;

