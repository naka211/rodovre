<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgVmCouponAwoCoupon extends JPlugin {

	function plgVmCouponAwoCoupon(& $subject, $config){
		parent::__construct($subject, $config);
	}

	function plgVmValidateCouponCode($_code,$_billTotal) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemartCouponHandler::validate_coupon($_code);
		}
		
		return null;
	}

	function plgVmRemoveCoupon($_code,$_force) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemartCouponHandler::remove_coupon_code($_code);
		}
		
		return null;
	}
	
	function plgVmCouponHandler($_code, & $_cartData, & $_cartPrices) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemartCouponHandler::process_coupon_code($_code, $_cartData, $_cartPrices );
		}
		
		return null;
	}
	
	function plgVmCouponUpdateOrderStatus($data, $order_status_code) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart/giftcerthandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			AwoCouponVirtuemartGiftcertHandler::process($data,$order_status_code);
		}
		
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemartCouponHandler::order_cancel_check($data);
		}
	}
	
}

// No closing tag