<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewConfig extends AwoCouponView {

	function display($tpl = null) {
		global $AWOCOUPON_lang;
		
		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		parent::display_beforeload();

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$editor		= JFactory::getEditor();
		
		
		//add css/ & js to document
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
		$params = new awoParams();
		
		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CFG_CONFIGURATION' ), 'config' );
		parent::display_aftertitle();

		JToolBarHelper::save('saveconfig');
        JToolBarHelper::apply('applyconfig');
        JToolBarHelper::divider();
        JToolBarHelper::cancel('cancelconfig');
        JToolBarHelper::spacer();
		
		$defaulterror     	= $this->get( 'DefaultError' );
		$orderstatus     	= $this->get( 'OrderStatus' );
		$iscaseSensitive   	= awoLibrary::getCaseSensitive();
		$estores     		= $this->get( 'InstalledEstores' );
		
		
		$tmp = array();
		$tmp[] = JHTML::_('select.option', 0, JText::_( 'COM_AWOCOUPON_GBL_NO' ));
		$tmp[] = JHTML::_('select.option', 1, JText::_( 'COM_AWOCOUPON_GBL_YES' ));
		$lists['enable_store_coupon'] = JHTML::_('select.genericlist', $tmp, 'params[enable_store_coupon]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('enable_store_coupon', 0) );		
		$lists['enable_multiple_coupon'] = JHTML::_('select.genericlist', $tmp, 'params[enable_multiple_coupon]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('enable_multiple_coupon', 0) );		
		$lists['casesensitive'] = JHTML::_('select.genericlist', $tmp, 'casesensitive', 'class="inputbox" size="1" ', 'value', 'text', $iscaseSensitive ? 1 : 0 );
		$lists['casesensitiveold'] = '<input type="hidden" name="casesensitiveold" value="'.($iscaseSensitive ? 1 : 0).'" />';
		$lists['giftcert_vendor_enable'] = JHTML::_('select.genericlist', $tmp, 'params[giftcert_vendor_enable]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('giftcert_vendor_enable', 0) );		
		$lists['enable_giftcert_discount_before_tax'] = JHTML::_('select.genericlist', $tmp, 'params[enable_giftcert_discount_before_tax]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('enable_giftcert_discount_before_tax', 0) );		
		$lists['enable_coupon_discount_before_tax'] = JHTML::_('select.genericlist', $tmp, 'params[enable_coupon_discount_before_tax]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('enable_coupon_discount_before_tax', 0) );		
		$lists['enable_frontend_image'] = JHTML::_('select.genericlist', $tmp, 'params[enable_frontend_image]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('enable_frontend_image', 0) );		
		
		$tmp = array();
		foreach($orderstatus as $v) $tmp[] = JHTML::_('select.option', $v->order_status_code, $v->order_status_name);
		$lists['giftcert_order_status'] = JHTML::_('select.genericlist', $tmp, 'params[giftcert_order_status]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('giftcert_order_status', 'C') );		
		$lists['ordercancel_order_status'] = JHTML::_('select.genericlist', $tmp, 'params[ordercancel_order_status][]', ' MULTIPLE class="inputbox" size="7" ', 'value', 'text', $params->get('ordercancel_order_status', '') );		
		
		$tmp = array();
		foreach($estores as $v) $tmp[] = JHTML::_('select.option', $v, $AWOCOUPON_lang['estore'][$v]);
		$lists['estore'] = JHTML::_('select.genericlist', $tmp, 'params[estore]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('estore', 'virtuemart') );		
		
		$tmp = array();
		$tmp[] = JHTML::_('select.option', ',', ',');
		$tmp[] = JHTML::_('select.option', ';', ';');
		$lists['csvDelimiter'] = JHTML::_('select.genericlist', $tmp, 'params[csvDelimiter]', 'class="inputbox" size="1" ', 'value', 'text', $params->get('csvDelimiter', ',') );		
		
		//assign data to template
		$this->assignRef('params'		, $params);
		$this->assignRef('defaulterror'	, $defaulterror);
		$this->assignRef('lists'		, $lists);
		$this->assignRef('editor'		, $editor);

		parent::display($tpl);
	}
}
