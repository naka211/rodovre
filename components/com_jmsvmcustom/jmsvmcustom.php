<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.helper');
//error_reporting(0);
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();
if(!class_exists('VmImage')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'image.php'); //dont remove that file it is actually in every view except the state view
if(!class_exists('shopFunctionsF'))require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php'); //dont remove that file it is actually in every view
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_jmsvmcustom/assets/css/style.css');
//$document->addScript(JURI::base().'components/com_jmsvmcustom/assets/js/script.js');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jmsvmcustom'.DS.'tables');
require_once(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'helper.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jmsvmcustom'.DS.'helpers'.DS.'vmcustom.php');

// Create the controller
$controllerName = JRequest::getVar('view','category','default','cmd');

if(file_exists(JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php')) {
	require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );	
} else {
	JError::raiseError('500','The view not found');
}

$controllerName = 'JmsvmcustomController'.$controllerName;

// Create the controller
$controller = new $controllerName();

// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect();