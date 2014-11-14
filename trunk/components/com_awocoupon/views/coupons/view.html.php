<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class AwoCouponViewCoupons extends AwoCouponView {
	function display($tpl = null) {
	
		$db 	  = JFactory::getDBO();
		$document = JFactory::getDocument();
		$pathway  = JFactory::getApplication()->getPathway();
		$params = JFactory::getApplication()->getParams();
		
		JHTML::_('behavior.modal');

		$page_title = JText::_('COM_AWOCOUPON_CP_COUPONS');
		$rows      	= $this->get( 'Data');

		//Set page title information
		$menus	= JFactory::getApplication()->getMenu();
		$menu	= $menus->getActive();
		if (is_object( $menu )) {
			if(version_compare( JVERSION, '1.6.0', 'ge' )) {
				$menu_params = json_decode($menu->params);
				if (!$menu_params->page_title) $params->set('page_title',	$page_title);
			} 
			else {
				if(!class_exists('JParameter')) jimport( 'joomla.html.parameter' );
				$menu_params = new JParameter( $menu->params );
				if (!$menu_params->get( 'page_title')) $params->set('page_title',	$page_title);
			}
		} else {
			$params->set('page_title',	$page_title);
		}
		$document->setTitle( $params->get( 'page_title' ) );


		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order', 	'filter_order', 	'coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;


		//$this->assignRef('lists',	$lists);

		$this->assignRef('rows',	$rows);
		$this->assignRef('params',	$params);
		$this->assignRef('lists',	$lists);
		
		
		parent::display($tpl);
	}
}
