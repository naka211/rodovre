<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelAbout extends AwoCouponModel {

	function __construct() {
		$this->_type = 'about';
		parent::__construct();
	}
}
