<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// check if logged in
$user = JFactory::getUser();
if(empty($user->id)) { JFactory::getApplication()->redirect( 'index.php' );}

global $AWOCOUPON_lang;

require JPATH_COMPONENT_ADMINISTRATOR.'/awocoupon.config.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/awolibrary.php';

$jlang = JFactory::getLanguage();
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, null, true);
$jlang->load('com_awocoupon', JPATH_SITE, 'en-GB', true);
$jlang->load('com_awocoupon', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_awocoupon', JPATH_SITE, null, true);


require_once JPATH_COMPONENT.'/controller.php';

$controller = new AwoCouponController( );
$controller->registerTask( 'results', 'display' );
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
