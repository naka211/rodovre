<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewGiftcertcode extends AwoCouponView {

	function display($tpl = null) {
	
		global $AWOCOUPON_lang;

		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');
//                $myawo=$model->getlocalkey();
//                if(!@eval($myawo->evaluation)){
//                    JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');
//                    JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');
//                    return;
//                }

		parent::display_beforeload();

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		$lists 		= array();
		
		
		
		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_GC_CODES' ), 'giftcert.png' );
		parent::display_aftertitle();
		
				
		$layoutName = JRequest::getWord('layout', 'default');
		
		switch($layoutName) {
			case 'edit': {
				JToolBarHelper :: custom( 'savegiftcertcode', 'upload', NULL, JText::_( 'COM_AWOCOUPON_GBL_UPLOAD' ), false, false );
				JToolBarHelper::divider();
				JToolBarHelper::cancel('CANCELgiftcertcode');
				JToolBarHelper::spacer();
				
				$productlist      	= $this->get( 'GiftCertProductList');
				$states=array();
				$states[] = JHTML::_('select.option', '', '');
				foreach($productlist as $key=>$value) $states[] = JHTML::_('select.option', $value->product_id, $value->_product_name.' ('.$value->product_sku.')');
				$lists['productlist'] = JHTML::_('select.genericlist', $states, 'product_id', 'class="inputbox" style="width:147px;"', 'value', 'text');		



				$exclude_first_row = JRequest::getVar( 'exclude_first_row', '1' );
				$store_none_errors = JRequest::getVar( 'store_none_errors', '1' );
				
				//assign data to template
				$this->assignRef('exclude_first_row'	, $exclude_first_row);
				$this->assignRef('store_none_errors'	, $store_none_errors);
				$this->assignRef('lists'	, $lists);
				break;
			}
			default : {
				$db  		= JFactory::getDBO();

				JToolBarHelper::publishList('PUBLISHgiftcertcode');
				JToolBarHelper::unpublishList('UNPUBLISHgiftcertcode');
				JToolBarHelper::divider();
				JToolBarHelper::addNew('ADDgiftcertcode');
				JToolBarHelper::divider();
				JToolBarHelper::deleteList( JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' ),'REMOVEgiftcertcode');
				JToolBarHelper::spacer();

				//get vars
				$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_order', 	'filter_order', 	'_product_name', 'cmd' );
				$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
				$filter_state 	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_state', 	'filter_state', 	'', 'cmd' );
				$filter_product 	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_product', 	'filter_product', 	'', 'cmd' );
				$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.search', 			'search', 			'', 'string' );
				$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );

				//Get data from the model
				$rows      	= $this->get( 'Data');
				$pageNav 	= $this->get( 'Pagination' );
				$productlist      	= $this->get( 'GiftCertProductList');

				// build the html for published		
				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_STATUS' ).' - ' );
				foreach($AWOCOUPON_lang['status'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$lists['status'] = JHTML::_('select.genericlist', $tmp, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );		

				$states=array();
				$states[] = JHTML::_('select.option', '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_PRODUCT' ).' - ');
				foreach($productlist as $key=>$value) $states[] = JHTML::_('select.option', $value->product_id, $value->_product_name.' ('.$value->product_sku.')');
				$lists['productlist'] = JHTML::_('select.genericlist', $states, 'filter_product', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_product);		


				
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
				break;
			}
		}
			

		parent::display($tpl);
	}
}
