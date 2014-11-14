<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableLangText extends JTable {
	var $id					= null;
	var $elem_id			= null;
	var $lang	 			= null;
	var $text				= null;

	
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon_lang_text', 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {

		return true;
	}
}
