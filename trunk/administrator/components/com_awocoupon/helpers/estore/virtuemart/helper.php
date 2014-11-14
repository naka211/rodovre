<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

//if (!class_exists( 'VmConfig' )) {
//	require JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php';
//	VmConfig::loadConfig();
	//$vmconfig = VmConfig(); 
	//$vmconfig->loadConfig();
//}


class AwocouponVirtuemartHelper {

	static function isInstalled() { return file_exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.cfg') ? true : false; }

	static function getAwoCouponPluginFolder() { return 'vmcoupon,vmpayment,vmshipment'; }

	static function getAppLink() { return 'index.php?option=com_virtuemart'; }

	static function getVMLang() {	
		$lang_params = JComponentHelper::getParams('com_languages');
		$lang = $lang_params->get('site', 'en-GB'); //use default joomla
		return strtolower(strtr($lang,'-','_'));
	}
	static function scrubids($ids) {
		if(!is_array($ids)) $ids = array($ids);
		array_walk($ids, create_function('&$val', '$val = (int)$val;'));
		if(empty($ids)) $ids = array(0);
		return implode(',',$ids);
	}
	
	static function getAwoUser($coupon_id,$order_by=null) {
		$db = JFactory::getDBO();

		$sql = 'SELECT a.coupon_id,a.user_id,u.name as user_name FROM #__awocoupon_user a JOIN #__users u ON u.id=a.user_id WHERE a.coupon_id IN ('.self::scrubids($coupon_id).')
				 '.(!empty($order_by) ? $order_by : '');
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	static function getAwoShopperGroup($coupon_id,$order_by=null) {
		$db = JFactory::getDBO();
		
		$sql = 'SELECT a.coupon_id,a.shopper_group_id as user_id,u.shopper_group_name as user_name
				  FROM #__awocoupon_usergroup a
				  JOIN #__virtuemart_shoppergroups u ON u.virtuemart_shoppergroup_id=a.shopper_group_id
				 WHERE a.coupon_id IN ('.self::scrubids($coupon_id).')
				 '.(!empty($order_by) ? $order_by : '');
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	static function getAwoItem($table,$coupon_id,$order_by=null) {
		if($table!='1' && $table!='2') return;
		
		$db = JFactory::getDBO();
		$coupon_ids = self::scrubids($coupon_id);
		$lang = self::getVMLang();
		
		$sql = 'SELECT a.coupon_id,a.asset_id,c.product_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__virtuemart_products b ON b.virtuemart_product_id=a.asset_id
				  JOIN #__virtuemart_products_'.$lang.' c USING (virtuemart_product_id)
				 WHERE a.asset_type="product" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,c.category_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__virtuemart_categories b ON b.virtuemart_category_id=a.asset_id
				  JOIN #__virtuemart_categories_'.$lang.' c USING (virtuemart_category_id)
				 WHERE a.asset_type="category" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,c.mf_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__virtuemart_manufacturers b ON b.virtuemart_manufacturer_id=a.asset_id
				  JOIN #__virtuemart_manufacturers_'.$lang.' c USING (virtuemart_manufacturer_id)
				 WHERE a.asset_type="manufacturer" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,c.vendor_store_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__virtuemart_vendors b ON b.virtuemart_vendor_id=a.asset_id
				  JOIN #__virtuemart_vendors_'.$lang.' c USING (virtuemart_vendor_id)
				 WHERE a.asset_type="vendor" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,c.shipment_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__virtuemart_shipmentmethods b ON b.virtuemart_shipmentmethod_id=a.asset_id
				  JOIN #__virtuemart_shipmentmethods_'.$lang.' c USING (virtuemart_shipmentmethod_id)
				 WHERE a.asset_type="shipping" AND a.coupon_id IN ('.$coupon_ids.')
								UNION
				 SELECT a.coupon_id,a.asset_id,b.coupon_code AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__awocoupon b ON b.id=a.asset_id
				 WHERE a.asset_type="coupon" AND a.coupon_id IN ('.$coupon_ids.')
				 
				 
				 '.(!empty($order_by) ? $order_by : '');
		$db->setQuery($sql);
		return $db->loadObjectList();


	}

	
		
	static function getEStoreProduct($product_id=null,$search=null,$limit=null,$is_published=true,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS
					p.virtuemart_product_id AS id,CONCAT(lang.product_name," (",p.product_sku,")") AS label,p.product_sku as sku,lang.product_name
				  FROM #__virtuemart_products p
				  JOIN `#__virtuemart_products_'.self::getVMLang().'` as lang using (`virtuemart_product_id`)
				 WHERE 1=1
				 '.($is_published ? ' AND p.published=1 ' : '').'
				 '.(!empty($product_id) ? ' AND p.virtuemart_product_id IN ('.self::scrubids($product_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(lang.product_name," (",p.product_sku,")") LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ?'label,p.product_sku' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreProductNotGift($product_id=null,$search=null,$limit=null) {
		$db = JFactory::getDBO();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS p.virtuemart_product_id AS id,CONCAT(lang.product_name," (",p.product_sku,")") AS label 
				  FROM #__virtuemart_products p
				  JOIN `#__virtuemart_products_'.self::getVMLang().'` as lang using (`virtuemart_product_id`)
				  LEFT JOIN #__awocoupon_giftcert_product g ON g.product_id=p.virtuemart_product_id
				 WHERE p.published=1
				 '.(!empty($product_id) ? ' AND p.virtuemart_product_id IN ('.self::scrubids($product_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(lang.product_name," (",p.product_sku,")") LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY label,p.product_sku
				 '.(!empty($limit) ? ' LIMIT '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreCategory($category_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		if(empty($category_id) && empty($search) && empty($limit)) return self::categoryListTree();
		
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS c.virtuemart_category_id AS id,lang.category_name AS label
				  FROM #__virtuemart_categories c
				  JOIN `#__virtuemart_categories_'.self::getVMLang().'` as lang using (`virtuemart_category_id`)
				 WHERE c.published=1
				 '.(!empty($category_id) ? ' AND c.virtuemart_category_id IN ('.self::scrubids($category_id).') ' : '').'
				 '.(!empty($search) ? ' AND lang.category_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'lang.category_name,c.virtuemart_category_id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static private function categoryListTree($selectedCategories = array(), $cid = 0, $level = 0, $disabledFields=array()) {
		global $option;
		
		$cache = JFactory::getCache($option);
		$cache->setCaching( 1 );
		$cache->setLifeTime(version_compare( JVERSION, '1.6.0', 'ge' ) ? 300/60 : 300);
		//$rtn = $cache->call( array( 'AwocouponVirtuemartHelper', 'categoryListTreeLoop' ),$selectedCategories, $cid, $level, $disabledFields );
		$rtn = self::categoryListTreeLoop($selectedCategories, $cid, $level, $disabledFields);

		return $rtn;
	}
	static private function categoryListTreeLoop($selectedCategories = array(), $cid = 0, $level = 0, $disabledFields=array()) {
		static $categoryTree = array();
		
		$db = JFactory::getDBO();
		$cid = (int)$cid;

		$level++;

		$sql = 'SELECT c.`virtuemart_category_id`, l.`category_description`, l.`category_name`, c.`ordering`, c.`published`, cx.`category_child_id`, cx.`category_parent_id`, c.`shared` 
				  FROM `#__virtuemart_categories_'.self::getVMLang().'` l
				  JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)
				  LEFT JOIN `#__virtuemart_category_categories` AS cx ON l.`virtuemart_category_id` = cx.`category_child_id`
				  WHERE c.`published` = 1 AND cx.`category_parent_id` = '. (int)$cid;
		$db->setQuery($sql);
		$records = $db->loadObjectList();

		$selected="";
		if(!empty($records)){
			foreach ($records as $key => $category) {
				if(empty($category->category_child_id)) continue;//$category->category_child_id = $category->category_id;

				$childId = $category->category_child_id;

				if ($childId != $cid) {
					if(in_array($childId, $selectedCategories)) $selected = 'selected=\"selected\"'; else $selected='';

						/*$categoryTree .= '<option '. $selected .' value="'. $childId .'">'."\n";
						$categoryTree .= str_repeat(' - ', ($level-1) );

						$categoryTree .= $category->category_name .'</option>';*/
						
						$categoryTree[$childId] = (object) array(
								'category_id'=>$childId,
								'category_name'=>$category->category_name,
								'id'=>$childId,
								'label'=>str_repeat('---', ($level-1) ).$category->category_name,
							);
				}

				
				$sql = 'SELECT category_child_id FROM #__virtuemart_category_categories WHERE category_parent_id='.(int)$childId;
				$db->setQuery($sql);
				$db->query();
				if ($db->getAffectedRows() > 0){
					self::categoryListTreeLoop($selectedCategories, $childId, $level, $disabledFields);
				}

			}
		}

		return $categoryTree;
	}
	static function getEStoreManufacturer($manu_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS m.virtuemart_manufacturer_id AS id,lang.mf_name AS label
				  FROM #__virtuemart_manufacturers m
				  JOIN `#__virtuemart_manufacturers_'.self::getVMLang().'` as lang using (`virtuemart_manufacturer_id`)
				 WHERE m.published=1
				 '.(!empty($manu_id) ? ' AND m.virtuemart_manufacturer_id IN ('.self::scrubids($manu_id).') ' : '').'
				 '.(!empty($search) ? ' AND lang.mf_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'lang.mf_name,m.virtuemart_manufacturer_id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreVendor($vendor_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS v.virtuemart_vendor_id AS id,lang.vendor_store_name AS label
				  FROM #__virtuemart_vendors v
				  JOIN `#__virtuemart_vendors_'.self::getVMLang().'` as lang using (`virtuemart_vendor_id`)
				 WHERE 1=1
				 '.(!empty($vendor_id) ? ' AND v.virtuemart_vendor_id IN ('.self::scrubids($vendor_id).') ' : '').'
				 '.(!empty($search) ? ' AND lang.vendor_store_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'lang.vendor_store_name,v.virtuemart_vendor_id ' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreShipping($shipping_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS s.virtuemart_shipmentmethod_id AS id, lang.shipment_name AS label,p.name as carrier
				  FROM #__virtuemart_shipmentmethods s
				  JOIN `#__virtuemart_shipmentmethods_'.self::getVMLang().'` as lang using (`virtuemart_shipmentmethod_id`)
				  '.(version_compare( JVERSION, '1.6.0', 'ge' )
						? 'LEFT JOIN #__extensions p ON p.extension_id=s.shipment_jplugin_id'
						: 'LEFT JOIN #__plugins p ON p.id=s.shipment_jplugin_id').'
				 WHERE s.published=1 
				 '.(!empty($shipping_id) ? ' AND s.virtuemart_shipmentmethod_id IN ('.self::scrubids($shipping_id).') ' : '').'
				 '.(!empty($search) ? ' AND lang.shipment_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'label,id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);//trigger_error(print_r($sql,1));
		return $db->loadObjectList('id');
	}
	static function getEStoreShopperGroup($shoppergroup_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS virtuemart_shoppergroup_id AS id,shopper_group_name AS label 
				  FROM #__virtuemart_shoppergroups
				 WHERE 1=1
				 '.(!empty($shoppergroup_id) ? ' AND virtuemart_shoppergroup_id IN ('.self::scrubids($shoppergroup_id).') ' : '').'
				 '.(!empty($search) ? ' AND shopper_group_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'shopper_group_name,virtuemart_shoppergroup_id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreUser($user_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';

		$sql = 'SELECT SQL_CALC_FOUND_ROWS
						u.id,CONCAT(u.username," - ",u.name) as label,
						u.username,
						IF(vu.last_name IS NULL,
								TRIM(SUBSTRING(TRIM(u.name),LENGTH(TRIM(u.name))-LOCATE(" ",REVERSE(TRIM(u.name)))+1)),
								vu.last_name) as lastname,
						IF(vu.first_name IS NULL,
								TRIM(REVERSE(SUBSTRING(REVERSE(TRIM(u.name)),LOCATE(" ",REVERSE(TRIM(u.name)))+1))),
								vu.first_name) as firstname
				  FROM #__users u
				  LEFT JOIN #__virtuemart_userinfos vu ON vu.virtuemart_user_id=u.id AND vu.address_type="BT"
				 WHERE 1=1
				 '.(!empty($user_id) ? ' AND u.id IN ('.self::scrubids($user_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(u.username," - ",u.name) LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'label,u.id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}

	
	
	static function getOrder($order_id) {
		$db = JFactory::getDBO();
		$sql = 'SELECT * FROM #__virtuemart_orders WHERE virtuemart_order_id='.(int)$order_id;
		$db->setQuery($sql);
		return $db->loadObject();
	}
	
	static function getOrderDetailLink($order_id) { return JRoute::_('index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id='.(int)$order_id); }

	
	static function getQueryHistoryOrder($where,$having,$orderby) {
		$sql = 'SELECT go.order_id,go.codes,o.order_number,"" AS coupon_code
				 FROM #__awocoupon_giftcert_order go
				 LEFT JOIN #__virtuemart_orders o ON o.virtuemart_order_id=go.order_id
				WHERE go.estore="virtuemart"
				'.$orderby
			;
		return $sql;
	}
	static function getQueryHistoryGift($where,$having,$orderby) {
		$sql = 'SELECT c.*,
					 uv.virtuemart_user_id AS user_id,uv.first_name,uv.last_name,u.username,
					 o.virtuemart_order_id AS order_id,UNIX_TIMESTAMP(o.created_on) AS cdate,
					 SUM(au.coupon_discount)+SUM(au.shipping_discount) AS coupon_value_used,
					 c.coupon_value-IFNULL(SUM(au.coupon_discount),0)-IFNULL(SUM(au.shipping_discount),0) AS balance,au.user_email,
					 u.username as _username, uv.first_name as _fname, uv.last_name as _lname
				 FROM #__awocoupon c
				 LEFT JOIN #__virtuemart_orders o ON o.virtuemart_order_id=c.order_id
				 LEFT JOIN #__virtuemart_userinfos uv ON uv.virtuemart_user_id=o.virtuemart_user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=o.virtuemart_user_id
				 LEFT JOIN #__awocoupon_history au ON au.coupon_id=c.id
				WHERE c.estore="virtuemart" AND c.function_type="giftcert"
				'.$where
				.' GROUP BY c.id'
				.$having.' '
				.$orderby
			;
		return $sql;
	}
	static function getQueryHistoryCoupon($where,$having,$orderby) {
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.startdate,c.expiration,c.published,
					 uu.coupon_id,uu.coupon_entered_id,c2.coupon_code as coupon_entered_code,
					 uu.id as use_id,uv.first_name,uv.last_name,uu.user_id,u.username,
					 (uu.coupon_discount+uu.shipping_discount) AS discount,uu.productids,uu.timestamp,uu.user_email,
					 ov.virtuemart_order_id AS order_id,ov.created_on AS cdate,ov.order_number,
					 u.username as _username, uv.first_name as _fname, uv.last_name as _lname,ov.created_on AS _created_on
				 FROM #__awocoupon_history uu
				 JOIN #__awocoupon c ON c.id=uu.coupon_id
				 LEFT JOIN #__awocoupon c2 ON c2.id=uu.coupon_entered_id
				 LEFT JOIN #__virtuemart_userinfos uv ON uv.virtuemart_user_id=uu.user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=uu.user_id
				 LEFT JOIN #__virtuemart_orders ov ON ov.virtuemart_order_id=uu.order_id
				WHERE uu.estore="virtuemart"
				'.$where.'
				'.$having.'
				'.$orderby;
			;
		return $sql;
	}

	static function getQueryAwoUser($coupon_id,$orderby) {
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.startdate,c.expiration,u.user_id as _user_id,
					 if(uv.virtuemart_user_id is NULL,us.name,uv.first_name) as _fname,uv.last_name as _lname,
					 if(uv.virtuemart_user_id is NULL,us.name,CONCAT(uv.first_name," ",uv.last_name)) as _name,
					 u.user_id as asset_id
				 FROM #__awocoupon c
				 JOIN #__awocoupon_user u ON u.coupon_id=c.id
				 JOIN #__users us ON us.id=u.user_id
				 LEFT JOIN #__virtuemart_userinfos uv ON uv.virtuemart_user_id=u.user_id
				WHERE c.id='.(int)$coupon_id.'
				GROUP BY u.user_id
				'.$orderby
			; 
		return $sql;
	}
	static function getQueryAwoShopperGroup($coupon_id,$orderby) {
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.startdate,c.expiration,u.shopper_group_id,
					 g.shopper_group_name as label, g.shopper_group_name as _name, u.shopper_group_id as sid, u.shopper_group_id as asset_id
				 FROM #__awocoupon c
				 JOIN #__awocoupon_usergroup u ON u.coupon_id=c.id
				 JOIN #__virtuemart_shoppergroups g ON g.virtuemart_shoppergroup_id=u.shopper_group_id
				WHERE c.id='.$coupon_id.'
				'.$orderby
			; 
		return $sql;
	}
	
	static function getQueryGiftCertProducts($where,$having,$orderby) {
		$sql = 'SELECT g.*,lang.product_name as _product_name,p.product_sku,pr.title as profile, COUNT(pc.id) as codecount,c.coupon_code
				  FROM #__awocoupon_giftcert_product g
				  LEFT JOIN #__awocoupon c ON c.id=g.coupon_template_id
				  LEFT JOIN #__virtuemart_products p ON p.virtuemart_product_id=g.product_id
				  LEFT JOIN `#__virtuemart_products_'.self::getVMLang().'` as lang using (`virtuemart_product_id`)
				  LEFT JOIN #__awocoupon_profile pr ON pr.id=g.profile_id
				  LEFT JOIN #__awocoupon_giftcert_code pc ON pc.product_id=p.virtuemart_product_id
				 WHERE g.estore="virtuemart"
				 '.$where.'
				   GROUP BY g.id
				   '.$having.'
				   '.$orderby;
		return $sql;
	}
	static function getQueryGiftCertProduct($product_id) {
		$sql = 'SELECT lang.product_name,p.product_sku
				   FROM #__virtuemart_products p
				   JOIN `#__virtuemart_products_'.self::getVMLang().'` as lang using (`virtuemart_product_id`)
				   WHERE p.virtuemart_product_id = '.$product_id;
		return $sql;
	}
	static function getQueryGiftCertProductCodes($where,$having,$orderby) {
		$sql = 'SELECT g.*,lang.product_name as _product_name,p.product_sku
				  FROM #__awocoupon_giftcert_code g
				  LEFT JOIN #__virtuemart_products p ON p.virtuemart_product_id=g.product_id
				  LEFT JOIN `#__virtuemart_products_'.self::getVMLang().'` as lang using (`virtuemart_product_id`)
				   '.$where.' '.$having.' '.$orderby;
		return $sql;
	}
	

	/*static function getHistorySentGift($order_id) {
		$db = JFactory::getDBO();
		$sql = 'SELECT i.virtuemart_order_item_id AS order_item_id,i.virtuemart_order_id AS order_id,i.product_item_price,i.product_quantity,
						u.virtuemart_user_id AS user_id,u.email,u.first_name,u.last_name,ap.expiration_number,ap.expiration_type,ap.coupon_template_id,
						i.order_item_currency,ap.profile_id,i.virtuemart_product_id AS product_id,i.product_attribute,i.order_item_name,g.codes
						
				  FROM #__virtuemart_order_items i 
				  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.virtuemart_product_id
				  JOIN #__virtuemart_order_userinfos u ON u.virtuemart_order_id=i.virtuemart_order_id AND u.address_type="BT"
				  JOIN #__awocoupon_giftcert_order g ON g.order_id=i.virtuemart_order_id AND g.email_sent=1
				 WHERE i.virtuemart_order_id='.(int)$order_id.' AND ap.published=1
				 GROUP BY i.virtuemart_order_item_id';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}*/
    //T.Trung
    static function getHistorySentGift($order_id) {
		$db = JFactory::getDBO();
		$sql = 'SELECT i.virtuemart_order_item_id AS order_item_id,i.virtuemart_order_id AS order_id,i.product_item_price,i.product_quantity,
						u.virtuemart_user_id AS user_id,u.email1 as email,u.first_name,u.last_name, u.message1 as message, ap.expiration_number, ap.expiration_type, ap.coupon_template_id,
						i.order_item_currency,ap.profile_id,i.virtuemart_product_id AS product_id,i.product_attribute,i.order_item_name,g.codes
						
				  FROM #__virtuemart_order_items i 
				  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.virtuemart_product_id
				  JOIN #__virtuemart_order_userinfos u ON u.virtuemart_order_id=i.virtuemart_order_id AND u.address_type="ST"
				  JOIN #__awocoupon_giftcert_order g ON g.order_id=i.virtuemart_order_id AND g.email_sent=1
				 WHERE i.virtuemart_order_id='.(int)$order_id.' AND ap.published=1
				 GROUP BY i.virtuemart_order_item_id';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	//T.Trung end
	
	
	static function rpt_purchased_giftcert_list($start_date,$end_date,$order_status) {
		$datestr = '';
		if(!empty($start_date) && !empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		$initial_list = array();
		$coupon_ids = array();
		$sql = 'SELECT go.codes,
					 uv.virtuemart_user_id AS user_id,uv.first_name,uv.last_name,u.username,u.email,
					 o.virtuemart_order_id AS order_id,o.order_total,UNIX_TIMESTAMP(o.created_on) AS ocdate,go.codes,
					 o.order_subtotal,o.order_tax,o.order_shipment,o.order_shipment_tax,o.order_discount*-1 AS order_fee
				 FROM #__awocoupon_giftcert_order go
				 LEFT JOIN #__virtuemart_orders o ON o.virtuemart_order_id=go.order_id
				 LEFT JOIN #__virtuemart_userinfos uv ON uv.virtuemart_user_id=o.virtuemart_user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=o.virtuemart_user_id
				WHERE go.estore="virtuemart"
				 '.$datestr.'
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 GROUP BY go.order_id
				 ORDER BY go.order_id'
			;
		return $sql;
	}
	
	static function rpt_history_uses_coupons($start_date,$end_date,$order_status,$where) {
		$datestr = '';
		if(!empty($start_date) && !empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(ov.created_on) BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(ov.created_on) >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(ov.created_on) <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,c.published,
					 uu.coupon_id,uu.coupon_entered_id,c2.coupon_code as coupon_entered_code,
					 uv.first_name,uv.last_name,uu.user_id,u.username,
					 (uu.coupon_discount+uu.shipping_discount) AS discount,uu.productids,uu.timestamp,
					 ov.virtuemart_order_id AS order_id,ov.order_total,UNIX_TIMESTAMP(ov.created_on) AS cdate,uu.id as num_uses_id,
					 ov.order_subtotal,ov.order_tax,ov.order_shipment,ov.order_shipment_tax,ov.order_discount*-1 AS order_fee
				 FROM #__awocoupon c
				 JOIN #__awocoupon_history uu ON uu.coupon_id=c.id
				 JOIN #__virtuemart_userinfos uv ON uv.virtuemart_user_id=uu.user_id
				 LEFT JOIN #__awocoupon c2 ON c2.id=uu.coupon_entered_id
				 LEFT JOIN #__users u ON u.id=uu.user_id
				 LEFT JOIN #__virtuemart_orders ov ON ov.virtuemart_order_id=uu.order_id
				WHERE c.estore="virtuemart"
				 '.$datestr.'
				 '.(!empty($order_status) && is_array($order_status) ? ' AND ov.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$where.'
				 ORDER BY u.username'
			;
		return $sql;
	}

	static function rpt_history_uses_giftcerts($start_date,$end_date,$order_status,$published) {
			
		$datestr = '';
		if(!empty($start_date) && !empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,c.published,
					 uv.virtuemart_user_id as user_id,uv.first_name,uv.last_name,u.username,
					 o.virtuemart_order_id,o.order_total,UNIX_TIMESTAMP(o.created_on) AS cdate,go.codes,
					 o.order_subtotal,o.order_tax,o.order_shipment,o.order_shipment_tax,o.order_discount*-1 AS order_fee,
					 SUM(au.coupon_discount)+SUM(au.shipping_discount) AS coupon_value_used,
					 c.coupon_value-IFNULL(SUM(au.coupon_discount),0)-IFNULL(SUM(au.shipping_discount),0) AS balance
				 FROM #__awocoupon c
				 LEFT JOIN #__virtuemart_orders o ON o.virtuemart_order_id=c.order_id
				 LEFT JOIN #__virtuemart_userinfos uv ON uv.virtuemart_user_id=o.virtuemart_user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=o.virtuemart_user_id
				 LEFT JOIN #__awocoupon_history au ON au.coupon_id=c.id
				 LEFT JOIN #__awocoupon_giftcert_order go ON go.order_id=o.virtuemart_order_id
				WHERE c.estore="virtuemart" AND c.function_type="giftcert"
				 '.$datestr.'
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.(!empty($published) ? 'AND c.published="'.$published.'" ' : '').'
				 GROUP BY c.id
				 ORDER BY u.username'
			;
			
		return $sql;
	}
	
	static function rpt_coupon_vs_total($start_date,$end_date,$order_status,$where) {
		$datestr = '';
		if(!empty($start_date) && !empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		

		$sql = 'SELECT c.id, SUM(o.order_total) as total, COUNT(c.id) as count
				  FROM #__awocoupon c
				  JOIN (SELECT coupon_entered_id,order_id FROM #__awocoupon_history GROUP BY order_id,coupon_entered_id) uu ON uu.coupon_entered_id=c.id
				  JOIN #__virtuemart_orders o ON o.virtuemart_order_id=uu.order_id
				 WHERE c.estore="virtuemart"
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$datestr.'
				 '.$where.'
				 GROUP BY c.id';
		return $sql;
	}
	
	static function rpt_coupon_vs_location($start_date,$end_date,$order_status,$where) {
		$db = JFactory::getDBO();
		$datestr = '';
		if(!empty($start_date) && !empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND UNIX_TIMESTAMP(o.created_on) <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
				
		$sql = 'SELECT c.id,c.coupon_code, SUM(o.order_total) as total, COUNT(uu.order_id) as count,ctry.country_3_code as country,st.state_2_code as state,u.city,
					 CONCAT(c.id,"-",IF(ISNULL(ctry.country_3_code),"0",ctry.country_3_code),"-",IF(ISNULL(st.state_2_code),"0",st.state_2_code),"-",u.city) as realid
				  FROM #__awocoupon c
				  JOIN (SELECT coupon_entered_id,order_id FROM #__awocoupon_history GROUP BY order_id,coupon_entered_id) uu ON uu.coupon_entered_id=c.id
				  JOIN #__virtuemart_orders o ON o.virtuemart_order_id=uu.order_id
				  JOIN #__virtuemart_order_userinfos u ON u.virtuemart_order_id=o.virtuemart_order_id AND u.address_type="BT"
				  LEFT JOIN #__virtuemart_countries ctry ON ctry.virtuemart_country_id=u.virtuemart_country_id
				  LEFT JOIN #__virtuemart_states st ON st.virtuemart_state_id=u.virtuemart_state_id
				 WHERE c.estore="virtuemart"
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$datestr.'
				 '.$where.'
				 GROUP BY c.id,u.virtuemart_country_id,u.virtuemart_state_id,u.city';
		$db->setQuery( $sql );
		$order_details = $db->loadAssocList('realid');

		if(empty($order_details)) return;


		$sql = 'SELECT c.id,c.coupon_code, uu.productids,
						SUM(uu.coupon_discount+uu.shipping_discount) as discount,
						ctry.country_3_code as country,st.state_2_code as state,u.city
				  FROM #__awocoupon_history uu
				  JOIN #__awocoupon c ON c.id=uu.coupon_entered_id 
				  JOIN #__virtuemart_orders o ON o.virtuemart_order_id=uu.order_id
				  JOIN #__virtuemart_order_userinfos u ON u.virtuemart_order_id=o.virtuemart_order_id AND u.address_type="BT"
				  LEFT JOIN #__virtuemart_countries ctry ON ctry.virtuemart_country_id=u.virtuemart_country_id
				  LEFT JOIN #__virtuemart_states st ON st.virtuemart_state_id=u.virtuemart_state_id
				 WHERE c.estore="virtuemart"
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$datestr.'
				 '.$where.'
				 GROUP BY c.id,u.virtuemart_country_id,u.virtuemart_state_id,u.city
				 ORDER BY c.coupon_code';
				 
				 
		return (object) array('sql'=>$sql,'order_details'=>$order_details);
	}
	
	static function getOrderStatuses() {
		$jlang =JFactory::getLanguage();
		$jlang->load('com_virtuemart_orders', JPATH_SITE, 'en-GB', true);
		$jlang->load('com_virtuemart_orders', JPATH_SITE, $jlang->getDefault(), true);
		$jlang->load('com_virtuemart_orders', JPATH_SITE, null, true);
		$db = JFactory::getDBO();
		$sql = 'SELECT virtuemart_orderstate_id AS order_status_id, order_status_code, order_status_name FROM #__virtuemart_orderstates';
		$db->setQuery($sql);
		$items = $db->loadObjectList();
		foreach($items as $k=>$item) {
			$items[$k]->order_status_name = JText::_($item->order_status_name);
		}
		return $items;
	}

	static function priceDisplay($amount) {
		if (!class_exists( 'VmConfig' )) require JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php';
		VmConfig::loadConfig();
		require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/currencydisplay.php';
		$currency_class = CurrencyDisplay::getInstance('',1);
		
		return $currency_class->priceDisplay($amount);
	}

}