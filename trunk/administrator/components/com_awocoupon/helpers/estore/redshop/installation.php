<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


class AwocouponRedshopInstallation {
	
	static function include_installation() { return true; }
	
	static function getFiles() {
		return	array(
		
			'cart_core'=> array('func'=>'inject_front_helper_cart','index'=>'coupon','name'=>'COM_AWOCOUPON_FI_MSG_CORE_REQUIRED','file'=>'www/components/com_redshop/helpers/cart.php','desc'=>''),
			'checkout_core' => array('func'=>'inject_front_model_checkout','index'=>'orderplugin','name'=>'COM_AWOCOUPON_FI_MSG_CORE_REQUIRED','file'=>'www/components/com_redshop/models/checkout.php','desc'=>''),
			//'orderhook_update'=>array('func'=>'inject_admin_helper_order','index'=>'orderupdate','name'=>'COM_AWOCOUPON_FI_MSG_SELLABLE_GIFTCERT','file'=>'www/administrator/components/com_redshop/helpers/order.php','desc'=>''),
			'giftcert_hook'=>array('func'=>'inject_front_model_checkout','index'=>'giftcertplugin','name'=>'COM_AWOCOUPON_FI_MSG_SELLABLE_GIFTCERT','file'=>'www/components/com_redshop/models/checkout.php','desc'=>''),
			'autocoupon_hook'=>array('func'=>'inject_front_views_cart_view','index'=>'autocouponhook','name'=>'COM_AWOCOUPON_CP_AUTO_DISCOUNT','file'=>'www/components/com_redshop/views/cart/view.html.php','desc'=>''),
		);
	}	
	
	
	static function inject_front_model_checkout($type) {
		
		switch($type) {
			case 'inject': {
				$vars = array(
					'patterns' => array(
						//'orderplugin'=>'/(\$this\s*\->\s*_redshopMail\s*\->\s*sendOrderMail\s*\(\s*\$row\s*\->\s*order_id\s*,\s*\$sendreddesignmail\s*\)\s*;)/is',
						'orderplugin'=>'/(\$this\s*\->\s*_redshopMail\s*\->\s*sendOrderMail\s*\(\s*\$row\s*\->\s*order_id\s*[^)]*\)\s*;)/is', // changed for version 1.3.x
						'giftcertplugin'=>'/(function\s+sendGiftCard\s*\(\s*\$order_id\s*\)\s*{)/is',
					),
					'replacements' => array(
						'orderplugin'=>'# awocoupon_code START ===============================================================
		JPluginHelper::importPlugin(\'redshop_coupon\');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger(\'onCouponRemove\', array($row->order_id));
		# awocoupon_code END =================================================================
		$1',
						'giftcertplugin'=>'$1
		
		# awocoupon_code START ===============================================================
		JPluginHelper::importPlugin(\'redshop_coupon\');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger(\'onOrderGiftcertSend\', array($order_id));
		if(!empty($returnValues)){ foreach ($returnValues as $returnValue) { if ($returnValue !== null ) return $returnValue; } }
		# awocoupon_code END =================================================================',
		
					),
				);
				break;
			}
			case 'check':
			case 'reject': {

				$vars = array(
					'patterns' => array(
					
						'orderplugin'=>'/\s*#\s*awocoupon_code\s*START\s*===============================================================\s*'.
									'JPluginHelper\s*::\s*importPlugin\s*\(\s*\'redshop_coupon\'\s*\)\s*;\s*'.
									'\$dispatcher\s*\=\s*JDispatcher\s*::\s*getInstance\s*\(\s*\)\s*;\s*'.
									'\$returnValues\s*\=\s*\$dispatcher\->trigger\s*\(\s*\'onCouponRemove\'\s*,\s*array\s*\(\s*\$row\->order_id\s*\)\s*\);\s*'.
									'#\s*awocoupon_code\s*END\s*=================================================================/is',
						'giftcertplugin'=>'/\s*#\s*awocoupon_code\s*START\s*===============================================================\s*'.
									'JPluginHelper\s*::\s*importPlugin\s*\(\s*\'redshop_coupon\'\s*\)\s*;\s*'.
									'\$dispatcher\s*\=\s*JDispatcher\s*::\s*getInstance\s*\(\s*\)\s*;\s*'.
									'\$returnValues\s*\=\s*\$dispatcher\s*\->\s*trigger\s*\(\s*\'onOrderGiftcertSend\'\s*,\s*array\s*\(\s*\$order_id\s*\)\s*\)\s*;\s*'.
									'if\s*\(\s*\!\s*empty\s*\(\s*\$returnValues\s*\)\s*\)\s*{\s*foreach\s*\(\s*\$returnValues\s*as\s*\$returnValue\s*\)\s*{\s*if\s*\(\s*\$returnValue\s*\!==\s*null\s*\)\s*return\s*\$returnValue\s*;\s*}\s*}\s*'.
									'#\s*awocoupon_code\s*END\s*=================================================================/is',

					),
					'replacements' => array(
						'orderplugin'=>'',
						'giftcertplugin'=>'',
					),
				);
				break;
			}
		}
		
		
		$file = JPATH_SITE.'/components/com_redshop/models/checkout.php';
		return (object) array('file'=>$file,'vars'=>$vars);
	}
	static function inject_front_helper_cart($type) {
		
		switch($type) {
			case 'inject': {
				$vars = array(
					'patterns' => array(
						'coupon'=>'/(function\s+coupon\s*\(\s*\$c_data\s*=\s*array\s*\(\s*\)\s*\)\s*{)/is',
					),
					'replacements' => array(
						'coupon'=>'$1
		# awocoupon_code START ===============================================================
		JPluginHelper::importPlugin(\'redshop_coupon\');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger(\'onCouponProcess\', array($c_data));
		if(!empty($returnValues)){
			foreach ($returnValues as $returnValue) {
				if ($returnValue !== null  ) {
					return $returnValue;
				}
			}
		}
		# awocoupon_code END =================================================================',
					),
				);
				break;
			}
			case 'check':
			case 'reject': {

				$vars = array(
					'patterns' => array(
						'coupon'=>'/\s*#\s*awocoupon_code\s*START\s*===============================================================\s*'.
									'JPluginHelper\s*::\s*importPlugin\s*\(\s*\'redshop_coupon\'\s*\)\s*;\s*'.
									'\$dispatcher\s*\=\s*JDispatcher\s*::\s*getInstance\s*\(\s*\)\s*;\s*'.
									'\$returnValues\s*\=\s*\$dispatcher\->trigger\s*\(\s*\'onCouponProcess\'\s*,\s*array\s*\(\s*\$c_data\s*\)\s*\)\s*;\s*'.
									'if\s*\(\s*\!empty\s*\(\s*\$returnValues\s*\)\s*\)\s*{\s*'.
										'foreach\s*\(\s*\$returnValues\s+as\s+\$returnValue\s*\)\s*{\s*'.
											'if\s*\(\s*\$returnValue\s*\!\=\=\s*null\s*\)\s*{\s*'.
												'return\s*\$returnValue\s*;\s*'.
											'}\s*'.
										'}\s*'.
									'}\s*'.
									'#\s*awocoupon_code\s*END\s*=================================================================/is',
					),
					'replacements' => array(
						'coupon'=>'',
					),
				);
				break;
			}
		}
		
		
		$file = JPATH_SITE.'/components/com_redshop/helpers/cart.php';
		return (object) array('file'=>$file,'vars'=>$vars);
	}
	static function inject_admin_helper_order($type) {
		
		switch($type) {
			case 'inject': {
				$vars = array(
					'patterns' => array(
						'orderupdate'=>'/(\$query\s*=\s*\'UPDATE\s*\'\s*\.\s*\$this\s*\->\s*_table_prefix\s*\.\s*\'orders\s*\'\s*\.\s*\'SET\s*order_status\s*=\s*"\'\s*\.\s*\$newstatus\s*\.\s*\'"\s*,\s*mdate\s*=\s*\'\s*\.\s*time\s*\(\s*\)\s*\.\s*\'\s*WHERE\s*order_id\s*IN\s*\(\s*\'\.\s*\$order_id\s*\.\s*\'\s*\)\s*\'\s*;\s*\$this\s*\->\s*_db\s*\->\s*setQuery\s*\(\s*\$query\s*\)\s*;\s*\$this\s*\->\s*_db\s*\->\s*query\s*\(\s*\)\s*;)/is',
					),
					'replacements' => array(
						'orderupdate'=>'$1

		# awocoupon_code START ===============================================================
		JPluginHelper::importPlugin(\'redshop_coupon\');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger(\'onOrderStatusUpdate\', array($order_id));
		# awocoupon_code END =================================================================',

					),
				);
				break;
			}
			case 'check':
			case 'reject': {

				$vars = array(
					'patterns' => array(
						'orderupdate'=>'/\s*#\s*awocoupon_code\s*START\s*===============================================================\s*'.
										'JPluginHelper\s*::\s*importPlugin\s*\(\s*\'redshop_coupon\'\s*\)\s*;\s*'.
										'\$dispatcher\s*=\s*JDispatcher\s*::\s*getInstance\s*\(\s*\)\s*;\s*'.
										'\$returnValues\s*=\s*\$dispatcher\s*\->\s*trigger\s*\(\s*\'onOrderStatusUpdate\'\s*,\s*array\s*\(\s*\$order_id\s*\)\s*\)\s*;\s*'.
										'#\s*awocoupon_code\s*END\s*=================================================================/is',
					),
					'replacements' => array(
						'orderupdate'=>'',
					),
				);
				break;
			}
		}
		
		
		$file = JPATH_SITE.'/administrator/components/com_redshop/helpers/order.php';
		return (object) array('file'=>$file,'vars'=>$vars);
	}
	

	static function inject_front_views_cart_view($type) {
		switch($type) {
			case 'inject': {
				$vars = array(
					'patterns' => array(
						//'autocouponhook'=>'/(function\s+coupon\s*\(\s*\$c_data\s*=\s*array\s*\(\s*\)\s*\)\s*{)/is',
						'autocouponhook'=>'/(\$session\s*\=\s*\&?\s*JFactory\s*::\s*getSession\s*\(\s*\)\s*;\s*\$cart\s*\=\s*\$session\s*\->\s*get\s*\(\s*\'cart\'\s*\)\s*;)/is',
					),
					'replacements' => array(
						'autocouponhook'=>'# awocoupon_code START ===============================================================
		JPluginHelper::importPlugin(\'redshop_coupon\');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger(\'onBeforeCartLoad\', array());
		# awocoupon_code END =================================================================
		$1',
					),
				);
				break;
			}
			case 'check':
			case 'reject': {

				$vars = array(
					'patterns' => array(
						'autocouponhook'=>'/#\s*awocoupon_code\s*START\s*===============================================================\s*'.
									'JPluginHelper\s*::\s*importPlugin\s*\(\s*\'redshop_coupon\'\s*\)\s*;\s*'.
									'\$dispatcher\s*\=\s*JDispatcher\s*::\s*getInstance\s*\(\s*\)\s*;\s*'.
									'\$dispatcher\->trigger\s*\(\s*\'onBeforeCartLoad\'\s*,\s*array\s*\(\s*\)\s*\)\s*;\s*'.
									'#\s*awocoupon_code\s*END\s*=================================================================\s*/is',
					),
					'replacements' => array(
						'autocouponhook'=>'',
					),
				);
				break;
			}
		}
		
		
		$file = JPATH_SITE.'/components/com_redshop/views/cart/view.html.php';
		return (object) array('file'=>$file,'vars'=>$vars);
	}


}
