<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewProfile extends AwoCouponView {

	function display($tpl = null) {

	
		global $AWOCOUPON_lang;

		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}


		parent::display_beforeload();

		//Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.modal');

		//initialise variables
		$editor		= JFactory::getEditor();
		$cid 		= JRequest::getVar( 'cid' );
		$lists 		= array();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		
		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_PF_PROFILES' ), 'profile' );
		parent::display_aftertitle();
		
		$layoutName = JRequest::getWord('layout', 'default');
		
		
		switch($layoutName) {
			case 'edit': {

				JToolBarHelper::save('SAVEprofile');
				JToolBarHelper::divider();
				JToolBarHelper::cancel('CANCELprofile');
				JToolBarHelper::spacer();

				$row     	= $this->get( 'Entry' );
				$font_color     	= $this->get( 'fontcolor' );
				$imagedd     	= $this->get( 'images' );
				$fontdd     	= $this->get( 'fonts' );

				require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoslider.php';
				$tagdesc = array();
				JPluginHelper::importPlugin('awocoupon');
				$_dispatcher = JDispatcher::getInstance();
				$plgtagdescriptions = $_dispatcher->trigger('getTagDescription');
				foreach($plgtagdescriptions as $t) {
					if(!is_array($t)) continue;
					foreach($t as $t2) {
						if(!isset($t2['title']) || !isset($t2['description'])) continue;
						$tagdesc[] = $t2;
					}
				}
				
				
				$post = JRequest::get('post');
				if ( $post ) {
					$row = (object) array_merge((array) $row, (array) $post); //bind the db return and post
					$text = JRequest::getVar( 'email_html', '', 'post', 'string', JREQUEST_ALLOWRAW );
					if(!empty($text)) {
						$text		= str_replace( '<br>', '<br />', $text );
						$row->email_body = $text;
					}
					foreach($row->imgplugin as $k=>$r1) foreach($r1 as $k2=>$r2) $row->imgplugin[$k][$k2] = (object)$r2;
				}	

				$tmp = array();
				$tmp[] = JHTML::_('select.option', '', JText::_( 'COM_AWOCOUPON_PF_DO_NOT_DISPLAY' ));
				$tmp[] = JHTML::_('select.option', 'Y-m-d', date('Y-m-d'));
				$tmp[] = JHTML::_('select.option', 'm/d/Y', date('m/d/Y'));
				$tmp[] = JHTML::_('select.option', 'd/m/Y', date('d/m/Y'));
				$tmp[] = JHTML::_('select.option', 'Y/m/d', date('Y/m/d'));
				$tmp[] = JHTML::_('select.option', 'd.m.Y', date('d.m.Y'));
				$tmp[] = JHTML::_('select.option', 'M j Y', date('M j Y'));
				$tmp[] = JHTML::_('select.option', 'j M Y', date('j M Y'));
				$tmp[] = JHTML::_('select.option', 'Y M j', date('Y M j'));
				$tmp[] = JHTML::_('select.option', 'F j Y', date('F j Y'));
				$tmp[] = JHTML::_('select.option', 'j F Y', date('j F Y'));
				$tmp[] = JHTML::_('select.option', 'Y F j', date('Y F j'));
				$lists['expiration_text'] = JHTML::_('select.genericlist', $tmp, 'expiration_text', 'class="inputbox" size="1" ', 'value', 'text', $row->expiration_text );		
			
				
				
				
				$tmp = array();
				$tmp[] = JHTML::_('select.option', '', '');
				foreach($AWOCOUPON_lang['giftcert_message_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$lists['message_type'] = JHTML::_('select.genericlist', $tmp, 'message_type', 'class="inputbox" size="1" onchange="message_type_change()" ', 'value', 'text', $row->message_type );		
				
				
				$tmp = array();
				$tmp[] = JHTML::_('select.option', '', '----');
				foreach($imagedd as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$lists['image'] = JHTML::_('select.genericlist', $tmp, 'image', 'class="inputbox" size="1" onchange="checkimage();"', 'value', 'text', $row->image );		
				
				$aligndd = array('L'=>JText::_( 'COM_AWOCOUPON_PF_LEFT' ),'C'=>JText::_( 'COM_AWOCOUPON_PF_MIDDLE' ),'R'=>JText::_( 'COM_AWOCOUPON_PF_RIGHT' ),);
				$tmp_align = array();
				foreach($aligndd as $key=>$value) $tmp_align[] = JHTML::_('select.option', $key, $value);
				$lists['couponcode_align'] = JHTML::_('select.genericlist', $tmp_align, 'couponcode_align', 'class="inputbox" size="1"', 'value', 'text', $row->couponcode_align );		
				$lists['couponvalue_align'] = JHTML::_('select.genericlist', $tmp_align, 'couponvalue_align', 'class="inputbox" size="1"', 'value', 'text', $row->couponvalue_align );		
				$lists['expiration_align'] = JHTML::_('select.genericlist', $tmp_align, 'expiration_align', 'class="inputbox" size="1"', 'value', 'text', $row->expiration_align );		
				$lists['freetext1_align'] = JHTML::_('select.genericlist', $tmp_align, 'freetext1_align', 'class="inputbox" size="1"', 'value', 'text', $row->freetext1_align );		
				$lists['freetext2_align'] = JHTML::_('select.genericlist', $tmp_align, 'freetext2_align', 'class="inputbox" size="1"', 'value', 'text', $row->freetext2_align );		
				$lists['freetext3_align'] = JHTML::_('select.genericlist', $tmp_align, 'freetext3_align', 'class="inputbox" size="1"', 'value', 'text', $row->freetext3_align );		

				$tmp_font = array();
				foreach($fontdd as $key=>$value) $tmp_font[] = JHTML::_('select.option', $key, $value);
				$lists['couponcode_font'] = JHTML::_('select.genericlist', $tmp_font, 'couponcode_font', 'class="inputbox" size="1"', 'value', 'text', $row->couponcode_font );		
				$lists['couponvalue_font'] = JHTML::_('select.genericlist', $tmp_font, 'couponvalue_font', 'class="inputbox" size="1"', 'value', 'text', $row->couponvalue_font );		
				$lists['expiration_font'] = JHTML::_('select.genericlist', $tmp_font, 'expiration_font', 'class="inputbox" size="1"', 'value', 'text', $row->expiration_font );		
				$lists['freetext1_font'] = JHTML::_('select.genericlist', $tmp_font, 'freetext1_font', 'class="inputbox" size="1"', 'value', 'text', $row->freetext1_font );		
				$lists['freetext2_font'] = JHTML::_('select.genericlist', $tmp_font, 'freetext2_font', 'class="inputbox" size="1"', 'value', 'text', $row->freetext2_font );		
				$lists['freetext3_font'] = JHTML::_('select.genericlist', $tmp_font, 'freetext3_font', 'class="inputbox" size="1"', 'value', 'text', $row->freetext3_font );		

				$tmp_fontcolor = '';
				foreach($font_color as $key=>$value) $tmp_fontcolor .= '<option value="'.$key.'" style="background-color:'.$key.';">'.$value.'</option>';
				$lists['couponcode_font_color'] = '<select name="couponcode_font_color" class="inputbox size="1">'.$tmp_fontcolor.'</select>';
				$lists['couponvalue_font_color'] = '<select name="couponvalue_font_color" class="inputbox size="1">'.$tmp_fontcolor.'</select>';
				$lists['expiration_font_color'] = '<select name="expiration_font_color" class="inputbox size="1">'.$tmp_fontcolor.'</select>';
				$lists['freetext1_font_color'] = '<select name="freetext1_font_color" class="inputbox size="1">'.$tmp_fontcolor.'</select>';
				$lists['freetext2_font_color'] = '<select name="freetext2_font_color" class="inputbox size="1">'.$tmp_fontcolor.'</select>';
				$lists['freetext3_font_color'] = '<select name="freetext3_font_color" class="inputbox size="1">'.$tmp_fontcolor.'</select>';
		
				foreach($row->imgplugin as $k=>$r1) {
					foreach($r1 as $k2=>$r2) {
						$lists[$k.'_'.$k2.'_align'] = JHTML::_('select.genericlist', $tmp_align, 'imgplugin['.$k.']['.$k2.'][align]', 'class="inputbox" size="1"', 'value', 'text', @$row->align );		
						$lists[$k.'_'.$k2.'_font'] = JHTML::_('select.genericlist', $tmp_font, 'imgplugin['.$k.']['.$k2.'][font]', 'class="inputbox" size="1"', 'value', 'text', @$row->font );		
						$lists[$k.'_'.$k2.'_font_color'] = '<select name="imgplugin['.$k.']['.$k2.'][font_color]" class="inputbox size="1">'.$tmp_fontcolor.'</select>';
					}
				}
				
				//printrx($row);
				//assign data to template
				$this->assignRef('row'      			, $row);
				$this->assignRef('lists'      			, $lists);
				$this->assignRef('editor'      			, $editor);
				$this->assignRef('AWOCOUPON_lang'			, $AWOCOUPON_lang);
				$this->assignRef('tagdesc'      		, $tagdesc);
				
				break;
			}

			default : {
				$db  		= JFactory::getDBO();
				//create the toolbar
				JToolBarHelper::makeDefault('defaultprofile');
				JToolBarHelper::divider();
				JToolBarHelper::addNew('ADDprofile');
				JToolBarHelper::editList('EDITprofile');
				JToolBarHelper::custom('duplicateprofile','copy','',JText::_( 'COM_AWOCOUPON_GBL_COPY' ));
				JToolBarHelper::divider();
				JToolBarHelper::deleteList( JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' ),'REMOVEprofile');
				JToolBarHelper::spacer();
				//get vars
				$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.profile.filter_order', 	'filter_order', 	'title', 'cmd' );
				$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.profile.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
				$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.profile.search', 			'search', 			'', 'string' );
				$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );


				//Get data from the model
				$rows      	= $this->get( 'Data');
				$pageNav 	= $this->get( 'Pagination' );

				
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

