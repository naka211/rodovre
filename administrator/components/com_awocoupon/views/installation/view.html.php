<?php
/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 */
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewInstallation extends AwoCouponView {
	/**
	 * Creates the Entrypage
	 *
	 * @since 1.0
	 */
	function display( $tpl = null ) {
		global  $AWOCOUPON_lang;
		
		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		parent::display_beforeload();

		//initialise variables
		if(version_compare( JVERSION, '3.0.0', 'ge' )) JHtml::_('bootstrap.framework');
		$template	= JFactory::getApplication()->getTemplate();

		//build toolbar
		JToolBarHelper::title( JText::_('COM_AWOCOUPON_FI_INSTALLATION_CHECK'), 'installation' );
		parent::display_aftertitle();

		JToolBarHelper::save('saveinstallation',JText::_('COM_AWOCOUPON_GBL_INSTALL'));
		
		//Get data from the model
		$checks 	= $this->get( 'Check' );
		


		$this->assignRef('checks'		, $checks);		

		parent::display($tpl);

	}
	
}