<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

function awocouponinstall_UPGRADE_200() {
	$db			= JFactory::getDBO();
	
	$db->setQuery('DESC #__awocoupon_vm');
	$columns = $db->loadObjectList('Field');
	$is_function_type2 = isset($columns['function_type2']) ? true : false;
	
	
	if(!$is_function_type2) {
		$db->setQuery('ALTER TABLE `#__awocoupon_vm` ADD COLUMN `function_type2` enum("product","category","manufacturer","vendor","shipping","parent") AFTER `function_type`');
		$db->query();
		$db->setQuery('UPDATE `#__awocoupon_vm` SET `function_type2`="product"');
		$db->query();
	}
	else {
		$db->setQuery('ALTER TABLE `#__awocoupon_vm` MODIFY `function_type2` enum("product","category","manufacturer","vendor","shipping","parent") AFTER `function_type`');
		$db->query();
	}
	

}