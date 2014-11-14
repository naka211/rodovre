<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class awo_ship_item_specific{
    var $classname = 'ship_item_specific';
	
	function awo_ship_item_specific() {
	}
	
	function get_all_rates() {
		
		$o = array();
		$o['_raw'][$this->classname.'-1'] = (object) array(
					'dbshipper_id'=>$this->classname.'-1',
					'shipper_string'=>'Standard Shipping',
					'dd_name'=>'Standard Shipping',
				);
		$o[$this->classname][] = $o['_raw'][$this->classname.'-1'];
		return $o;
	}

	
	function get_unused_rates($coupon_id,$current_rates) {
		$o = array();
		if(!isset($current_rates[$this->classname.'-1'])) {
			$o[$this->classname][] = (object) array(
						'dbshipper_id'=>$this->classname.'-1',
						'shipper_string'=>'Standard Shipping',
					);
		}
		return $o;
	}
	
	function get_module_name() { return 'Virtuemart Shipping'; }
	function get_rate_name($rate_id) {
		return $this->get_module_name().'- Standard Shipping';
	}
	function get_rate_id($rate_array) { return 1; }
}