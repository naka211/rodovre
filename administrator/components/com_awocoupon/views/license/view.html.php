<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewLicense extends AwoCouponView {

	function display($tpl = null) {
		
		parent::display_beforeload();

		//initialise variables
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_LI_LICENSE' ), 'license' );
		parent::display_aftertitle();

		//Get data from the model
		$rows      	= $this->get( 'Data');
		if(empty($rows->local_key) && empty($rows->license)) JToolBarHelper::addNew('licenseactivate',JText::_( 'COM_AWOCOUPON_LI_ACTIVATE' ));
		elseif($rows->ispermanent != 'yes') JToolBarHelper::addNew('licenseupdatelocalkey',JText::_( 'COM_AWOCOUPON_LI_UPDATE_LOCAL_KEY' ));
		if(!empty($rows->expiration) && $rows->expiration<time()) JToolBarHelper::addNew( 'licenseupdateexpired', JText::_( 'COM_AWOCOUPON_LI_UPDATE_EXPIRED' ));
		if(!empty($rows->license)) JToolBarHelper::custom( 'licensedelete', 'delete','',JText::_( 'COM_AWOCOUPON_LI_DELETE' ),false);
		//assign data to template
		$this->assignRef('rows'      	, $rows);

		parent::display($tpl);
	}
}
