<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


class AwocouponHikashopInstallation {

	static function include_installation() {
		static $hikashop_version		= '';

		if( empty( $hikashop_version ) ) {
			if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop.xml')) return false;
			
			$parser = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop.xml');
			$hikashop_version = (string)$parser->version;
		}
		
		return (version_compare($hikashop_version,'1.5.8','<=')) ? true : false;
	}
	
	static function getFiles() {
		return	array(
		
			'discount_core'=>	array('func'=>'inject_admin_class_discount',	'index'=>'onBeforeCouponLoad',		'name'=>'COM_AWOCOUPON_FI_MSG_CORE_REQUIRED','file'=>'www/administrator/components/com_hikashop/classes/discount.php','desc'=>''),
			'cart_core'=> 		array('func'=>'inject_admin_class_cart',		'index'=>'onAfterCartShippingLoad',	'name'=>'COM_AWOCOUPON_FI_MSG_CORE_REQUIRED','file'=>'www/administrator/components/com_hikashop/classes/cart.php','desc'=>''),
		);
	}	
	


	static function inject_admin_class_cart($type) {
		
		switch($type) {
			case 'inject': {
				$vars = array(
					'patterns' => array(
						'onAfterCartShippingLoad'=>'/(\$shipping_id\s*\=\s*\$app\s*\->\s*getUserState\s*\(\s*HIKASHOP_COMPONENT\s*\.\s*\'\.shipping_id\'\s*\)\s*;.*?)'.
												'(if\s*\(\s*bccomp\s*\(\s*\$cart\s*\->\s*full_total)'.
												'/is',
					),
					'replacements' => array(
						'onAfterCartShippingLoad'=>'$1
# awocoupon_code START ===============================================================
$dispatcher->trigger(\'onAfterCartShippingLoad\', array( &$cart ) );
# awocoupon_code END =================================================================

			$2',

					),
				);
				break;
			}
			case 'check':
			case 'reject': {

				$vars = array(
					'patterns' => array(
						'onAfterCartShippingLoad'=>'/\n?#\s*awocoupon_code\s*START\s*===============================================================\s*'.
										'\$dispatcher\s*\->\s*trigger\s*\(\s*\'onAfterCartShippingLoad\'\s*,\s*array\s*\(\s*\&\s*\$cart\s*\)\s*\)\s*;\s*'.
										'#\s*awocoupon_code\s*END\s*=================================================================\s*/is',
					),
					'replacements' => array(
						'onAfterCartShippingLoad'=>'',
					),
				);
				break;
			}
		}
		
		
		$file = JPATH_ADMINISTRATOR.'/components/com_hikashop/classes/cart.php';
		return (object) array('file'=>$file,'vars'=>$vars);
	}
	static function inject_admin_class_discount($type) {
		
		switch($type) {
			case 'inject': {
				$vars = array(
					'patterns' => array(
						'onBeforeCouponLoad'=>'/(function\s+load\s*\(\s*\$coupon\s*\)\s*{)/is',
					),
					'replacements' => array(
						'onBeforeCouponLoad'=>'$1
# awocoupon_code START ===============================================================
$do=true;
JPluginHelper::importPlugin( \'hikashop\' );
$dispatcher =& JDispatcher::getInstance();
$item = $dispatcher->trigger( \'onBeforeCouponLoad\', array( &$coupon, & $do) );
if(!$do) return current($item);
# awocoupon_code END =================================================================',
		
					),
				);
				break;
			}
			case 'check':
			case 'reject': {

				$vars = array(
					'patterns' => array(
					
						'onBeforeCouponLoad'=>'/\s*#\s*awocoupon_code\s*START\s*===============================================================\s*'.
									'\$do\s*\=\s*true\s*;\s*'.
									'JPluginHelper\s*::\s*importPlugin\s*\(\s*\'hikashop\'\s*\)\s*;\s*'.
									'\$dispatcher\s*\=\s*\&\s*JDispatcher\s*::\s*getInstance\s*\(\s*\)\s*;\s*'.
									'\$item\s*\=\s*\$dispatcher\s*\->\s*trigger\s*\(\s*\'onBeforeCouponLoad\'\s*,\s*array\s*\(\s*\&\s*\$coupon\s*,\s*\&\s*\$do\s*\)\s*\)\s*;\s*'.
									'if\s*\(\s*\!\s*\$do\s*\)\s*return\s+current\s*\(\s*\$item\s*\)\s*;\s*'.
									'#\s*awocoupon_code\s*END\s*=================================================================/is',
					),
					'replacements' => array(
						'onBeforeCouponLoad'=>'',
					),
				);
				break;
			}
		}
		
		$file = JPATH_ADMINISTRATOR.'/components/com_hikashop/classes/discount.php';
		return (object) array('file'=>$file,'vars'=>$vars);
	}


}
