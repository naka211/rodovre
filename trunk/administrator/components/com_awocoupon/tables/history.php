<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableHistory extends JTable {
	var $id					= null;
	var $estore			= null;
	var $coupon_id			= null;
	var $coupon_entered_id		= null;
	var $user_id = null;
	var $user_email = null;
	var $coupon_discount = null;
	var $shipping_discount			= null;
	var $order_id			= null;
	var $productids			= null;
	var $timestamp			= null;

	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon_history', 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {
		global $AWOCOUPON_lang;
		$err = array();
		
		if(empty($AWOCOUPON_lang['estore'][$this->estore])) $err[] = JText::_('COM_AWOCOUPON_GBL_ERROR');
		
		if(empty($this->coupon_id) || !ctype_digit($this->coupon_id)) $err[] = JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
		if(empty($this->user_id) || !ctype_digit($this->user_id)) $err[] = JText::_('COM_AWOCOUPON_GBL_USERNAME').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(!empty($this->user_email)) {
			jimport('joomla.mail.helper');
			if (!JMailHelper::isEmailAddress($this->user_email)) $err[] = JText::_('COM_AWOCOUPON_GBL_EMAIL').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		}
		if(!empty($this->coupon_discount) && (!is_numeric($this->coupon_discount) || $this->coupon_discount<0))  $err[] = JText::_('COM_AWOCOUPON_CP_DISCOUNT').' ('.JText::_('COM_AWOCOUPON_CP_PRODUCT').'): '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(!empty($this->shipping_discount) && (!is_numeric($this->shipping_discount) || $this->shipping_discount<0))  $err[] = JText::_('COM_AWOCOUPON_CP_DISCOUNT').' ('.JText::_('COM_AWOCOUPON_CP_SHIPPING').'): '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		//if(empty($this->shipping_discount) && empty($this->coupon_discount)) $err[] = JText::_('COM_AWOCOUPON_CP_DISCOUNT').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_INPUT');

		if(!empty($err)) {
			foreach($err as $error) $this->setError($error);
			return false;
		}

		return true;
	}
}
