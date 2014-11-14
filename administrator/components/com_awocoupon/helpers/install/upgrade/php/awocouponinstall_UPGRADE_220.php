<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

function awocouponinstall_UPGRADE_220() {
	$db			= JFactory::getDBO();
		
	$config = JFactory::getConfig ();
	$p__ = $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'dbprefix' );

	$db->setQuery('SHOW TABLES LIKE "'.$p__.'virtuemart_orders"');
	$tmp = $db->loadResult();
	if(!empty($tmp)) {
		$db->setQuery("UPDATE #__awocoupon_giftcert_order g,#__virtuemart_orders o SET g.user_id=o.virtuemart_user_id WHERE g.estore='virtuemart' AND g.order_id=o.virtuemart_order_id;");
		$db->query();
	}
	$db->setQuery('SHOW TABLES LIKE "'.$p__.'hikashop_order"');
	$tmp = $db->loadResult();
	if(!empty($tmp)) {
		$db->setQuery("UPDATE #__awocoupon_giftcert_order g,#__hikashop_order o,#__hikashop_user uu SET g.user_id=uu.user_cms_id WHERE g.estore='hikashop' AND g.order_id=o.order_id AND uu.user_id=o.order_user_id;");
		$db->query();
	}
	$db->setQuery('SHOW TABLES LIKE "'.$p__.'redshop_orders"');
	$tmp = $db->loadResult();
	if(!empty($tmp)) {
		$db->setQuery("UPDATE #__awocoupon_giftcert_order g,#__redshop_orders o SET g.user_id=o.user_id WHERE g.estore='redshop' AND g.order_id=o.order_id;");
		$db->query();
	}
		
}