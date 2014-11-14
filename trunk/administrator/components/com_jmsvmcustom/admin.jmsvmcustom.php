<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

// no direct access
defined( '_JEXEC' ) or die;
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();
JHtml::stylesheet('style.css',JURI::root().'administrator/components/com_jmsvmcustom/assets/');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jmsvmcustom'.DS.'tables');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jmsvmcustom'.DS.'helpers'.DS.'vmcustom.php');
$task = JRequest::getCmd('task');
$controllerName = JRequest::getVar('controller','products');

$colors_active = false;
$products_active  = false;
if($controllerName=='config') {
	$config_active = true;
} elseif($controllerName=='colors') {
	$colors_active = true;
} elseif($controllerName=='products') {
	$products_active = true;
} else {
	$products_active = true;
}
JSubMenuHelper::addEntry(JText::_('COLORS_MANAGER'), 'index.php?option=com_jmsvmcustom&controller=colors',$colors_active);
JSubMenuHelper::addEntry(JText::_('PRODUCTS_MANAGER'), 'index.php?option=com_jmsvmcustom&controller=products',$products_active);
// allow fall through
require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
$controllerName = 'JmsvmcustomController'.$controllerName;

// Create the controller
$controller = new $controllerName();

// Perform the Request task
$controller->execute( JRequest::getCmd('task') );

// Redirect if set by the controller
$controller->redirect();
?>