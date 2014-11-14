<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewCoupon extends AwoCouponView {

	function display($tpl = null) {

	
		global $AWOCOUPON_lang;

		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		parent::display_beforeload();
		
		$document	= JFactory::getDocument();

		$layout = $this->getLayout();
		if($layout == 'generate') {
			JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_GENERATE_COUPONS' ), 'generic.png' );
			parent::display_aftertitle();

			JToolBarHelper::save('generatecoupons');
			JToolBarHelper::divider();
			JToolBarHelper::cancel('cancelcoupon');
			JToolBarHelper::spacer();
		
			require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/plgautogenerate.php';
			$templates = awoAutoGenerate::getCouponTemplates(AWOCOUPON_ESTORE);

			array_unshift($templates,(object)array('id'=>'','coupon_code'=>''));
			$this->templatelist = JHTML::_('select.genericlist',   $templates, 'template', 'class="inputbox" style="width:250px;"', 'id', 'coupon_code');
			//$this->assignRef('ordering'		, $ordering);
			$this->assignRef('AWOCOUPON_lang', $AWOCOUPON_lang);

			parent::display($tpl);
			return;
		}

		//Load pane behavior

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		$lists 		= array();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoslider.php';
		
		//add css/ & js to document
		
		$media_path = JURI::root(true).'/media/com_awocoupon';
		$document->addStyleSheet($media_path.'/css/jquery-ui.css');
		$document->addScript($media_path.'/js/jquery-ui.min.js');
				
		$document->addStyleSheet($media_path.'/css/pqgrid.min.css');
		$document->addScript($media_path.'/js/pqgrid.min.js');
		
		$document->addScript($media_path.'/js/jquery.ui.autocomplete.ext.js');
		
		$document->addScript(com_awocoupon_ASSETS.'/js/coupon.js?219');
		$document->addScript(com_awocoupon_ASSETS.'/js/coupon_cumulative_value.js');

		// force ie7 compatible mode because search of multiple dropdown very very slow in ie8
		$headData = $document->getHeadData();
		$headData['metaTags']['http-equiv']['X-UA-Compatible'] = 'E=EmulateIE7';
		$document->setHeadData($headData);

		
		//create the toolbar
		if ( $cid ) JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPON' ), 'edit' );
		else JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPON' ), 'new' );
		parent::display_aftertitle();

		JToolBarHelper::save('savecoupon');
        JToolBarHelper::apply('applycoupon');
		JToolBarHelper::divider();
		JToolBarHelper::cancel('cancelcoupon');
		JToolBarHelper::spacer();

		// fail if checked out not by 'me'
		$row     		= $this->get( 'Entry' );
		

		$post = JRequest::get('post');
		if ( !empty($post) ) {
			if(!empty($post['userlist'])) {
				$tmp = $post['userlist'];
				$post['userlist'] = array();
				foreach($tmp as $id) $post['userlist'][$id] = (object) array('user_id'=>$id,'user_name'=>$post['usernamelist'][$id]);
			} //else $post['productlist'] = array();
			
			if(!empty($post['assetlist'])) {
				$tmp = $post['assetlist'];
				$post['assetlist'] = array();
				foreach($tmp as $id) $post['assetlist'][$id] = (object) array('asset_id'=>$id,'asset_name'=>$post['assetnamelist'][$id]);
			} //else $post['productlist'] = array();

			if(!empty($post['assetlist2'])) {
				$tmp = $post['assetlist2'];
				$post['assetlist2'] = array();
				foreach($tmp as $id) $post['assetlist2'][$id] = (object) array('asset_id'=>$id,'asset_name'=>$post['asset2namelist'][$id]);
			} //else $post['productlist'] = array();

			$row = (object) array_merge((array) $row, (array) $post); //bind the db return and post
			//$row->bind($post);
		}

		// build the html for select boxes
		$states=array();
		$states[] = JHTML::_('select.option', '', '');
		foreach($AWOCOUPON_lang['function_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['function_type'] = JHTML::_('select.genericlist', $states, 'function_type', 'class="inputbox" style="width:147px;" onchange="funtion_type_change();"', 'value', 'text', $row->function_type );		

		$states=array();
		foreach($AWOCOUPON_lang['published'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['published'] = JHTML::_('select.genericlist', $states, 'published', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->published );		
		$states=array();
		foreach($AWOCOUPON_lang['parent_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['parent_type'] = JHTML::_('select.genericlist', $states, 'parent_type', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->parent_type );		
		$states=array();
		foreach($AWOCOUPON_lang['buy_xy_process_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['buy_xy_process_type'] = JHTML::_('select.genericlist', $states, 'buy_xy_process_type', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->buy_xy_process_type );		
		$states=array();
		foreach($AWOCOUPON_lang['coupon_value_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['coupon_value_type'] = JHTML::_('select.genericlist', $states, 'coupon_value_type', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->coupon_value_type );		
		$states=array();
		foreach($AWOCOUPON_lang['discount_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['discount_type'] = JHTML::_('select.genericlist', $states, 'discount_type', 'class="inputbox" style="width:147px;" ', 'value', 'text', $row->discount_type );		
		$states=array();
		foreach($AWOCOUPON_lang['min_value_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['min_value_type'] = JHTML::_('select.genericlist', $states, 'min_value_type', 'class="inputbox" style="width:100px;"', 'value', 'text', $row->min_value_type );		
		$states=array();
		$states[] = JHTML::_('select.option', '', '');
		foreach($AWOCOUPON_lang['num_of_uses_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['num_of_uses_type'] = JHTML::_('select.genericlist', $states, 'num_of_uses_type', 'class="inputbox" style="width:100px;"', 'value', 'text', $row->num_of_uses_type );		
		$states=array();
		foreach($AWOCOUPON_lang['user_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['user_type'] = JHTML::_('select.genericlist', $states, 'user_type', 'class="inputbox" style="width:200px;" onchange="user_type_change();"', 'value', 'text', $row->user_type );		
		$states = array(JHTML::_('select.option', '', ''),
						JHTML::_('select.option', 'product', JText::_( 'COM_AWOCOUPON_CP_PRODUCT' )),
						JHTML::_('select.option', 'category', JText::_( 'COM_AWOCOUPON_CP_CATEGORY' )),
						JHTML::_('select.option', 'manufacturer', JText::_( 'COM_AWOCOUPON_CP_MANUFACTURER' )),
						JHTML::_('select.option', 'vendor', JText::_( 'COM_AWOCOUPON_CP_VENDOR' )),
					);
		$lists['asset1_function_type'] = JHTML::_('select.genericlist', $states, 'asset1_function_type', 'class="inputbox" style="width:147px;" onchange="asset_type_change(1);"', 'value', 'text', $row->asset1_function_type );		
		$lists['asset2_function_type'] = JHTML::_('select.genericlist', $states, 'asset2_function_type', 'class="inputbox" style="width:147px;" onchange="asset_type_change(2);"', 'value', 'text', $row->asset2_function_type );		
		
		
		//assign data to template
		$this->assignRef('lists'      			, $lists);
		$this->assignRef('row'      			, $row);
		$this->assignRef('AWOCOUPON_lang'			, $AWOCOUPON_lang);

		parent::display($tpl);
	}
	
}

