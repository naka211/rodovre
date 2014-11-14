<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');


class JmsvmcustomViewColors extends JView 
{
	function display($tpl = null)
	{
       parent::display($tpl);		
	}
	
	function _toolbarDefault()
	{
		$document =  JFactory::getDocument();
		$document->setTitle( JText::_('COLORS_MANAGER') );
		JToolBarHelper::title( JText::_( 'COLORS_MANAGER' ), 'generic.png' );
		
		JToolBarHelper::publish();
		JToolBarHelper::unpublish();
		JToolBarHelper::deleteList();
		JToolBarHelper::addNewX();
		JToolBarHelper::editListX();
	}
	 
	function _toolbarEdit(){
		$document =  JFactory::getDocument();
		$document->setTitle( JText::_('COLORS_MANAGER') );
		JToolBarHelper::title( JText::_( 'COLORS_MANAGER' ), 'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel( 'cancel' );
		
	}
}	