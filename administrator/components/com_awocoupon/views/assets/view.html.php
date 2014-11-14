<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewAssets extends AwoCouponView {

	function display($tpl = null) {
		global $AWOCOUPON_lang;
		
		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		JRequest::setVar('tmpl', 'component');

		parent::display_beforeload();

		$app = JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoslider.php';

		$document	= JFactory::getDocument();
		$document->addStyleSheet($templateDir.'/css/template.css');
		$document->addStyleSheet('templates/system/css/system.css');

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		$_type		= JRequest::getVar( 'type','' );
		
		//get vars
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.assets_'.$_type.'.filter_order', 	'filter_order', 	'asset_name', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.assets_'.$_type.'.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$asset_name = $asset_title = '';
		switch($_type) {
			case 'product': $asset_name = 'COM_AWOCOUPON_CP_PRODUCT'; $asset_title = 'COM_AWOCOUPON_CP_PRODUCTS'; break;
			case 'category': $asset_name = 'COM_AWOCOUPON_CP_CATEGORY'; $asset_title = 'COM_AWOCOUPON_CP_CATEGORYS'; break;
			case 'manufacturer': $asset_name = 'COM_AWOCOUPON_CP_MANUFACTURER'; $asset_title = 'COM_AWOCOUPON_CP_MANUFACTURERS'; break;
			case 'vendor': $asset_name = 'COM_AWOCOUPON_CP_VENDOR'; $asset_title = 'COM_AWOCOUPON_CP_VENDORS'; break;
			case 'shipping': $asset_name = 'COM_AWOCOUPON_CP_SHIPPING'; $asset_title = 'COM_AWOCOUPON_CP_SHIPPING'; break;
			case 'parent': $asset_name = 'COM_AWOCOUPON_CP_COUPON'; $asset_title = 'COM_AWOCOUPON_CP_COUPONS'; break;
		}
		//Get data from the model
		$row      	= $this->get( 'Data');
		$row->_type = $_type;
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		//assign data to template
		$this->assignRef('asset_name'      	, $asset_name);
		$this->assignRef('asset_title'      	, $asset_title);
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('now'			, $now);
		$this->assignRef('AWOCOUPON_lang', $AWOCOUPON_lang);

		parent::display($tpl);
	}
}
