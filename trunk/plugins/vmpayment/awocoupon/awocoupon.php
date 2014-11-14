<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

if (!class_exists ('vmPSPlugin'))  require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentAwoCoupon extends vmPSPlugin {

	function __construct(& $subject, $config) { parent::__construct ($subject, $config); }

	
	public function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrency){
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponVirtuemartCouponHandler::process_autocoupon();
		}
		
		return null;
	}

}
