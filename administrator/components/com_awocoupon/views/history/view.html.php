<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewHistory extends AwoCouponView {

	function display($tpl = null) {

	
		global $AWOCOUPON_lang;

		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		parent::display_beforeload();

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		$lists 		= array();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		
		JToolBarHelper::title( JText::_('COM_AWOCOUPON_CP_HISTORY_USES'), 'history' );
		parent::display_aftertitle();
		
		$layoutName = JRequest::getWord('layout', 'default');
		
		switch($layoutName) {
			case 'edit': {
		
				JToolBarHelper::save('SAVEhistory');
				JToolBarHelper::divider();
				JToolBarHelper::cancel('CANCELhistory');
				JToolBarHelper::spacer();

				// fail if checked out not by 'me'
				$model			= $this->getModel();
				$couponlist     	= $this->get( 'CouponList' );
				$row     		= $this->get( 'Entry' );
				
				$post = JRequest::get('post');
				if ( $post ) {
					$row = (object) array_merge((array) $row, (array) $post); //bind the db return and post
				}

				// build the html for select boxes
				
				$states=array();
				$states[] = JHTML::_('select.option', '', '');
				foreach($couponlist as $key=>$value) $states[] = JHTML::_('select.option', $value->id, $value->coupon_code);
				$lists['couponlist'] = JHTML::_('select.genericlist', $states, 'coupon_id', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->coupon_id );		

				
				//assign data to template
				$this->assignRef('lists'      			, $lists);
				$this->assignRef('row'      			, $row);
				$this->assignRef('AWOCOUPON_lang'			, $AWOCOUPON_lang);
				
				break;
			}
			
			case 'order': {
				//get vars
				$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyorder.filter_order', 	'filter_order', 	'coupon_code', 'cmd' );
				$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyorder.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

				//Get data from the model
				$row      	= $this->get( 'Data');
				$pageNav 	= $this->get( 'Pagination' );
				
				// build the html for published		

				// table ordering
				$lists['order_Dir'] = $filter_order_Dir;
				$lists['order'] = $filter_order;
				


				//assign data to template
				$this->assignRef('lists'      	, $lists);
				$this->assignRef('row'      	, $row);
				$this->assignRef('pageNav' 		, $pageNav);
				$this->assignRef('type'			, $type);
				
				break;
			}
			
			case 'gift': {
				$db  		=  JFactory::getDBO();

				JToolBarHelper::deleteList( JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' ),'removehistorygift');


				//get vars
				$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.filter_order', 	'filter_order', 	'coupon_code', 'cmd' );
				$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

				//Get data from the model
				$row      	= $this->get( 'Data');
				$pageNav 	= $this->get( 'Pagination' );
		
				// build the html for published		
				$tmp = array();
				$tmp[] = JHTML::_('select.option',  'coupon', JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ) );
				$tmp[] = JHTML::_('select.option',  'user', JText::_( 'COM_AWOCOUPON_GBL_USERNAME' ) );
				$tmp[] = JHTML::_('select.option',  'last', JText::_( 'COM_AWOCOUPON_GBL_LAST_NAME' ) );
				$tmp[] = JHTML::_('select.option',  'first', JText::_( 'COM_AWOCOUPON_GBL_FIRST_NAME' ) );
				$search_type 	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.search_type', 	'search_type', 	'', 'cmd' );
				$lists['search_type'] = JHTML::_('select.genericlist', $tmp, 'search_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $search_type );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_STATUS' ).' - ' );
				$published = $AWOCOUPON_lang['published'];
				unset($published[-2]);
				foreach($published as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$filter_state 	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.filter_state', 	'filter_state', 	'', 'cmd' );
				$lists['published'] = JHTML::_('select.genericlist', $tmp, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );		


				// table ordering
				$lists['order_Dir'] = $filter_order_Dir;
				$lists['order'] = $filter_order;
		
				$lists['search'] = awoLibrary::dbEscape( trim(JString::strtolower( JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.search', 			'search', 			'', 'string' ) ) ) );


				//assign data to template
				$this->assignRef('lists'      	, $lists);
				$this->assignRef('row'      	, $row);
				$this->assignRef('pageNav' 		, $pageNav);
				$this->assignRef('ordering'		, $ordering);
				$this->assignRef('type'			, $type);
				
				break;
			}
			
			
			default: {
			

				$db  		= JFactory::getDBO();

				JToolBarHelper::addNew('ADDhistory');
				JToolBarHelper::divider();
				JToolBarHelper::deleteList( JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' ),'removehistorycoupon');
	

				//get vars
				$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.filter_order', 	'filter_order', 	'coupon_code', 'cmd' );
				$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

				//Get data from the model
				$row      	= $this->get( 'Data');
				$pageNav 	= $this->get( 'Pagination' );
		
				// build the html for published		
				$tmp = array();
				$tmp[] = JHTML::_('select.option',  'coupon', JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ) );
				$tmp[] = JHTML::_('select.option',  'user', JText::_( 'COM_AWOCOUPON_GBL_USERNAME' ) );
				$tmp[] = JHTML::_('select.option',  'last', JText::_( 'COM_AWOCOUPON_GBL_LAST_NAME' ) );
				$tmp[] = JHTML::_('select.option',  'first', JText::_( 'COM_AWOCOUPON_GBL_FIRST_NAME' ) );
				$search_type 	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.search_type', 	'search_type', 	'', 'cmd' );
				$lists['search_type'] = JHTML::_('select.genericlist', $tmp, 'search_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $search_type );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_STATUS' ).' - ' );
				$published = $AWOCOUPON_lang['published'];
				unset($published[-2]);
				foreach($published as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$filter_state 	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.filter_state', 	'filter_state', 	'', 'cmd' );
				$lists['published'] = JHTML::_('select.genericlist', $tmp, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_PERCENT_AMOUNT' ).' - ' );
				foreach($AWOCOUPON_lang['coupon_value_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$filter_coupon_value_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
				$lists['coupon_value_type'] = JHTML::_('select.genericlist', $tmp, 'filter_coupon_value_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_coupon_value_type );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_DISCOUNT_TYPE' ).' - ' );
				foreach($AWOCOUPON_lang['discount_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$filter_discount_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
				$lists['discount_type'] = JHTML::_('select.genericlist', $tmp, 'filter_discount_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_discount_type );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_FUNCTION_TYPE' ).' - ' );
				foreach($AWOCOUPON_lang['function_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$filter_function_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
				$lists['function_type'] = JHTML::_('select.genericlist', $tmp, 'filter_function_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_function_type );		

				// table ordering
				$lists['order_Dir'] = $filter_order_Dir;
				$lists['order'] = $filter_order;
				
				$lists['search'] = awoLibrary::dbEscape( trim(JString::strtolower( JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historycoupon.search', 			'search', 			'', 'string' ) ) ) );


				//assign data to template
				$this->assignRef('lists'      	, $lists);
				$this->assignRef('row'      	, $row);
				$this->assignRef('pageNav' 		, $pageNav);
				$this->assignRef('ordering'		, $ordering);

				break;
			}
			
		}

		parent::display($tpl);
	}
}

