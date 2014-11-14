<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class AwoCouponModelProfile extends AwoCouponModel {

	function __construct() {
		$this->_type = 'profile';
		parent::__construct();
	}


	function &getEntry() {
		parent::getEntry();
	
		//printrx($this->_entry);
		$this->_initEntry_helper();
		if(!empty($this->_entry->id)) {
		
			$params = JComponentHelper::getParams('com_languages');
			$lang = $params->get('administrator');
			$sql = 'SELECT text FROM #__awocoupon_lang_text WHERE elem_id='.(int)$this->_entry->email_subject_lang_id.' AND lang="'.$lang.'"';
			$this->_db->setQuery($sql);
			$this->_entry->email_subject = $this->_db->loadResult();
			$sql = 'SELECT text FROM #__awocoupon_lang_text WHERE elem_id='.(int)$this->_entry->email_body_lang_id.' AND lang="'.$lang.'"';
			$this->_db->setQuery($sql);
			$this->_entry->email_body = $this->_db->loadResult();
			$sql = 'SELECT text FROM #__awocoupon_lang_text WHERE elem_id='.(int)$this->_entry->voucher_text_lang_id.' AND lang="'.$lang.'"';
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadResult();
			if(!empty($tmp)) $this->_entry->voucher_text = $tmp;
			$sql = 'SELECT text FROM #__awocoupon_lang_text WHERE elem_id='.(int)$this->_entry->voucher_text_exp_lang_id.' AND lang="'.$lang.'"';
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadResult();
			if(!empty($tmp)) $this->_entry->voucher_exp_text = $tmp;
		
			if($this->_entry->message_type == 'html') {
				$tmp = unserialize($this->_entry->coupon_code_config);
				$this->_entry->couponcode_align = $tmp['align'];
				$this->_entry->couponcode_padding = $tmp['pad'];
				$this->_entry->couponcode_y = $tmp['y'];
				$this->_entry->couponcode_font = $tmp['font'];
				$this->_entry->couponcode_font_size = $tmp['size'];
				$this->_entry->couponcode_font_color = $tmp['color'];
				
				$tmp = unserialize($this->_entry->coupon_value_config);
				$this->_entry->couponvalue_align = $tmp['align'];
				$this->_entry->couponvalue_padding = $tmp['pad'];
				$this->_entry->couponvalue_y = $tmp['y'];
				$this->_entry->couponvalue_font = $tmp['font'];
				$this->_entry->couponvalue_font_size = $tmp['size'];
				$this->_entry->couponvalue_font_color = $tmp['color'];
				
				if(!empty($this->_entry->expiration_config)) {
					$tmp = unserialize($this->_entry->expiration_config);
					$this->_entry->expiration_text = $tmp['text'];
					$this->_entry->expiration_align = $tmp['align'];
					$this->_entry->expiration_padding = $tmp['pad'];
					$this->_entry->expiration_y = $tmp['y'];
					$this->_entry->expiration_font = $tmp['font'];
					$this->_entry->expiration_font_size = $tmp['size'];
					$this->_entry->expiration_font_color = $tmp['color'];
				}
				if(!empty($this->_entry->freetext1_config)) {
					$tmp = unserialize($this->_entry->freetext1_config);
					$this->_entry->freetext1_text = $tmp['text'];
					$this->_entry->freetext1_align = $tmp['align'];
					$this->_entry->freetext1_padding = $tmp['pad'];
					$this->_entry->freetext1_y = $tmp['y'];
					$this->_entry->freetext1_font = $tmp['font'];
					$this->_entry->freetext1_font_size = $tmp['size'];
					$this->_entry->freetext1_font_color = $tmp['color'];
				}
				if(!empty($this->_entry->freetext2_config)) {
					$tmp = unserialize($this->_entry->freetext2_config);
					$this->_entry->freetext2_text = $tmp['text'];
					$this->_entry->freetext2_align = $tmp['align'];
					$this->_entry->freetext2_padding = $tmp['pad'];
					$this->_entry->freetext2_y = $tmp['y'];
					$this->_entry->freetext2_font = $tmp['font'];
					$this->_entry->freetext2_font_size = $tmp['size'];
					$this->_entry->freetext2_font_color = $tmp['color'];
				}
				if(!empty($this->_entry->freetext3_config)) {
					$tmp = unserialize($this->_entry->freetext3_config);
					$this->_entry->freetext3_text = $tmp['text'];
					$this->_entry->freetext3_align = $tmp['align'];
					$this->_entry->freetext3_padding = $tmp['pad'];
					$this->_entry->freetext3_y = $tmp['y'];
					$this->_entry->freetext3_font = $tmp['font'];
					$this->_entry->freetext3_font_size = $tmp['size'];
					$this->_entry->freetext3_font_color = $tmp['color'];
				}
				
				JPluginHelper::importPlugin('awocoupon');
				$dispatcher = JDispatcher::getInstance();
				$rtn = $dispatcher->trigger('profileOnBeforeEntryView', array($this->_entry->id));
				foreach($rtn as $items) {
					foreach($items as $k2=>$row) {
						if(empty($row->key)) continue;
						$this->_entry->imgplugin[$row->key][$k2] = $row;
					}
				}
			}
		}

		return $this->_entry;
	}
	
	
	function _initEntry_helper() {
		$this->_entry->email_subject = '';
		$this->_entry->email_body = '';
		$this->_entry->voucher_text_lang_id = 0;
		$this->_entry->voucher_text_exp_lang_id = 0;
		$this->_entry->voucher_text = JText::_('COM_AWOCOUPON_GC_GIFTCERT').': {voucher}<br />'.JText::_('COM_AWOCOUPON_CP_VALUE').': {price}{expiration_text}<br /><br />';
		$this->_entry->voucher_exp_text = '<br />'.JText::_('COM_AWOCOUPON_CP_EXPIRATION').': {expiration}';
		
		$this->_entry->couponcode_align = '';
		$this->_entry->couponcode_padding = '';
		$this->_entry->couponcode_y = '';
		$this->_entry->couponcode_font = '';
		$this->_entry->couponcode_font_size = '';
		$this->_entry->couponcode_font_color = '';
		
		$this->_entry->couponvalue_align = '';
		$this->_entry->couponvalue_padding = '';
		$this->_entry->couponvalue_y = '';
		$this->_entry->couponvalue_font = '';
		$this->_entry->couponvalue_font_size = '';
		$this->_entry->couponvalue_font_color = '';

		$this->_entry->expiration_text = '';
		$this->_entry->expiration_align = '';
		$this->_entry->expiration_padding = '';
		$this->_entry->expiration_y = '';
		$this->_entry->expiration_font = '';
		$this->_entry->expiration_font_size = '';
		$this->_entry->expiration_font_color = '';
		
		$this->_entry->freetext1_text = '';
		$this->_entry->freetext1_align = '';
		$this->_entry->freetext1_padding = '';
		$this->_entry->freetext1_y = '';
		$this->_entry->freetext1_font = '';
		$this->_entry->freetext1_font_size = '';
		$this->_entry->freetext1_font_color = '';
		
		$this->_entry->freetext2_text = '';
		$this->_entry->freetext2_align = '';
		$this->_entry->freetext2_padding = '';
		$this->_entry->freetext2_y = '';
		$this->_entry->freetext2_font = '';
		$this->_entry->freetext2_font_size = '';
		$this->_entry->freetext2_font_color = '';
		
		$this->_entry->freetext3_text = '';
		$this->_entry->freetext3_align = '';
		$this->_entry->freetext3_padding = '';
		$this->_entry->freetext3_y = '';
		$this->_entry->freetext3_font = '';
		$this->_entry->freetext3_font_size = '';
		$this->_entry->freetext3_font_color = '';
		
		$this->_entry->imgplugin = array();
		
		JPluginHelper::importPlugin('awocoupon');
		$dispatcher = JDispatcher::getInstance();
		$rtn = $dispatcher->trigger('profileOnInitializeEntryView');
		foreach($rtn as $items) {
			foreach($items as $k2=>$row) {
				if(empty($row->key)) continue;
				$this->_entry->imgplugin[$row->key][$k2] = $row;
			}
		}
		
	}


	
	function storeEach($data) {
		$errors = array();
		
		
		// set null fields
		if(empty($data['from_name'])) $data['from_name'] = null;
		if(empty($data['from_email'])) $data['from_email'] = null;
		if(empty($data['bcc_admin'])) $data['bcc_admin'] = null;
		if(empty($data['email_subject'])) $data['email_subject'] = null;
		
		$data['is_default'] = null;
		

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		$row 		= JTable::getInstance('profile', 'Table');
		$user		= JFactory::getUser();
		
		// bind it to the table
		if (!$row->bind($data)) {
			$errors[] = $this->_db->getErrorMsg();
		}


		// sanitise fields
		
		$row->id 			= (int) $row->id;
		if(!empty($row->id)) {
			$sql = 'SELECT is_default,email_subject_lang_id,email_body_lang_id,voucher_text_lang_id,voucher_text_exp_lang_id FROM #__awocoupon_profile WHERE id='.$row->id;
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadObject();
			if(!empty($tmp)) {
				$row->is_default = $tmp->is_default;
				$row->email_subject_lang_id = $tmp->email_subject_lang_id;
				$row->email_body_lang_id = $tmp->email_body_lang_id;
				$row->voucher_text_lang_id = $tmp->voucher_text_lang_id;
				$row->voucher_text_exp_lang_id = $tmp->voucher_text_exp_lang_id;
			}
			
		}

		// Make sure the data is valid
		if (!$row->check()) {
			foreach($row->getErrors() as $err) $errors[] = $err;
		}

		// take a break and return if there are any errors
		if(!empty($errors)) return $errors;
		
		
		
		if($row->message_type == 'text') {
			$row->image = null;
			$row->coupon_code_config = null;
			$row->coupon_value_config = null;
			$row->expiration_config = null;
			$row->freetext1_config = null;
			$row->freetext2_config = null;
			$row->freetext3_config = null;
		} else {
			
			if(empty($row->image)) $row->image = null;
			else {
				$row->coupon_code_config = serialize(array(
					'align'=>$data['couponcode_align'],
					'pad'=>$data['couponcode_align']=='C' ? '' : $data['couponcode_padding'],
					'y'=>$data['couponcode_y'],
					'font'=>$data['couponcode_font'],
					'size'=>$data['couponcode_font_size'],
					'color'=>$data['couponcode_font_color'],
				));
				
				if($data['couponcode_align']!='C' && (empty($data['couponcode_padding']) || !ctype_digit($data['couponcode_padding']))) $errors[] = JText::_('COM_AWOCOUPON_CP_COUPON_CODE').'=>'.JText::_('COM_AWOCOUPON_PF_PADDING').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				if(empty($data['couponcode_y']) || !ctype_digit($data['couponcode_y'])) $errors[] = JText::_('COM_AWOCOUPON_CP_COUPON_CODE').'=>'.JText::_('COM_AWOCOUPON_PF_YAXIS').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				if(empty($data['couponcode_font'])) $errors[] = JText::_('COM_AWOCOUPON_CP_COUPON_CODE').'=>'.JText::_('COM_AWOCOUPON_PF_FONT').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
				if(empty($data['couponcode_font_size']) || !ctype_digit($data['couponcode_font_size'])) $errors[] = JText::_('COM_AWOCOUPON_CP_COUPON_CODE').'=>'.JText::_('COM_AWOCOUPON_PF_FONT_SIZE').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');

				$row->coupon_value_config = serialize(array(
					'align'=>$data['couponvalue_align'],
					'pad'=>$data['couponvalue_align']=='C' ? '' : $data['couponvalue_padding'],
					'y'=>$data['couponvalue_y'],
					'font'=>$data['couponvalue_font'],
					'size'=>$data['couponvalue_font_size'],
					'color'=>$data['couponvalue_font_color'],
				));
				if($data['couponvalue_align']!='C' && (empty($data['couponvalue_padding']) || !ctype_digit($data['couponvalue_padding']))) $errors[] = JText::_('COM_AWOCOUPON_CP_VALUE').'=>'.JText::_('COM_AWOCOUPON_PF_PADDING').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				if(empty($data['couponvalue_y']) || !ctype_digit($data['couponvalue_y'])) $errors[] = JText::_('COM_AWOCOUPON_CP_VALUE').'=>'.JText::_('COM_AWOCOUPON_PF_YAXIS').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				if(empty($data['couponvalue_font'])) $errors[] = JText::_('COM_AWOCOUPON_CP_VALUE').'=>'.JText::_('COM_AWOCOUPON_PF_FONT').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
				if(empty($data['couponvalue_font_size']) || !ctype_digit($data['couponvalue_font_size'])) $errors[] = JText::_('COM_AWOCOUPON_CP_VALUE').'=>'.JText::_('COM_AWOCOUPON_PF_FONT_SIZE').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					
				if(empty($data['expiration_text'])) $row->expiration_config = null;
				else {
					$row->expiration_config = serialize(array(
						'text'=>$data['expiration_text'],
						'align'=>$data['expiration_align'],
						'pad'=>$data['expiration_align']=='C' ? '' : $data['expiration_padding'],
						'y'=>$data['expiration_y'],
						'font'=>$data['expiration_font'],
						'size'=>$data['expiration_font_size'],
						'color'=>$data['expiration_font_color'],
					));
					if($data['expiration_align']!='C' && (empty($data['expiration_padding']) || !ctype_digit($data['expiration_padding']))) $errors[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').'=>'.JText::_('COM_AWOCOUPON_PF_PADDING').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['expiration_y']) || !ctype_digit($data['expiration_y'])) $errors[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').'=>'.JText::_('COM_AWOCOUPON_PF_YAXIS').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['expiration_font'])) $errors[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').'=>'.JText::_('COM_AWOCOUPON_PF_FONT').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
					if(empty($data['expiration_font_size']) || !ctype_digit($data['expiration_font_size'])) $errors[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').'=>'.JText::_('COM_AWOCOUPON_PF_FONT_SIZE').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				}

				if(empty($data['freetext1_text'])) $row->freetext1_config = null;
				else {
					$row->freetext1_config = serialize(array(
						'text'=>$data['freetext1_text'],
						'align'=>$data['freetext1_align'],
						'pad'=>$data['freetext1_align']=='C' ? '' : $data['freetext1_padding'],
						'y'=>$data['freetext1_y'],
						'font'=>$data['freetext1_font'],
						'size'=>$data['freetext1_font_size'],
						'color'=>$data['freetext1_font_color'],
					));
					if($data['freetext1_align']!='C' && (empty($data['freetext1_padding']) || !ctype_digit($data['freetext1_padding']))) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 1=>'.JText::_('COM_AWOCOUPON_PF_PADDING').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['freetext1_y']) || !ctype_digit($data['freetext1_y'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 1=>'.JText::_('COM_AWOCOUPON_PF_YAXIS').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['freetext1_font'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 1=>'.JText::_('COM_AWOCOUPON_PF_FONT').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
					if(empty($data['freetext1_font_size']) || !ctype_digit($data['freetext1_font_size'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 1=>'.JText::_('COM_AWOCOUPON_PF_FONT_SIZE').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				}
				
				if(empty($data['freetext2_text'])) $row->freetext2_config = null;
				else {
					$row->freetext2_config = serialize(array(
						'text'=>$data['freetext2_text'],
						'align'=>$data['freetext2_align'],
						'pad'=>$data['freetext2_align']=='C' ? '' : $data['freetext2_padding'],
						'y'=>$data['freetext2_y'],
						'font'=>$data['freetext2_font'],
						'size'=>$data['freetext2_font_size'],
						'color'=>$data['freetext2_font_color'],
					));
					if($data['freetext2_align']!='C' && (empty($data['freetext2_padding']) || !ctype_digit($data['freetext2_padding']))) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 2=>'.JText::_('COM_AWOCOUPON_PF_PADDING').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['freetext2_y']) || !ctype_digit($data['freetext2_y'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 2=>'.JText::_('COM_AWOCOUPON_PF_YAXIS').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['freetext2_font'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 2=>'.JText::_('COM_AWOCOUPON_PF_FONT').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
					if(empty($data['freetext2_font_size']) || !ctype_digit($data['freetext2_font_size'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 2=>'.JText::_('COM_AWOCOUPON_PF_FONT_SIZE').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				}
					
				if(empty($data['freetext3_text'])) $row->freetext3_config = null;
				else {
					$row->freetext3_config = serialize(array(
						'text'=>$data['freetext3_text'],
						'align'=>$data['freetext3_align'],
						'pad'=>$data['freetext3_align']=='C' ? '' : $data['freetext3_padding'],
						'y'=>$data['freetext3_y'],
						'font'=>$data['freetext3_font'],
						'size'=>$data['freetext3_font_size'],
						'color'=>$data['freetext3_font_color'],
					));
					if($data['freetext3_align']!='C' && (empty($data['freetext3_padding']) || !ctype_digit($data['freetext3_padding']))) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 3=>'.JText::_('COM_AWOCOUPON_PF_PADDING').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['freetext3_y']) || !ctype_digit($data['freetext3_y'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 3=>'.JText::_('COM_AWOCOUPON_PF_YAXIS').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
					if(empty($data['freetext3_font'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 3=>'.JText::_('COM_AWOCOUPON_PF_FONT').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
					if(empty($data['freetext3_font_size']) || !ctype_digit($data['freetext3_font_size'])) $errors[] = JText::_('COM_AWOCOUPON_PF_FREE_TEXT').' 3=>'.JText::_('COM_AWOCOUPON_PF_FONT_SIZE').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
				}
			
			
				if(!empty($data['imgplugin'])) {
					foreach($data['imgplugin'] as $pluginitems) {
						foreach($pluginitems as $pluginrow) {
							if(!empty($pluginrow['text'])) {
								if($pluginrow['align']!='C' && (empty($pluginrow['padding']) || !ctype_digit($pluginrow['padding']))) $errors[] = $pluginrow['title'].'=>'.JText::_('COM_AWOCOUPON_PF_PADDING').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
								if(empty($pluginrow['y']) || !ctype_digit($pluginrow['y'])) $errors[] = $pluginrow['title'].'=>'.JText::_('COM_AWOCOUPON_PF_YAXIS').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
								if(empty($pluginrow['is_ignore_font']) && empty($pluginrow['font'])) $errors[] = $pluginrow['title'].'=>'.JText::_('COM_AWOCOUPON_PF_FONT').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
								if(empty($pluginrow['is_ignore_font_size']) && (empty($pluginrow['font_size']) || !ctype_digit($pluginrow['font_size']))) $errors[] = $pluginrow['title'].'=>'.JText::_('COM_AWOCOUPON_PF_FONT_SIZE').': '.JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED');
							}
						}
					}
				}
				
			
			
			
			}
		}
		
		
		if(!empty($errors)) return $errors;
		
		// Store the entry to the database
		if (!$row->store(true)) {
			$errors[] = $this->_db->stderr();
			return $errors;
		}
		
		
		JPluginHelper::importPlugin('awocoupon');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('profileOnAfterUpdate', array($row,$data));
		

		if(!empty($row->id)) {
		
			$elem_id = awoLibrary::setLangData($row->email_subject_lang_id,$data['email_subject']);
			if(!empty($elem_id)) $row->email_subject_lang_id = $elem_id;
		
			$elem_id = awoLibrary::setLangData($row->email_body_lang_id,$row->message_type == 'html' ? JRequest::getVar( 'email_html', '', 'post', 'string', JREQUEST_ALLOWRAW ) : $data['email_body']);
			if(!empty($elem_id)) $row->email_body_lang_id = $elem_id;
			
			$elem_id = awoLibrary::setLangData($row->voucher_text_lang_id,$row->message_type == 'html' ? JRequest::getVar( 'voucher_text', '', 'post', 'string', JREQUEST_ALLOWRAW ) : $data['voucher_text']);
			if(!empty($elem_id)) $row->voucher_text_lang_id = $elem_id;
		
			$elem_id = awoLibrary::setLangData($row->voucher_text_exp_lang_id,$row->message_type == 'html' ? JRequest::getVar( 'voucher_exp_text', '', 'post', 'string', JREQUEST_ALLOWRAW ) : $data['voucher_exp_text']);
			if(!empty($elem_id)) $row->voucher_text_exp_lang_id = $elem_id;
		
			$row->store(true);
		
		}
		
				
		$this->_entry	=& $row;
		
		return;
	}
	
	
	
	
	
	
	function getData() {
		parent::getData();
		
		if (!empty($this->_data)) {
		
		
			$params = JComponentHelper::getParams('com_languages');
			$lang = $params->get('administrator');
			
			$ids = array();
			$ptr = null;
			foreach($this->_data as $i=>$row) {
				$this->_data[$i]->email_subject = '';
				
				$ids[] = $row->id;
				$ptr[$row->id]['email_subject'] = &$this->_data[$i]->email_subject;
			}
			
			
			
			$sql = 'SELECT elem_id,p.id as profile_id,text FROM #__awocoupon_lang_text l
					  JOIN #__awocoupon_profile p ON p.email_subject_lang_id=l.elem_id
					 WHERE lang="'.$lang.'" AND p.id IN ('.implode(',',$ids).')';
			$this->_db->setQuery($sql);
			foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->profile_id]['email_subject'] = $tmp->text;
			
		}
		return $this->_data;
	}
	
	
	
	function _buildQuery() {
		// Get the WHERE, and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$sql = 'SELECT *
				   FROM #__awocoupon_profile
				   '.$where.' '.$orderby;
		return $sql;
	}
	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.profile.filter_order', 	'filter_order', 	'title', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.profile.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}
	function _buildContentWhere() {
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.profile.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
	
		$where = array();
		

		if ($search) $where[] = ' LOWER(title) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	function makedefault($cid = array()) {
		$cid = current($cid);
		
		$sql = 'SELECT id FROM #__awocoupon_profile WHERE id='.(int)$cid;
		$this->_db->setQuery($sql);
		$tmp = $this->_db->loadResult();
		if(!empty($tmp)) {
			$sql = 'UPDATE #__awocoupon_profile SET is_default=NULL';
			$this->_db->setQuery($sql);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			$sql = 'UPDATE #__awocoupon_profile SET is_default=1 WHERE id='.(int)$cid;
			$this->_db->setQuery($sql);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

		}
		return $cid;
	}
	

	
	function duplicate($cids) {		
		$cid = (int)current($cids);

		$sql = 'INSERT INTO #__awocoupon_profile (title,is_default,from_name,from_email,bcc_admin,message_type,image,coupon_code_config,coupon_value_config,expiration_config,freetext1_config,freetext2_config,freetext3_config)
					SELECT title,NULL,from_name,from_email,bcc_admin,message_type,image,coupon_code_config,coupon_value_config,expiration_config,freetext1_config,freetext2_config,freetext3_config
					  FROM #__awocoupon_profile 
					 WHERE id='.$cid;
		$this->_db->setQuery($sql);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}
		$profile_id = (int)$this->_db->insertid();
		
		if(!empty($profile_id)) {
			$this->_db->setQuery('SELECT email_subject_lang_id,email_body_lang_id,voucher_text_lang_id,voucher_text_exp_lang_id FROM #__awocoupon_profile WHERE id='.$cid);
			$tmp = $this->_db->loadObject();
			$vars = array(
				'email_subject_lang_id'=>(int)$tmp->email_subject_lang_id,
				'email_body_lang_id'=>(int)$tmp->email_body_lang_id,
				'voucher_text_lang_id'=>(int)$tmp->voucher_text_lang_id,
				'voucher_text_exp_lang_id'=>(int)$tmp->voucher_text_exp_lang_id,
			);

			foreach($vars as $elem=>$old_id) {
				$this->_db->setQuery('SELECT MAX(elem_id) FROM #__awocoupon_lang_text');
				$new_id = (int)$this->_db->loadResult()+1;
			
				$sql = 'INSERT INTO #__awocoupon_lang_text (elem_id,lang,text) 
							SELECT '.$new_id.',lang,text FROM #__awocoupon_lang_text WHERE elem_id='.$old_id;
				$this->_db->setQuery($sql);
				$this->_db->query();
				$affectedRows = $this->_db->getAffectedRows();
				if(!empty($affectedRows)) {
					$this->_db->setQuery('UPDATE #__awocoupon_profile SET '.$elem.'='.$new_id.' WHERE id='.$profile_id);
					$this->_db->query();
				}
			}		
		
		}
		
				
		

		return true;
	}
	
	
	
	
	
	function getimages() {
		$imagedd = array();
		$accepted_formats = array('png','jpg');
		//foreach(glob(strtolower(com_awocoupon_GIFTCERT_IMAGES.'/*.png')) as $img) {
		foreach(glob(com_awocoupon_GIFTCERT_IMAGES.'/*.*') as $img) {
			$parts = pathinfo($img);
			if(in_array(strtolower($parts['extension']),$accepted_formats)) $imagedd[$parts['basename']] = ucwords($parts['filename']);
		}
		return $imagedd;
	}
	
	function getfonts() {
		$fontdd = array();
		foreach(glob(com_awocoupon_GIFTCERT_FONTS.'/*.[tT][tT][fF]') as $font) {
			$font = basename($font);
			$fontdd[$font] = ucwords(substr($font,0,-4));
		}
		return $fontdd;
	}
	
	function getfontcolor() {
		return array(
			'#F0F8FF'=>'ALICEBLUE',
			'#FAEBD7'=>'ANTIQUEWHITE',
			'#00FFFF'=>'AQUA',
			'#7FFFD4'=>'AQUAMARINE',
			'#F0FFFF'=>'AZURE',
			'#F5F5DC'=>'BEIGE',
			'#FFE4C4'=>'BISQUE',
			'#000000'=>'BLACK',
			'#FFEBCD'=>'BLANCHEDALMOND',
			'#0000FF'=>'BLUE',
			'#8A2BE2'=>'BLUEVIOLET',
			'#A52A2A'=>'BROWN',
			'#DEB887'=>'BURLYWOOD',
			'#5F9EA0'=>'CADETBLUE',
			'#7FFF00'=>'CHARTREUSE',
			'#D2691E'=>'CHOCOLATE',
			'#FF7F50'=>'CORAL',
			'#6495ED'=>'CORNFLOWERBLUE',
			'#FFF8DC'=>'CORNSILK',
			'#DC143C'=>'CRIMSON',
			'#00FFFF'=>'CYAN',
			'#00008B'=>'DARKBLUE',
			'#008B8B'=>'DARKCYAN',
			'#B8860B'=>'DARKGOLDENROD',
			'#A9A9A9'=>'DARKGRAY',
			'#006400'=>'DARKGREEN',
			'#BDB76B'=>'DARKKHAKI',
			'#8B008B'=>'DARKMAGENTA',
			'#556B2F'=>'DARKOLIVEGREEN',
			'#FF8C00'=>'DARKORANGE',
			'#9932CC'=>'DARKORCHID',
			'#8B0000'=>'DARKRED',
			'#E9967A'=>'DARKSALMON',
			'#8FBC8F'=>'DARKSEAGREEN',
			'#483D8B'=>'DARKSLATEBLUE',
			'#2F4F4F'=>'DARKSLATEGRAY',
			'#00CED1'=>'DARKTURQUOISE',
			'#9400D3'=>'DARKVIOLET',
			'#FF1493'=>'DEEPPINK',
			'#00BFFF'=>'DEEPSKYBLUE',
			'#696969'=>'DIMGRAY',
			'#1E90FF'=>'DODGERBLUE',
			'#B22222'=>'FIREBRICK',
			'#FFFAF0'=>'FLORALWHITE',
			'#228B22'=>'FORESTGREEN',
			'#FF00FF'=>'FUCHSIA',
			'#DCDCDC'=>'GAINSBORO',
			'#F8F8FF'=>'GHOSTWHITE',
			'#FFD700'=>'GOLD',
			'#DAA520'=>'GOLDENROD',
			'#BEBEBE'=>'GRAY',
			'#008000'=>'GREEN',
			'#ADFF2F'=>'GREENYELLOW',
			'#F0FFF0'=>'HONEYDEW',
			'#FF69B4'=>'HOTPINK',
			'#CD5C5C'=>'INDIANRED',
			'#4B0082'=>'INDIGO',
			'#FFFFF0'=>'IVORY',
			'#F0D58C'=>'KHAKI',
			'#E6E6FA'=>'LAVENDER',
			'#FFF0F5'=>'LAVENDERBLUSH',
			'#7CFC00'=>'LAWNGREEN',
			'#FFFACD'=>'LEMONCHIFFON',
			'#ADD8E6'=>'LIGHTBLUE',
			'#F08080'=>'LIGHTCORAL',
			'#E0FFFF'=>'LIGHTCYAN',
			'#FAFAD2'=>'LIGHTGOLDENRODYELLOW',
			'#90EE90'=>'LIGHTGREEN',
			'#D3D3D3'=>'LIGHTGREY',
			'#FFB6C1'=>'LIGHTPINK',
			'#FFA07A'=>'LIGHTSALMON',
			'#20B2AA'=>'LIGHTSEAGREEN',
			'#87CEFA'=>'LIGHTSKYBLUE',
			'#778899'=>'LIGHTSLATEGRAY',
			'#B0C4DE'=>'LIGHTSTEELBLUE',
			'#FFFFE0'=>'LIGHTYELLOW',
			'#00FF00'=>'LIME',
			'#32CD32'=>'LIMEGREEN',
			'#FAF0E6'=>'LINEN',
			'#FF00FF'=>'MAGENTA',
			'#800000'=>'MAROON',
			'#66CDAA'=>'MEDIUMAQUAMARINE',
			'#0000CD'=>'MEDIUMBLUE',
			'#BA55D3'=>'MEDIUMORCHID',
			'#9370DB'=>'MEDIUMPURPLE',
			'#3CB371'=>'MEDIUMSEAGREEN',
			'#7B68EE'=>'MEDIUMSLATEBLUE',
			'#00FA9A'=>'MEDIUMSPRINGGREEN',
			'#48D1CC'=>'MEDIUMTURQUOISE',
			'#C71585'=>'MEDIUMVIOLETRED',
			'#191970'=>'MIDNIGHTBLUE',
			'#F5FFFA'=>'MINTCREAM',
			'#FFE4E1'=>'MISTYROSE',
			'#FFE4B5'=>'MOCCASIN',
			'#FFDEAD'=>'NAVAJOWHITE',
			'#000080'=>'NAVY',
			'#FDF5E6'=>'OLDLACE',
			'#808000'=>'OLIVE',
			'#6B8E23'=>'OLIVEDRAB',
			'#FFA500'=>'ORANGE',
			'#FF4500'=>'ORANGERED',
			'#DA70D6'=>'>ORCHID',
			'#EEE8AA'=>'PALEGOLDENROD',
			'#98FB98'=>'PALEGREEN',
			'#AFEEEE'=>'PALETURQUOISE',
			'#DB7093'=>'PALEVIOLETRED',
			'#FFEFD5'=>'PAPAYAWHIP',
			'#FFDAB9'=>'PEACHPUFF',
			'#CD853F'=>'PERU',
			'#FFC0CB'=>'PINK',
			'#DDA0DD'=>'PLUM',
			'#B0E0E6'=>'POWDERBLUE',
			'#800080'=>'PURPLE',
			'#FF0000'=>'RED',
			'#BC8F8F'=>'ROSYBROWN',
			'#4169E1'=>'ROYALBLUE',
			'#8B4513'=>'SADDLEBROWN',
			'#FA8072'=>'SALMON',
			'#F4A460'=>'SANDYBROWN',
			'#2E8B57'=>'SEAGREEN',
			'#FFF5EE'=>'SEASHELL',
			'#A0522D'=>'SIENNA',
			'#C0C0C0'=>'SILVER',
			'#87CEEB'=>'SKYBLUE',
			'#6A5ACD'=>'SLATEBLUE',
			'#708090'=>'SLATEGRAY',
			'#FFFAFA'=>'SNOW',
			'#00FF7F'=>'SPRINGGREEN',
			'#4682B4'=>'STEELBLUE',
			'#D2B48C'=>'TAN',
			'#008080'=>'TEAL',
			'#D8BFD8'=>'THISTLE',
			'#FF6347'=>'TOMATO',
			'#40E0D0'=>'TURQUOISE',
			'#EE82EE'=>'VIOLET',
			'#F5DEB3'=>'WHEAT',
			'#FFFFFF'=>'WHITE',
			'#F5F5F5'=>'WHITESMOKE',
			'#FFFF00'=>'YELLOW',
			'#9ACD32'=>'YELLOWGREEN',
		);
	}

}

