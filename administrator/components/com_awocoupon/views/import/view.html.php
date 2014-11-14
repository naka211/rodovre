<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewImport extends AwoCouponView {

	function display($tpl = null) {
	
		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		parent::display_beforeload();

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		$lists 		= array();
				
		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_IMP_IMPORT' ), 'import' );
		parent::display_aftertitle();

		JToolBarHelper :: custom( 'saveimport', 'upload', NULL, JText::_( 'COM_AWOCOUPON_GBL_UPLOAD' ), false, false );
		JToolBarHelper::divider();
		JToolBarHelper::cancel('cancelimport');
		JToolBarHelper::spacer();

		$exclude_first_row = JRequest::getVar( 'exclude_first_row', '1' );
		$store_none_errors = JRequest::getVar( 'store_none_errors', '1' );
		
		//assign data to template
		$this->assignRef('exclude_first_row'	, $exclude_first_row);
		$this->assignRef('store_none_errors'	, $store_none_errors);

		parent::display($tpl);
	}
}
