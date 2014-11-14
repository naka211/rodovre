<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 * Originally created by Stanislav Scholtz, RuposTel.com
 **/

defined('_JEXEC') or die( 'Restricted access' );

class AwocouponModelGiftcerts extends AwoCouponModel {
	
	function __construct() {
		parent::__construct();
		
		$this->_id = 0;
		$this->_code = awolibrary::dbEscape(JRequest::getVar('coupon_code', ''));
		if(!empty($this->_code)) {
			$this->_db->setQuery('SELECT id FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'" AND coupon_code="'.$this->_code.'"');
			$this->_id = (int)$this->_db->loadResult();
		}

	}


	public function getCoupons() {
	  
		$user = JFactory::getUser();
		if(empty($user->id)) return array();


		$codes = array(); 


			  
		$this->_db->setQuery('SELECT filename FROM #__awocoupon_image WHERE user_id='.(int)$user->id.' AND coupon_id='.(int)$this->_id);
		$filename = $this->_db->loadResult();
		if(empty($filename)) return array();
		
		$img = $this->getCouponImage($filename, $user->id);
		if (empty($img)) return array();
				   
		$file = str_replace('.php', '', $filename); 
		$url = JURI::base().'index.php?option=com_awocoupon&amp;format=image&amp;view=giftcerts&amp;file='.$file; 
		$img_code = '<img src="'.$url.'" alt="coupon" />';
		
		return array(
			'image'=>$img_code,
			'b64'=>$img, // comment line if you get out of memory error, the img is base64 encoded image
		);
	}
	// this is safe function where filename can be handled from URL parameters
	function getRawCouponImage($filename, $user_id=0) {
	  // security
		$filename = JFile::makeSafe($filename); 
		if (strpos($filename, '/')!==false) die('security alert sent to administrator'); 
		if (strpos($filename, '..')!==false) die('security alert sent to administrator'); 
		$fi = pathinfo($filename); 
		$filename = $fi['basename']; 
	  //echo $filename; die();
	  // end of security
	  
	  
		if (empty($user_id)) {
			$user = JFactory::getUser();
			$user_id = $user->id; 
		}
		if (empty($user_id)) return false;
		   
		  
		$fi = pathinfo($filename); 
		if (empty($fi['extension'])) return false;
		$type = $fi['extension']; 
		$full_filename = JPATH_SITE.'/media/com_awocoupon/customers/'.$user_id.'/'.$filename.'.php';

		if (!file_exists($full_filename)) return false;
		$fcontent = file_get_contents($full_filename); 
		$fcontent = str_replace(urldecode('%3c%3fphp+die()%3b+%3f%3e'), '', $fcontent); 
		return $fcontent; // base64_encoded content of the file
	}
	
	// returns string base64 data:uri to be used in src="here" 
	// input parameters: user_id,
	// $filename from codes [i] section of the awocoupon_giftcert_order table
	// internal function, don't provide URL parameters here !!!
	function getCouponImage($filename, $user_id=0) {
	  
		if (empty($user_id)) {
			$user = JFactory::getUser();
			$user_id = $user->id; 
		}
		if (empty($user_id)) return false;
		
		$filename = JFile::makeSafe($filename); 
		$filename = str_replace('.php', '', JFile::makeSafe($filename)); 
		$fi = pathinfo($filename); 
		if (empty($fi['extension'])) return false;
		$type = $fi['extension']; 
		
		$full_filename = JPATH_SITE.'/media/com_awocoupon/customers/'.$user_id.'/'.$filename.'.php';
		if (!file_exists($full_filename)) return false;
		
		$fcontent = file_get_contents($full_filename); 
		$fcontent = str_replace(urldecode('%3c%3fphp+die()%3b+%3f%3e'), '', $fcontent); 
		$arr = array(); 
		$arr['c'] = $fcontent;
		$arr['t'] = strtolower($type);
		return $arr;
	}
	
	
}
