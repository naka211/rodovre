<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableGiftCert extends JTable {
	var $id					= null;
	var $estore			= null;
	var $product_id			= null;
	var $coupon_template_id		= null;
	var $profile_id			= null;
	var $expiration_number = null;
	var $expiration_type = null;
	var $vendor_name = null;
	var $vendor_email = null;
	var $published			= null;

	
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon_giftcert_product', 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {
		global $AWOCOUPON_lang;
		$err = array();
		
		if(empty($AWOCOUPON_lang['estore'][$this->estore])) $err[] = JText::_('COM_AWOCOUPON_GBL_ERROR');

		if(empty($this->product_id) || !ctype_digit($this->product_id)) $err[] = JText::_('COM_AWOCOUPON_CP_PRODUCT').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM');
		if(empty($this->coupon_template_id) || !ctype_digit($this->coupon_template_id)) $err[] = JText::_('COM_AWOCOUPON_CP_TEMPLATE').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM');
		if(!empty($this->profile_id) && !ctype_digit($this->profile_id)) $err[] = JText::_('COM_AWOCOUPON_PF_PROFILE').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
		if(!empty($this->expiration_number) || !empty($this->expiration_type)) {
		
			if(empty($this->expiration_number) || empty($this->expiration_type)) $err[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			elseif(!ctype_digit($this->expiration_number))  $err[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			elseif($this->expiration_type!='day' && $this->expiration_type!='month' && $this->expiration_type!='year') $err[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		}
		
		
		
		if(empty($this->published) || ($this->published!='1' && $this->published!='-1')) $err[] = JText::_('COM_AWOCOUPON_CP_PUBLISHED').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		
		if(!empty($err)) {
			foreach($err as $error) $this->setError($error);
			return false;
		}

		return true;
	}
}
