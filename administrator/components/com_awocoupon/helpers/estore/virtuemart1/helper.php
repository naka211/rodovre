<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();



class AwocouponVirtuemart1Helper {

	static function isInstalled() { 
		if(file_exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/version.php')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/version.php';
			if(isset($VMVERSION->RELEASE)) {
				if(substr($VMVERSION->RELEASE,0,4)=='1.1.') return true;
				elseif(substr($VMVERSION->RELEASE,0,4)=='1.2.') return true;
			}
		}
		return false;
	}

	static function getAwoCouponPluginFolder() { return 'virtuemart'; }

	static function getAppLink() { return 'index.php?option=com_virtuemart'; }

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
				  JOIN #__vm_shopper_group u ON u.shopper_group_id=a.shopper_group_id
				 WHERE a.coupon_id IN ('.self::scrubids($coupon_id).')
				 '.(!empty($order_by) ? $order_by : '');
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	static function getAwoItem($table,$coupon_id,$order_by=null) {
		if($table!='1' && $table!='2') return;
		
		$db = JFactory::getDBO();
		$coupon_ids = self::scrubids($coupon_id);
		
		$sql = 'SELECT a.coupon_id,a.asset_id,b.product_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__vm_product b ON b.product_id=a.asset_id
				 WHERE a.asset_type="product" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,b.category_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__vm_category b ON b.category_id=a.asset_id
				 WHERE a.asset_type="category" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,b.mf_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__vm_manufacturer b ON b.manufacturer_id=a.asset_id
				 WHERE a.asset_type="manufacturer" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,b.vendor_store_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__vm_vendor b ON b.vendor_id=a.asset_id
				 WHERE a.asset_type="vendor" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				 SELECT a.coupon_id,a.asset_id,b.coupon_code AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__awocoupon b ON b.id=a.asset_id
				 WHERE a.asset_type="coupon" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,CONCAT(b.field1," - ",b.field2) AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__awocoupon_vm1ids b ON b.id=a.asset_id
				 WHERE a.asset_type="shipping" AND a.coupon_id IN ('.$coupon_ids.')
				 
				 
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
					p.product_id AS id,CONCAT(p.product_name," (",p.product_sku,")") AS label,p.product_sku as sku,p.product_name
				  FROM #__vm_product p
				 WHERE 1=1
				 '.($is_published ? ' AND p.product_publish="Y" ' : '').'
				 '.(!empty($product_id) ? ' AND p.product_id IN ('.self::scrubids($product_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(p.product_name," (",p.product_sku,")") LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ?'label,p.product_sku' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreProductNotGift($product_id=null,$search=null,$limit=null) {
		$db = JFactory::getDBO();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS p.product_id AS id,CONCAT(p.product_name," (",p.product_sku,")") AS label 
				  FROM #__vm_product p
				  LEFT JOIN #__awocoupon_giftcert_product g ON g.product_id=p.product_id
				 WHERE p.product_publish="Y"
				 '.(!empty($product_id) ? ' AND p.product_id IN ('.self::scrubids($product_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(p.product_name," (",p.product_sku,")") LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
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
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS c.category_id AS id,c.category_name AS label
				  FROM #__vm_category c
				 WHERE c.category_publish="Y"
				 '.(!empty($category_id) ? ' AND c.category_id IN ('.self::scrubids($category_id).') ' : '').'
				 '.(!empty($search) ? ' AND c.category_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'c.category_name,c.category_id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
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

		$sql = 'SELECT c.`category_id`, c.`category_description`, c.`category_name`, c.`list_order`, cx.`category_child_id`, cx.`category_parent_id`
				  FROM `#__vm_category` c
				  LEFT JOIN #__vm_category_xref AS cx ON c.category_id = cx.`category_child_id`
				  WHERE c.category_publish="Y" AND cx.`category_parent_id` = '. (int)$cid;
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

				
				$sql = 'SELECT category_child_id FROM #__vm_category_xref WHERE category_parent_id='.(int)$childId;
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
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS m.manufacturer_id AS id,m.mf_name AS label
				  FROM #__vm_manufacturer m
				 WHERE 1=1
				 '.(!empty($manu_id) ? ' AND m.manufacturer_id IN ('.self::scrubids($manu_id).') ' : '').'
				 '.(!empty($search) ? ' AND m.mf_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'm.mf_name,m.manufacturer_id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreVendor($vendor_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS v.vendor_id AS id,v.vendor_store_name AS label
				  FROM #__vm_vendor v
				 WHERE 1=1
				 '.(!empty($vendor_id) ? ' AND v.vendor_id IN ('.self::scrubids($vendor_id).') ' : '').'
				 '.(!empty($search) ? ' AND v.vendor_store_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'v.vendor_store_name,v.vendor_id ' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreShipping($shipping_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		if(!defined('VM_TABLEPREFIX')) require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.cfg.php';
		global $PSHOP_SHIPPING_MODULES;
		$db = JFactory::getDBO();

		// get virtuemart shipping
		$o = array();
		$file_path = dirname(__FILE__);
		foreach ($PSHOP_SHIPPING_MODULES as $shipping_module) {
			if(file_exists($file_path.'/shipping/'.$shipping_module.'.php')) {
				require_once $file_path.'/shipping/'.$shipping_module.'.php';
				$shipping_class = 'awo_'.$shipping_module;
				if(class_exists($shipping_class)) {
					$SHIPPING = new $shipping_class();
					$o = array_merge_recursive($o,$SHIPPING->get_all_rates());
				}
			}
		}
		// make sure items are marked in vm1 ids
		$db = JFactory::getDBO();
		$db->setQuery('SELECT id,value FROM #__awocoupon_vm1ids WHERE type="shipping_rate_id"');
		$shipping_list = $db->loadObjectList('value');

		// return published items
		$output = array();
		$search = strtolower($search);
		foreach($o as $shipping_module=>$row1) {
			if($shipping_module=='_raw') continue;
			
			$shipping_class = 'awo_'.$shipping_module;
			$shipping_class = new $shipping_class();
			$name_module = $shipping_class->get_module_name();
				
			foreach($row1 as $tmp) {
				$id = @(int)$shipping_list[$tmp->dbshipper_id]->id;
				if(empty($id)) {
					$db->setQuery('INSERT INTO #__awocoupon_vm1ids (type,value,field1,field2) VALUE ("shipping_rate_id","'.$tmp->dbshipper_id.'","'.awolibrary::dbEscape($name_module).'","'.awolibrary::dbEscape($tmp->shipper_string).'")');
					$db->query();
					$id = $db->insertid();
				}
				if(empty($id)) continue;
			
			
				$label = $name_module.' - '.$tmp->shipper_string;
				if(!empty($search) && strpos(strtolower($label),$search)===false) continue;
				if(!empty($shipping_id) && $tmp->dbshipper_id!=$shipping_id) continue;

				$output[$id] = (object) array(
					'id'=>$id,
					'label'=>$tmp->shipper_string,
					'carrier'=>$name_module,
				);
				
			}
		}
		return $output = array_slice($output,$limitstart,$limit);
		
	}
	static function getEStoreShopperGroup($shoppergroup_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS shopper_group_id AS id,shopper_group_name AS label 
				  FROM #__vm_shopper_group
				 WHERE 1=1
				 '.(!empty($shoppergroup_id) ? ' AND shopper_group_id IN ('.self::scrubids($shoppergroup_id).') ' : '').'
				 '.(!empty($search) ? ' AND shopper_group_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'shopper_group_name,shopper_group_id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
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
				  LEFT JOIN #__vm_user_info vu ON vu.user_id=u.id AND vu.address_type="BT"
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
		$sql = 'SELECT * FROM #__v_vm_orders WHERE order_id='.(int)$order_id;
		$db->setQuery($sql);
		return $db->loadObject();
	}
	
	static function getOrderDetailLink($order_id) { return JRoute::_('index.php?option=com_virtuemart&page=order.order_print&order_id='.(int)$order_id); }

	
	static function getQueryHistoryOrder($where,$having,$orderby) {
		$sql = 'SELECT go.order_id,go.codes,LPAD(o.order_id,8,"0") AS order_number,"" AS coupon_code
				 FROM #__awocoupon_giftcert_order go
				 LEFT JOIN #__vm_orders o ON o.order_id=go.order_id
				WHERE go.estore="virtuemart1"
				'.$orderby
			;
		return $sql;
	}
	static function getQueryHistoryGift($where,$having,$orderby) {
		$sql = 'SELECT c.*,
					 uv.user_id AS user_id,uv.first_name,uv.last_name,u.username,
					 o.order_id AS order_id,o.cdate AS cdate,
					 SUM(au.coupon_discount)+SUM(au.shipping_discount) AS coupon_value_used,
					 c.coupon_value-IFNULL(SUM(au.coupon_discount),0)-IFNULL(SUM(au.shipping_discount),0) AS balance,au.user_email,
					 u.username as _username, uv.first_name as _fname, uv.last_name as _lname
				 FROM #__awocoupon c
				 LEFT JOIN #__vm_orders o ON o.order_id=c.order_id
				 LEFT JOIN #__vm_user_info uv ON uv.user_id=o.user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=o.user_id
				 LEFT JOIN #__awocoupon_history au ON au.coupon_id=c.id
				WHERE c.estore="virtuemart1" AND c.function_type="giftcert"
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
					 ov.order_id AS order_id,FROM_UNIXTIME(ov.cdate) AS cdate,LPAD(ov.order_id,8,"0") AS order_number,
					 u.username as _username, uv.first_name as _fname, uv.last_name as _lname,FROM_UNIXTIME(ov.cdate) AS _created_on
				 FROM #__awocoupon_history uu
				 JOIN #__awocoupon c ON c.id=uu.coupon_id
				 LEFT JOIN #__awocoupon c2 ON c2.id=uu.coupon_entered_id
				 LEFT JOIN #__vm_user_info uv ON uv.user_id=uu.user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=uu.user_id
				 LEFT JOIN #__awocoupon_vm1ids vm1 ON vm1.id=uu.order_id
				 LEFT JOIN #__vm_orders ov ON ov.order_number=vm1.value
				WHERE uu.estore="virtuemart1"
				'.$where.'
				'.$having.'
				'.$orderby;
			;
		return $sql;
	}

	static function getQueryAwoUser($coupon_id,$orderby) {
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.startdate,c.expiration,u.user_id as _user_id,
					 if(uv.user_id is NULL,us.name,uv.first_name) as _fname,uv.last_name as _lname,
					 if(uv.user_id is NULL,us.name,CONCAT(uv.first_name," ",uv.last_name)) as _name,
					 u.user_id as asset_id
				 FROM #__awocoupon c
				 JOIN #__awocoupon_user u ON u.coupon_id=c.id
				 JOIN #__users us ON us.id=u.user_id
				 LEFT JOIN #__vm_user_info uv ON uv.user_id=u.user_id
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
				 JOIN #__vm_shopper_group g ON g.shopper_group_id=u.shopper_group_id
				WHERE c.id='.$coupon_id.'
				'.$orderby
			; 
		return $sql;
	}
	
	static function getQueryGiftCertProducts($where,$having,$orderby) {
		$sql = 'SELECT g.*,p.product_name as _product_name,p.product_sku,pr.title as profile, COUNT(pc.id) as codecount,c.coupon_code
				  FROM #__awocoupon_giftcert_product g
				  LEFT JOIN #__awocoupon c ON c.id=g.coupon_template_id
				  LEFT JOIN #__vm_product p ON p.product_id=g.product_id
				  LEFT JOIN #__awocoupon_profile pr ON pr.id=g.profile_id
				  LEFT JOIN #__awocoupon_giftcert_code pc ON pc.product_id=p.product_id
				 WHERE g.estore="virtuemart1"
				 '.$where.'
				   GROUP BY g.id
				   '.$having.'
				   '.$orderby;
		return $sql;
	}
	static function getQueryGiftCertProduct($product_id) {
		$sql = 'SELECT p.product_name,p.product_sku
				   FROM #__vm_product p
				   WHERE p.product_id = '.$product_id;
		return $sql;
	}
	static function getQueryGiftCertProductCodes($where,$having,$orderby) {
		$sql = 'SELECT g.*,p.product_name as _product_name,p.product_sku
				  FROM #__awocoupon_giftcert_code g
				  LEFT JOIN #__vm_product p ON p.product_id=g.product_id
				   '.$where.' '.$having.' '.$orderby;
		return $sql;
	}
	

	static function getHistorySentGift($order_id) {
		$db = JFactory::getDBO();
		$sql = 'SELECT i.order_item_id AS order_item_id,i.order_id,i.product_item_price,i.product_quantity,
						u.user_id,u.user_email as email,u.first_name,u.last_name,ap.expiration_number,ap.expiration_type,ap.coupon_template_id,
						i.order_item_currency,ap.profile_id,i.product_id,i.product_attribute,i.order_item_name,g.codes
						
				  FROM #__vm_order_item i 
				  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.product_id
				  JOIN #__vm_order_user_info u ON u.order_id=i.order_id AND u.address_type="BT"
				  JOIN #__awocoupon_giftcert_order g ON g.order_id=i.order_id AND g.email_sent=1
				 WHERE i.order_id='.(int)$order_id.' AND ap.published=1
				 GROUP BY i.order_item_id';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	
	
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
					 uv.user_id,uv.first_name,uv.last_name,u.username,u.email,
					 o.order_id,o.order_total,o.cdate AS ocdate,go.codes,
					 o.order_subtotal,o.order_tax,o.order_shipping AS order_shipment,o.order_shipping_tax AS order_shipment_tax,o.order_discount*-1 AS order_fee
				 FROM #__awocoupon_giftcert_order go
				 LEFT JOIN #__vm_orders o ON o.order_id=go.order_id
				 LEFT JOIN #__vm_user_info uv ON uv.user_id=o.user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=o.user_id
				WHERE go.estore="virtuemart1"
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
					 ov.order_id,ov.order_total,ov.cdate,uu.id as num_uses_id,
					 ov.order_subtotal,ov.order_tax,ov.order_shipping AS order_shipment,ov.order_shipping_tax AS order_shipment_tax,ov.order_discount*-1 AS order_fee
				 FROM #__awocoupon c
				 JOIN #__awocoupon_history uu ON uu.coupon_id=c.id
				 JOIN #__vm_user_info uv ON uv.user_id=uu.user_id
				 LEFT JOIN #__awocoupon c2 ON c2.id=uu.coupon_entered_id
				 LEFT JOIN #__users u ON u.id=uu.user_id
				 LEFT JOIN #__vm_orders ov ON ov.order_id=uu.order_id
				WHERE c.estore="virtuemart1"
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
					 uv.user_id as user_id,uv.first_name,uv.last_name,u.username,
					 o.order_id,o.order_total,o.cdate,go.codes,
					 o.order_subtotal,o.order_tax,o.order_shipping AS order_shipment,o.order_shipping_tax AS order_shipment_tax,o.order_discount*-1 AS order_fee,
					 SUM(au.coupon_discount)+SUM(au.shipping_discount) AS coupon_value_used,
					 c.coupon_value-IFNULL(SUM(au.coupon_discount),0)-IFNULL(SUM(au.shipping_discount),0) AS balance
				 FROM #__awocoupon c
				 LEFT JOIN #__vm_orders o ON o.order_id=c.order_id
				 LEFT JOIN #__vm_user_info uv ON uv.user_id=o.user_id AND uv.address_type="BT"
				 LEFT JOIN #__users u ON u.id=o.user_id
				 LEFT JOIN #__awocoupon_history au ON au.coupon_id=c.id
				 LEFT JOIN #__awocoupon_giftcert_order go ON go.order_id=o.order_id
				WHERE c.estore="virtuemart1" AND c.function_type="giftcert"
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
				  JOIN #__vm_orders o ON o.order_id=uu.order_id
				 WHERE c.estore="virtuemart1"
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
				
		$sql = 'SELECT c.id,c.coupon_code, SUM(o.order_total) as total, COUNT(uu.order_id) as count,
					 CONCAT(c.id,"-",IF(ISNULL(u.country),"0",u.country),"-",IF(ISNULL(u.state),"0",u.state),"-",u.city) as realid,
					 u.country,u.state,u.city
				  FROM #__awocoupon c
				  JOIN (SELECT coupon_entered_id,order_id FROM #__awocoupon_history GROUP BY order_id,coupon_entered_id) uu ON uu.coupon_entered_id=c.id
				  JOIN #__vm_orders o ON o.order_id=uu.order_id
				  JOIN #__vm_order_user_info u ON u.order_id=o.order_id AND u.address_type="BT"
				 WHERE c.estore="virtuemart1"
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$datestr.'
				 '.$where.'
				 GROUP BY c.id,u.country,u.state,u.city';
		$db->setQuery( $sql );
		$order_details = $db->loadAssocList('realid');

		if(empty($order_details)) return;


		$sql = 'SELECT c.id,c.coupon_code, uu.productids,
						SUM(uu.coupon_discount+uu.shipping_discount) as discount,
						 u.country,u.state,u.city
				  FROM #__awocoupon_history uu
				  JOIN #__awocoupon c ON c.id=uu.coupon_entered_id 
				  JOIN #__vm_orders o ON o.order_id=uu.order_id
				  JOIN #__vm_order_user_info u ON u.order_id=o.order_id AND u.address_type="BT"
				 WHERE c.estore="virtuemart1"
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$datestr.'
				 '.$where.'
				 GROUP BY c.id,u.country,u.state,u.city
				 ORDER BY c.coupon_code';
				 
				 
		return (object) array('sql'=>$sql,'order_details'=>$order_details);
	}
	
	static function getOrderStatuses() {
		$db = JFactory::getDBO();
		$sql = 'SELECT order_status_id, order_status_code, order_status_name FROM #__vm_order_status';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}


}