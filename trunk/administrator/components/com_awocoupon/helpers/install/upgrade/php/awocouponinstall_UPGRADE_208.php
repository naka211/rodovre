<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

function awocouponinstall_UPGRADE_208() {
	$db			= JFactory::getDBO();
	
	$db->setQuery('UPDATE #__awocoupon_config SET name="virtuemart_giftcert_field_recipient_name" WHERE name="giftcert_field_recipient_name"');
	$db->query();
	
	$db->setQuery('UPDATE #__awocoupon_config SET name="virtuemart_giftcert_field_recipient_email" WHERE name="giftcert_field_recipient_email"');
	$db->query();
	
	$db->setQuery('UPDATE #__awocoupon_config SET name="virtuemart_giftcert_field_recipient_message" WHERE name="giftcert_field_recipient_message"');
	$db->query();
	
}