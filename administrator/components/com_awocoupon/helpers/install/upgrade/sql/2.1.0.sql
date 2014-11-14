/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
ALTER TABLE #__awocoupon MODIFY `estore` enum("virtuemart","redshop","hikashop") NOT NULL;
ALTER TABLE #__awocoupon_history MODIFY `estore` enum("virtuemart","redshop","hikashop") NOT NULL;
ALTER TABLE #__awocoupon_giftcert_product MODIFY `estore` enum("virtuemart","redshop","hikashop") NOT NULL;
ALTER TABLE #__awocoupon_giftcert_code MODIFY `estore` enum("virtuemart","redshop","hikashop") NOT NULL;
ALTER TABLE #__awocoupon_giftcert_order MODIFY `estore` enum("virtuemart","redshop","hikashop") NOT NULL;
		
