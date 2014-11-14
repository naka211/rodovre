<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class awoAutoGenerate  {

	static function getCouponTemplates($estore='virtuemart') {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
		$db = JFactory::getDBO();
		$sql = 'SELECT id,coupon_code FROM #__awocoupon WHERE estore="'.awolibrary::dbEscape($estore).'" AND published=-2 ORDER BY coupon_code,id';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	static function generateCoupon($coupon_id,$coupon_code=null,$expiration=null,$override_user=null,$estore='virtuemart') {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';

		$db = JFactory::getDBO();
		$coupon_id = (int)$coupon_id;
		if(!is_null($override_user)) $override_user = trim($override_user);
		if(!is_null($expiration)) $expiration = trim($expiration);
		
		$sql = 'SELECT * FROM #__awocoupon WHERE id='.$coupon_id;
		$db->setQuery($sql);
		$crow = $db->loadObject();
		if(empty($crow)) return false;  // template coupon does not exist
		
		
		if(empty($coupon_code)) $coupon_code = awoLibrary::generate_coupon_code($estore);
		elseif(awoLibrary::isCodeUsed($coupon_code,$estore)) $coupon_code = awoLibrary::generate_coupon_code($estore);

		$db_expiration = !empty($crow->expiration) ? '"'.$crow->expiration.'"' : 'NULL';
		if($crow->function_type!='parent' && !empty($expiration) && ctype_digit($expiration)) {
			$db_expiration = '"'.date('Y-m-d 23:59:59',time()+(86400*(int)$expiration)).'"';
		}

		
		$passcode = substr( md5((string)time().rand(1,1000).$coupon_code ), 0, 6);

		$sql = 'INSERT INTO #__awocoupon (	estore,template_id,coupon_code,passcode,parent_type,coupon_value_type,coupon_value,coupon_value_def,
										function_type,num_of_uses_type,num_of_uses,
										min_value,discount_type,user_type,
										startdate,expiration,exclude_special,exclude_giftcert,
										note,params,published)
				VALUES ("'.awolibrary::dbEscape($estore).'",
						'.$coupon_id.',
						"'.$coupon_code.'",
						"'.$passcode.'",
						'.(!empty($crow->parent_type) ? '"'.$crow->parent_type.'"' : 'NULL').',
						'.(!empty($crow->coupon_value_type) ? '"'.$crow->coupon_value_type.'"' : 'NULL').',
						'.(!empty($crow->coupon_value) ? $crow->coupon_value : 'NULL').',
						'.(!empty($crow->coupon_value_def) ? '"'.$crow->coupon_value_def.'"' : 'NULL').',
						"'.$crow->function_type.'",
						'.(!empty($crow->num_of_uses_type) ? '"'.$crow->num_of_uses_type.'"' : 'NULL').',
						'.(!empty($crow->num_of_uses) ? $crow->num_of_uses : 'NULL').',
						'.(!empty($crow->min_value) ? $crow->min_value : 'NULL').',
						'.(!empty($crow->discount_type) ? '"'.$crow->discount_type.'"' : 'NULL').',
						'.(!empty($crow->user_type) ? '"'.$crow->user_type.'"' : 'NULL').',
						'.(!empty($crow->startdate) ? '"'.$crow->startdate.'"' : 'NULL').',
						'.$db_expiration.',
						'.(!empty($crow->exclude_special) ? $crow->exclude_special : 'NULL').',
						'.(!empty($crow->exclude_giftcert) ? $crow->exclude_giftcert : 'NULL').',
						'.(!empty($crow->note) ? '"'.$crow->note.'"' : 'NULL').',
						'.(!empty($crow->params) ? $db->Quote( $crow->params) : 'NULL').',
						1
					)';
		$db->setQuery($sql);
		$db->query();
		$gen_coupon_id = $db->insertid();
		
		if($crow->function_type!='parent' && !empty($override_user) && ctype_digit(trim($override_user))) {
			$sql = 'INSERT INTO #__awocoupon_user ( coupon_id,user_id ) VALUES ( '.$gen_coupon_id.','.$override_user.' )';
			$db->setQuery($sql);
			$db->query();
		} else {
			self::populateTable(1,$coupon_id,$gen_coupon_id,'awocoupon_user','user_id');
			self::populateTable(1,$coupon_id,$gen_coupon_id,'awocoupon_usergroup','shopper_group_id');
		}

		self::populateTable(3,$coupon_id,$gen_coupon_id,'awocoupon_asset1');
		self::populateTable(3,$coupon_id,$gen_coupon_id,'awocoupon_asset2');
		
		$obj = new stdClass();
		$obj->coupon_id = $gen_coupon_id;
		$obj->coupon_code = $coupon_code;
		return $obj;
	}
	
	static private function populateTable($type,$coupon_id,$gen_coupon_id,$table,$column1='') {
		$db = JFactory::getDBO();
		$insert_str = '';

		if($type==1) {
			$sql = 'INSERT INTO #__'.$table.' (coupon_id,'.$column1.') 
						SELECT '.$gen_coupon_id.','.$column1.' FROM #__'.$table.' WHERE coupon_id='.$coupon_id;
			$db->setQuery($sql);
			$db->query();
		}
		elseif($type==3) {
			$sql = 'INSERT INTO #__'.$table.' (coupon_id,asset_type,asset_id,order_by) 
						SELECT '.$gen_coupon_id.',asset_type,asset_id,order_by FROM #__'.$table.' WHERE coupon_id='.$coupon_id;
			$db->setQuery($sql);
			$db->query();
		}
		
	}
	

}
