<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableProfile extends JTable {
	var $id					= null;
	var $title			= null;
	var $is_default		= null;
	var $from_name			= null;
	var $from_email			= null;
	var $bcc_admin			= null;
	var $email_subject_lang_id			= null;
	var $message_type			= null;
	var $image			= null;
	var $coupon_code_config			= null;
	var $coupon_value_config			= null;
	var $expiration_config			= null;
	var $freetext1_config			= null;
	var $freetext2_config			= null;
	var $freetext3_config			= null;
	var $email_body_lang_id			= null;

	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon_profile', 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {
		$err = array();
		
		if(empty($this->title)) $err[] = JText::_('COM_AWOCOUPON_PF_TITLE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_INPUT');
		if(empty($this->message_type)) $err[] = JText::_('COM_AWOCOUPON_PF_MESSAGE_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');

		if(!empty($err)) {
			foreach($err as $error) $this->setError($error);
			return false;
		}

		return true;
	}
}
