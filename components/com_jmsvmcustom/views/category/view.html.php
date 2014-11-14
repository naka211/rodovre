<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JmsvmcustomViewCategory extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$pathway	= &$mainframe->getPathway();
		$document	= & JFactory::getDocument();
		parent::display($tpl);
	}
}
