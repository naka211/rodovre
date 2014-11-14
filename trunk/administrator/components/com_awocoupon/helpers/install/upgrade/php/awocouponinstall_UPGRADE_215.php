<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

function awocouponinstall_UPGRADE_215() {
	$db			= JFactory::getDBO();
		
	$coupons = $filtered_coupons = array();
	$db->setQuery('SELECT *,"" as usercount,"" as usergroupcount,"" as asset1count,"" as asset2count FROM #__awocoupon WHERE function_type2 IN ("product","category","manufacturer","vendor","shipping","parent")');
	$coupons = $db->loadObjectList('id');
	foreach($coupons as $coupon) {
		//if($coupon->function_type2!='shipping' && !empty($coupon->params)) continue;
		if(!empty($coupon->params)) {
			if($coupon->function_type2 == 'parent') continue;
			elseif($coupon->function_type2!='shipping') {
				if(strpos($coupon->params,'asset1_type')!==false) continue;
			}
		}
		$filtered_coupons[$coupon->id] = $coupon;
	}
		
	if(empty($filtered_coupons)) return;
		
	$ids = implode(',',array_keys($filtered_coupons));
		
	$db->setQuery('SELECT coupon_id,count(user_id) as cnt FROM #__awocoupon_user WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id');
	foreach($db->loadObjectList() as $tmp) $filtered_coupons[$tmp->coupon_id]->usercount = $tmp->cnt;

	$db->setQuery('SELECT coupon_id,count(shopper_group_id) as cnt FROM #__awocoupon_usergroup WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id');
	foreach($db->loadObjectList() as $tmp) $filtered_coupons[$tmp->coupon_id]->usergroupcount = $tmp->cnt;
				
	$db->setQuery('SELECT coupon_id,asset_type,count(asset_id) as cnt FROM #__awocoupon_asset1 WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id,asset_type');
	foreach($db->loadObjectList() as $tmp) $filtered_coupons[$tmp->coupon_id]->asset1count = $tmp->cnt;

	$db->setQuery('SELECT coupon_id,asset_type,count(asset_id) as cnt FROM #__awocoupon_asset2 WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id,asset_type');
	foreach($db->loadObjectList() as $tmp) $filtered_coupons[$tmp->coupon_id]->asset2count = $tmp->cnt;
		
	foreach($filtered_coupons as $coupon) {
			
		if(($coupon->function_type2=='product' || $coupon->function_type2=='category' || $coupon->function_type2=='manufacturer' || $coupon->function_type2=='vendor')
		&& empty($coupon->asset1count)) continue;
		
		//correct invalid data
		$params = empty($coupon->params) ? array() : (array)json_decode($coupon->params);

		if(empty($params['user_mode']) && (!empty($coupon_row->usercount) || !empty($coupon_row->usergroupcount))) $params['user_mode'] = 'include';
		if($coupon->function_type2=='product' || $coupon->function_type2=='category' || $coupon->function_type2=='manufacturer' || $coupon->function_type2=='vendor') {
			if(!empty($coupon->asset1count)) {
				$params['asset1_type'] = $coupon->function_type2;
				$params['asset1_mode'] = $coupon->function_type2_mode;
			}			
		}
		elseif($coupon->function_type2=='parent') {
			$params['asset1_type'] = 'coupon';
		}		
		elseif($coupon->function_type2=='shipping') {
			if(!empty($coupon->asset1count)) {
				$params['asset1_type'] = 'shipping';
				$params['asset1_mode'] = $coupon->function_type2_mode;
			}
			if(!empty($coupon->asset2count)) {
				$params['asset2_type'] = 'product';
				$params['asset2_mode'] = !empty($params['product_inc_exc']) ? $params['product_inc_exc'] : 'include';
			}
		}
		if(empty($params['min_value_type']) && !empty($coupon->min_value)) $params['min_value_type'] = 'overall';
		
		$db->setQuery('UPDATE #__awocoupon SET params=\''.json_encode($params).'\' WHERE id='.$coupon->id);
		$db->query();
		
	}
}