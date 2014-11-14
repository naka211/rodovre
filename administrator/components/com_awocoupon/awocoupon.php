<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
//ini_set("display_errors", 1); error_reporting(E_ALL);

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT.'/tables');

global $AWOCOUPON_lang;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/awocoupon.config.php';
if(!defined('AWOCOUPON_ESTORE')) {
	echo 'Supported shopping cart not detecte';
	return;
}
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/awolibrary.php';


$jlang = JFactory::getLanguage();
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, null, true);


if(JRequest::getCmd('view','') == 'liveupdate') {
	require_once JPATH_COMPONENT_ADMINISTRATOR.'/liveupdate/liveupdate.php';
    LiveUpdate::handleRequest();
    return;
}

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

//Create the controller
$controller = new AwoCouponController( );

// Perform the Request task
$controller->execute( JRequest::getWord('task', ''));
$controller->redirect();

?>
<br><div align="right" style="font-size:9px;">&copy;<?php echo date('Y');?> <a href="http://awodev.com" target="_blank">AwoCoupon Pro</a> by Seyi Awofadeju</div>
