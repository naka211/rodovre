<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewAbout extends AwoCouponView {

	function display( $tpl = null ) {
	
		parent::display_beforeload();

		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_AT_ABOUT' ),'awocoupon');
		parent::display_aftertitle();

		//Retreive version from install file
		$element = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon'.(version_compare(JVERSION, '3.0.0', 'ge') ? '_j3':'').'.xml');
		$version = (string)$element->version;
		
		$this->assign( 'version'	, $version );
		
		parent::display( $tpl );
	}
}