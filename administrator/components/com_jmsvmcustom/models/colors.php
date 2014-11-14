<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

class JmsvmcustomModelColors extends JModel
{
		
	function reorder($id,$direction) {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		// Initialize variables
		$db		= & JFactory::getDBO();
		
		if (isset( $id))
		{
			$row =& JTable::getInstance('Color', 'Table');
			$row->load( $id );
			$row->move($direction, null );

			$cache = & JFactory::getCache('com_jmsvmcustom');
			$cache->clean();
		}
		$msg = "Order saved!";
		$mainframe->redirect('index.php?option=com_jmsvmcustom&controller=colors', $msg);
	}

}
?>
