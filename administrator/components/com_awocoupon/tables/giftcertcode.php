<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableGiftCertCode extends JTable {
	var $id					= null;
	var $estore			= null;
	var $product_id			= null;
	var $code		= null;
	var $status			= null;
	var $note = null;

	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon_giftcert_code', 'id', $db );
	}

	function check() {
		global $AWOCOUPON_lang;
		$err = array();
		
		if(empty($AWOCOUPON_lang['estore'][$this->estore])) $err[] = JText::_('COM_AWOCOUPON_GBL_ERROR');

		if(!empty($err)) {
			foreach($err as $error) $this->setError($error);
			return false;
		}

		return true;
	}

}
