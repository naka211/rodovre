<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgRedshop_CouponAwoCoupon extends JPlugin {

	function plgRedshop_CouponAwoCoupon(& $subject, $config){
		parent::__construct($subject, $config);
	}

	function onOrderGiftcertSend($order_id) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/redshop/giftcerthandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			AwoCouponRedshopGiftcertHandler::process($order_id);
		}
	}

	function onCouponRemove($order_id) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/redshop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			AwoCouponRedshopCouponHandler::process_coupon_code(null );
			return AwoCouponRedshopCouponHandler::remove_coupon_code($order_id);
		}
		
		return null;
	}

	function onCouponProcess($c_data) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/redshop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponRedshopCouponHandler::process_coupon_code($c_data );
		}
		
		return null;
	}
	
	function onBeforeCartLoad() {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/redshop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponRedshopCouponHandler::process_autocoupon();
		}
		return null;
	
	}
	
}

// No closing tag