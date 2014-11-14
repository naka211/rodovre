<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewCoupons extends AwoCouponView {

	function display($tpl = null) {
		global $AWOCOUPON_lang;

		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		parent::display_beforeload();

		//initialise variables
		$db  		= JFactory::getDBO();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		//get vars
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$filter_state 	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_state', 	'filter_state', 	'', 'cmd' );
		$filter_coupon_value_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
		$filter_discount_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
		$filter_function_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
		$filter_expiration = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_expiration', 		'filter_expiration', 		'', 'cmd' );
		$filter_template = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_template', 		'filter_template', 		'', 'cmd' );
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.search', 			'search', 			'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPONS' ), 'coupons' );
		parent::display_aftertitle();

		JToolBarHelper::custom('generatecouponform','copy','',JText::_( 'COM_AWOCOUPON_GBL_GENERATE' ),false);
		JToolBarHelper::divider();
		JToolBarHelper::publishList('publishcoupon');
		JToolBarHelper::unpublishList('unpublishcoupon');
		JToolBarHelper::divider();
		JToolBarHelper::addNew('addcoupon');
		JToolBarHelper::editList('editcoupon');
		JToolBarHelper::custom('duplicatecoupon','copy','',JText::_( 'COM_AWOCOUPON_GBL_COPY' ));
		JToolBarHelper::divider();
		JToolBarHelper::deleteList( JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' ),'removecoupon');
		JToolBarHelper::spacer();

		//Get data from the model
		$rows      	= $this->get( 'Data');
		$templateList	= $this->get( 'TemplateList');
		$pageNav 	= $this->get( 'Pagination' );

		// build the html for published		
		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_RPT_STATUS' ).' - ' );
		foreach($AWOCOUPON_lang['published'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
		$lists['published'] = JHTML::_('select.genericlist', $tmp, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );		

		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_CP_PERCENTAGE' ).' - ' );
		foreach($AWOCOUPON_lang['coupon_value_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
		$lists['coupon_value_type'] = JHTML::_('select.genericlist', $tmp, 'filter_coupon_value_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_coupon_value_type );		

		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_CP_DISCOUNT_TYPE' ).' - ' );
		foreach($AWOCOUPON_lang['discount_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
		$lists['discount_type'] = JHTML::_('select.genericlist', $tmp, 'filter_discount_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_discount_type );		

		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ).' - ' );
		foreach($AWOCOUPON_lang['function_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
		$lists['function_type'] = JHTML::_('select.genericlist', $tmp, 'filter_function_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_function_type );		


		if(empty($templateList)) $lists['filter_template'] = '';
		else {
			$tmp = array();
			$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_TEMPLATE' ).' - ' );
			foreach($templateList as $key=>$value) $tmp[] = JHTML::_('select.option', $value->id, $value->coupon_code);
			$lists['filter_template'] = JHTML::_('select.genericlist', $tmp, 'filter_template', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_template );		
		}
		
		// search filter
		$lists['search']= $search;

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('AWOCOUPON_lang', $AWOCOUPON_lang);

		parent::display($tpl);
	}
}
