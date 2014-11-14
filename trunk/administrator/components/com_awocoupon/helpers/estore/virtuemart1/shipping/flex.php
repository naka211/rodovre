<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class awo_flex {
    var $classname = 'flex';
	
	function awo_flex() {
		if(file_exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/classes/shipping/flex.cfg.php')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/classes/shipping/flex.cfg.php';
		} else define('FLEX_BASE_AMOUNT',0);
	}
	
	function get_all_rates() {
		
		$o = array();
		if(file_exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/classes/shipping/flex.cfg.php')) {
			$o['_raw'][$this->classname.'-1'] = (object) array(
						'dbshipper_id'=>$this->classname.'-1',
						'shipper_string'=>JText::_('FLAT RATE').': '.FLEX_BASE_AMOUNT,
						'dd_name'=>JText::_('FLAT RATE').': '.FLEX_BASE_AMOUNT,
					);
			$o[$this->classname][] = $o['_raw'][$this->classname.'-1'];
		}
		return $o;
	}

	
	function get_unused_rates($coupon_id,$current_rates) {
		$o = array();
		if(!isset($current_rates[$this->classname.'-1'])) {
			if(file_exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/classes/shipping/flex.cfg.php')) {
				$o[$this->classname][] = (object) array(
							'dbshipper_id'=>$this->classname.'-1',
							'shipper_string'=>JText::_('FLAT RATE').': '.FLEX_BASE_AMOUNT,
						);
			}
		}
		return $o;
	}
	
	function get_module_name() { return 'Flex Shipping'; }
	function get_rate_name($rate_id) {
		return $this->get_module_name().'-'.JText::_('FLAT RATE').': '.FLEX_BASE_AMOUNT;
	}
	function get_rate_id($rate_array) { return 1; }
}