<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgVirtuemartAwoCoupon extends JPlugin {

	function __construct(& $subject, $config){
		parent::__construct($subject, $config);		
	}


	function onCouponRemove($d) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart1/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemart1CouponHandler::remove_coupon_code($d);
		}
		
		return null;
	}
	
	function onCouponProcess($d) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart1/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemart1CouponHandler::process_coupon_code($d );
		}
		
		return null;
	}
	
	function onCouponProcessAuto($d) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart1/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemart1CouponHandler::process_autocoupon($d);
		}
		return null;
	
	}

	function onOrderStatusUpdate($d, $order_status_code) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart1/giftcerthandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			AwoCouponVirtuemart1GiftcertHandler::process($d,$order_status_code);
		}
		
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart1/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemart1CouponHandler::order_cancel_check($d,$order_status_code);
		}
	}
	
}

// No closing tag