<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

function awocouponinstall_UPGRADE_214() {
	$file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/admin.awocoupon.php';
	if(file_exists($file)) unlink($file);
}