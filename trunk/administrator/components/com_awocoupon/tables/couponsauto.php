<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableCouponsauto extends JTable {
	var $id					= null;
	var $coupon_id			= null;
	var $ordering 			= null;
	var $published			= null;

	
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon_auto', 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {
		global $AWOCOUPON_lang;
		$err = array();
		
		if(empty($this->coupon_id) || !ctype_digit($this->coupon_id)) $err[] = JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM');
		if(empty($this->published) || ($this->published!='1' && $this->published!='-1')) $err[] = JText::_('COM_AWOCOUPON_CP_PUBLISHED').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		
		if(!empty($err)) {
			foreach($err as $error) $this->setError($error);
			return false;
		}

		return true;
	}
}
