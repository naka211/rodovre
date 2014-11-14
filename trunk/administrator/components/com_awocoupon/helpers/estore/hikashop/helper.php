<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


class AwocouponHikashopHelper {

	static function isInstalled() { return file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop.xml') || file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop_j3.xml') ? true : false; }

	static function getAwoCouponPluginFolder() { return 'hikashop'; }

	static function getAppLink() { return 'index.php?option=com_hikashop'; }

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
	static function getAwoShopperGroup($coupon_id,$order_by=null) { return null; }
	static function getAwoItem($table,$coupon_id,$order_by=null) {
		if($table!='1' && $table!='2') return;
		
		$db = JFactory::getDBO();
		$coupon_ids = self::scrubids($coupon_id);
		
		$sql = 'SELECT a.coupon_id,a.asset_id,b.product_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__hikashop_product b ON b.product_id=a.asset_id
				 WHERE a.asset_type="product" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,b.category_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__hikashop_category b ON b.category_id=a.asset_id AND b.category_type="product"
				 WHERE a.asset_type="category" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,b.shipping_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__hikashop_shipping b ON b.shipping_id=a.asset_id
				 WHERE a.asset_type="shipping" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				 SELECT a.coupon_id,a.asset_id,b.coupon_code AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__awocoupon b ON b.id=a.asset_id
				 WHERE a.asset_type="coupon" AND a.coupon_id IN ('.$coupon_ids.')
							UNION
				SELECT a.coupon_id,a.asset_id,b.category_name AS asset_name
				  FROM #__awocoupon_asset'.$table.' a
				  JOIN #__hikashop_category b ON b.category_id=a.asset_id AND b.category_type="manufacturer"
				 WHERE a.asset_type="manufacturer" AND a.coupon_id IN ('.$coupon_ids.')
				 
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
					p.product_id AS id,CONCAT(p.product_name," (",p.product_code,")") AS label,p.product_code as sku,p.product_name
				  FROM #__hikashop_product p
				 WHERE 1=1 AND p.product_type="main"
				 '.($is_published ? ' AND p.product_published=1 ' : '').'
				 '.(!empty($product_id) ? ' AND p.product_id IN ('.self::scrubids($product_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(p.product_name," (",p.product_code,")") LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ?'label,p.product_code' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreProductNotGift($product_id=null,$search=null,$limit=null) {
		$db = JFactory::getDBO();
		$sql = 'SELECT p.product_id AS id,CONCAT(p.product_name," (",p.product_code,")") AS label 
				  FROM #__hikashop_product p
				  LEFT JOIN #__awocoupon_giftcert_product g ON g.product_id=p.product_id
				 WHERE p.product_published=1 AND p.product_type="main"
				 '.(!empty($product_id) ? ' AND p.product_id IN ('.self::scrubids($product_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(p.product_name," (",p.product_code,")") LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY label,p.product_code
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
				  FROM #__hikashop_category c
				 WHERE c.category_published=1 AND c.category_type="product"
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
		$rtn = self::categoryListTreeLoop($selectedCategories, $cid, $level, $disabledFields);

		return $rtn;
	}
	static private function categoryListTreeLoop($selectedCategories = array(), $cid = 0, $level = 0, $disabledFields=array()) {
		static $categoryTree = array();
		
		$db = JFactory::getDBO();
		$cid = (int)$cid;
		if(empty($cid)) $cid=1;

		$level++;

		$sql = 'SELECT c.`category_id`, c.`category_description`, c.`category_name`, c.`category_ordering`, c.`category_published`, c.category_id AS category_child_id, c.category_parent_id 
				  FROM #__hikashop_category c
				  WHERE c.`category_published` = 1 AND c.category_type="product" AND c.`category_parent_id` = '. (int)$cid;
		$db->setQuery($sql);
		$records = $db->loadObjectList();

		$selected="";
		if(!empty($records)){
			foreach ($records as $key => $category) {
				if(empty($category->category_child_id)) continue;//$category->category_child_id = $category->category_id;

				$childId = $category->category_child_id;

				if ($childId != $cid) {
					if(in_array($childId, $selectedCategories)) $selected = 'selected=\"selected\"'; else $selected='';
						
						$categoryTree[$childId] = (object) array(
								'category_id'=>$childId,
								'category_name'=>$category->category_name,
								'id'=>$childId,
								'label'=>str_repeat('---', ($level-1) ).$category->category_name,
							);
				}

				
				$sql = 'SELECT category_id FROM #__hikashop_category WHERE category_type="product" AND category_parent_id='.(int)$childId;
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
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS category_id AS id,category_name AS label
				  FROM #__hikashop_category
				 WHERE category_published=1 AND category_type="manufacturer"
				 '.(!empty($manu_id) ? ' AND category_id IN ('.self::scrubids($manu_id).') ' : '').'
				 '.(!empty($search) ? ' AND category_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'category_name,category_id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreVendor($vendor_id=null,$search=null,$limit=null) { return null; }
	static function getEStoreShipping($shipping_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS s.shipping_id AS id, s.shipping_name AS label,s.shipping_type as carrier
				  FROM #__hikashop_shipping s
				 WHERE s.shipping_published=1 
				 '.(!empty($shipping_id) ? ' AND s.shipping_id IN ('.self::scrubids($shipping_id).') ' : '').'
				 '.(!empty($search) ? ' AND s.shipping_name LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 ORDER BY '.(empty($orderby) ? 'label,id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}
	static function getEStoreShopperGroup($shoppergroup_id=null,$search=null,$limit=null) { return null; }
	static function getEStoreUser($user_id=null,$search=null,$limit=null,$limitstart=null,$orderby=null,$orderbydir=null) {
		$db = JFactory::getDBO();

		$limit = (int)$limit;
		$limitstart = (int)$limitstart;
		if(!empty($orderbydir) && strtolower($orderbydir)!='asc' && strtolower($orderbydir!='desc')) $orderbydir = '';

		$sql = 'SELECT SQL_CALC_FOUND_ROWS
						u.id,CONCAT(u.username," - ",u.name) as label,
						u.username,
						IF(ha.address_lastname IS NULL,
								TRIM(SUBSTRING(TRIM(u.name),LENGTH(TRIM(u.name))-LOCATE(" ",REVERSE(TRIM(u.name)))+1)),
								ha.address_lastname) as lastname,
						IF(ha.address_firstname IS NULL,
								TRIM(REVERSE(SUBSTRING(REVERSE(TRIM(u.name)),LOCATE(" ",REVERSE(TRIM(u.name)))+1))),
								ha.address_firstname) as firstname
				  FROM #__users u
				  LEFT JOIN #__hikashop_user hu ON hu.user_cms_id=u.id
				  LEFT JOIN #__hikashop_address ha ON ha.address_user_id=hu.user_id AND ha.address_published=1 AND ha.address_default=1
				 WHERE 1=1
				 '.(!empty($user_id) ? ' AND u.id IN ('.self::scrubids($user_id).') ' : '').'
				 '.(!empty($search) ? ' AND CONCAT(u.username," - ",u.name) LIKE '.$db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $search ) ), true ).'%', false ).' ' : '').' 
				 GROUP BY u.id
				 ORDER BY '.(empty($orderby) ? 'label,u.id' : $orderby).' '.(!empty($orderbydir) ? $orderbydir : '').'
				 '.(!empty($limit) ? ' LIMIT '.(!empty($limitstart) ? $limitstart.',' : '').' '.(int)$limit.' ':'');
		$db->setQuery($sql);
		return $db->loadObjectList('id');
	}

	
	
	static function getOrder($order_id) {
		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM #__hikashop_order WHERE order_id='.(int)$order_id);
		return $db->loadObject();
	}
	
	static function getOrderDetailLink($order_id) { return JRoute::_('index.php?option=com_hikashop&ctrl=order&task=edit&cid[]='.(int)$order_id); }

	
	static function getQueryHistoryOrder($where,$having,$orderby) {
		$sql = 'SELECT go.order_id,go.codes,CONCAT(o.order_id," (",o.order_number,")") AS order_number,"" AS coupon_code
				 FROM #__awocoupon_giftcert_order go
				 LEFT JOIN #__hikashop_order o ON o.order_id=go.order_id
				WHERE go.estore="hikashop"
				'.$orderby
			;
		return $sql;
	}
	static function getQueryHistoryGift($where,$having,$orderby) {
		$sql = 'SELECT c.*,
					 u.id AS user_id,uv.address_firstname AS first_name,uv.address_lastname AS last_name,u.username,
					 o.order_id,o.order_created AS cdate,
					 SUM(au.coupon_discount)+SUM(au.shipping_discount) AS coupon_value_used,
					 c.coupon_value-IFNULL(SUM(au.coupon_discount),0)-IFNULL(SUM(au.shipping_discount),0) AS balance,au.user_email,
					 u.username as _username, uv.address_firstname as _fname, uv.address_lastname as _lname
				 FROM #__awocoupon c
				 LEFT JOIN #__hikashop_order o ON o.order_id=c.order_id
				 LEFT JOIN #__hikashop_address uv ON uv.address_id=o.order_billing_address_id
				 LEFT JOIN #__hikashop_user uu ON uu.user_id=o.order_user_id
				 LEFT JOIN #__users u ON u.id=uu.user_cms_id
				 LEFT JOIN #__awocoupon_history au ON au.coupon_id=c.id
				WHERE c.estore="hikashop" AND c.function_type="giftcert"
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
					 uu.id as use_id,uv.address_firstname AS first_name,uv.address_lastname AS last_name,uu.user_id,u.username,
					 (uu.coupon_discount+uu.shipping_discount) AS discount,uu.productids,uu.timestamp,uu.user_email,
					 ov.order_id,FROM_UNIXTIME(ov.order_created) AS cdate,CONCAT(ov.order_id," (",ov.order_number,")") AS order_number,
					 u.username as _username, uv.address_firstname as _fname, uv.address_lastname as _lname,FROM_UNIXTIME(ov.order_created) AS _created_on
				 FROM #__awocoupon_history uu
				 JOIN #__awocoupon c ON c.id=uu.coupon_id
				 LEFT JOIN #__awocoupon c2 ON c2.id=uu.coupon_entered_id
				 LEFT JOIN #__users u ON u.id=uu.user_id
				 LEFT JOIN #__hikashop_user uh ON uh.user_cms_id=u.id
				 LEFT JOIN #__hikashop_order ov ON ov.order_id=uu.order_id
				 LEFT JOIN #__hikashop_address uv ON uv.address_id=ov.order_billing_address_id
				WHERE uu.estore="hikashop"
				'.$where.'
				GROUP BY uu.id
				'.$having.'
				'.$orderby;
			;
		return $sql;
	}

	static function getQueryAwoUser($coupon_id,$orderby) {
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.startdate,c.expiration,u.user_id as _user_id,
					 if(uv.address_id is NULL,us.name,uv.address_firstname) as _fname,uv.address_lastname as _lname,
					 if(uv.address_id is NULL,us.name,CONCAT(uv.address_firstname," ",uv.address_lastname)) as _name,
					 u.user_id as asset_id
				 FROM #__awocoupon c
				 JOIN #__awocoupon_user u ON u.coupon_id=c.id
				 JOIN #__users us ON us.id=u.user_id
				 LEFT JOIN #__hikashop_user uh ON uh.user_cms_id=us.id
				 LEFT JOIN #__hikashop_order o ON o.order_user_id=uh.user_id
				 LEFT JOIN #__hikashop_address uv ON uv.address_id=o.order_billing_address_id
				WHERE c.id='.(int)$coupon_id.'
				GROUP BY us.id
				'.$orderby
			; 
		return $sql;
	}
	static function getQueryAwoShopperGroup($coupon_id,$orderby) { return null; }
	
	static function getQueryGiftCertProducts($where,$having,$orderby) {
		$sql = 'SELECT g.*,p.product_name as _product_name,p.product_code AS product_sku,pr.title as profile, COUNT(pc.id) as codecount,c.coupon_code
				  FROM #__awocoupon_giftcert_product g
				  LEFT JOIN #__awocoupon c ON c.id=g.coupon_template_id
				  LEFT JOIN #__hikashop_product p ON p.product_id=g.product_id
				  LEFT JOIN #__awocoupon_profile pr ON pr.id=g.profile_id
				  LEFT JOIN #__awocoupon_giftcert_code pc ON pc.product_id=p.product_id
				 WHERE g.estore="hikashop"
				 '.$where.'
				   GROUP BY g.id
				   '.$having.'
				   '.$orderby;
		return $sql;
	}
	static function getQueryGiftCertProduct($product_id) {
		return 'SELECT p.product_name,p.product_code AS product_sku FROM #__hikashop_product p WHERE p.product_id = '.$product_id;
	}
	static function getQueryGiftCertProductCodes($where,$having,$orderby) {
		$sql = 'SELECT g.*,p.product_name as _product_name,p.product_code AS product_sku
				  FROM #__awocoupon_giftcert_code g
				  LEFT JOIN #__hikashop_product p ON p.product_id=g.product_id
				   '.$where.' '.$having.' '.$orderby;
		return $sql;
	}
	

	static function getHistorySentGift($order_id) {
		$db = JFactory::getDBO();
		$sql = 'SELECT i.order_product_id AS order_item_id,i.order_id,i.order_product_price AS product_item_price,i.order_product_quantity AS product_quantity,
						uh.user_cms_id AS user_id,uh.user_email AS email,u.address_firstname AS first_name,u.address_lastname AS last_name,ap.expiration_number,
						ap.expiration_type,ap.coupon_template_id,"" AS order_item_currency,
						ap.profile_id,i.product_id,i.order_product_options AS product_attribute,i.order_product_name AS order_item_name,g.codes
				  FROM #__hikashop_order_product i 
				  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.product_id
				  LEFT JOIN #__hikashop_order o ON o.order_id=i.order_id
				  LEFT JOIN #__hikashop_address u ON u.address_id=o.order_billing_address_id
				  LEFT JOIN #__hikashop_user uh ON uh.user_id=o.order_user_id
				  JOIN #__awocoupon_giftcert_order g ON g.order_id=i.order_id AND g.email_sent=1
				 WHERE i.order_id='.(int)$order_id.' AND ap.published=1
				 GROUP BY i.order_product_id';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	
	
	static function rpt_purchased_giftcert_list($start_date,$end_date,$order_status) {
		$datestr = '';
		if(!empty($start_date) && !empty($end_date)) {
			$datestr = ' AND o.order_created BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND o.order_created >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND o.order_created <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		$initial_list = array();
		$coupon_ids = array();
		$sql = 'SELECT go.codes,
					 uh.user_cms_id AS user_id,uv.address_firstname AS first_name,uv.address_lastname AS last_name,u.username,u.email,
					 o.order_id,o.order_full_price AS order_total,o.order_created AS ocdate,go.codes,
					 (o.order_full_price-o.order_shipping_price) AS order_subtotal,0 AS order_tax,o.order_shipping_price  AS order_shipment,
					 o.order_shipping_tax AS order_shipment_tax,o.order_discount_price*-1 AS order_fee
				 FROM #__awocoupon_giftcert_order go
				 LEFT JOIN #__hikashop_order o ON o.order_id=go.order_id
				 LEFT JOIN #__hikashop_address uv ON uv.address_id=o.order_billing_address_id
				 LEFT JOIN #__hikashop_user uh ON uh.user_id=o.order_user_id

				 LEFT JOIN #__users u ON u.id=uh.user_cms_id
				WHERE go.estore="hikashop"
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
			$datestr = ' AND ov.order_created BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND ov.order_created >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND ov.order_created <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,c.published,
					 uu.coupon_id,uu.coupon_entered_id,c2.coupon_code as coupon_entered_code,
					 uv.address_firstname AS first_name,uv.address_lastname AS last_name,uu.user_id,u.username,
					 (uu.coupon_discount+uu.shipping_discount) AS discount,uu.productids,uu.timestamp,
					 ov.order_id,ov.order_full_price AS order_total,ov.order_created AS cdate,uu.id as num_uses_id,
					 (ov.order_full_price-ov.order_shipping_price) AS order_subtotal,0 AS order_tax,
					 ov.order_shipping_price AS order_shipment,ov.order_shipping_tax AS order_shipment_tax,
					 ov.order_discount_price*-1 AS order_fee
				 FROM #__awocoupon c
				 JOIN #__awocoupon_history uu ON uu.coupon_id=c.id
				 LEFT JOIN #__awocoupon c2 ON c2.id=uu.coupon_entered_id
				 
				 LEFT JOIN #__hikashop_order ov ON ov.order_id=uu.order_id
				 LEFT JOIN #__users u ON u.id=uu.user_id
				 LEFT JOIN #__hikashop_user uh ON uh.user_cms_id=u.id
				 LEFT JOIN #__hikashop_address uv ON uv.address_id=ov.order_billing_address_id
				 
				WHERE c.estore="hikashop"
				 '.$datestr.'
				 '.(!empty($order_status) && is_array($order_status) ? ' AND ov.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$where.'
				 GROUP BY uu.id
				 ORDER BY u.username'
			;
		return $sql;
	}

	static function rpt_history_uses_giftcerts($start_date,$end_date,$order_status,$published) {
			
		$datestr = '';
		if(!empty($start_date) && !empty($end_date)) {
			$datestr = ' AND o.order_created BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND o.order_created >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND o.order_created <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,c.published,
					 u.id as user_id,uv.address_firstname AS first_name,uv.address_lastname AS last_name,u.username,
					 o.order_full_price AS order_total,o.order_created AS cdate,go.codes,
					 (o.order_full_price-o.order_shipping_price) AS order_subtotal,0 AS order_tax,
					 o.order_shipping_price AS order_shipment,o.order_shipping_tax AS order_shipment_tax,o.order_discount_price*-1 AS order_fee,
					 SUM(au.coupon_discount)+SUM(au.shipping_discount) AS coupon_value_used,
					 c.coupon_value-IFNULL(SUM(au.coupon_discount),0)-IFNULL(SUM(au.shipping_discount),0) AS balance
				 FROM #__awocoupon c
				 LEFT JOIN #__hikashop_order o ON o.order_id=c.order_id
				 LEFT JOIN #__hikashop_address uv ON uv.address_id=o.order_billing_address_id
				 LEFT JOIN #__hikashop_user uh ON uh.user_id=o.order_user_id
				 LEFT JOIN #__users u ON u.id=uh.user_cms_id
				 LEFT JOIN #__awocoupon_history au ON au.coupon_id=c.id
				 LEFT JOIN #__awocoupon_giftcert_order go ON go.order_id=o.order_id
				WHERE c.estore="hikashop" AND c.function_type="giftcert"
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
			$datestr = ' AND o.order_created BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND o.order_created >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND o.order_created <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
		

		$sql = 'SELECT c.id, SUM(o.order_full_price) as total, COUNT(c.id) as count
				  FROM #__awocoupon c
				  JOIN (SELECT coupon_entered_id,order_id FROM #__awocoupon_history GROUP BY order_id,coupon_entered_id) uu ON uu.coupon_entered_id=c.id
				  JOIN #__hikashop_order o ON o.order_id=uu.order_id
				 WHERE c.estore="hikashop"
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
			$datestr = ' AND o.order_created BETWEEN '.strtotime($start_date).' AND '.(strtotime($end_date)+(3600*24)-1).' ';
		} elseif(!empty($start_date)) {
			$datestr = ' AND o.order_created >= '.strtotime($start_date).' ';
		} elseif(!empty($end_date)) {
			$datestr = ' AND o.order_created <= '.(strtotime($end_date)+(3600*24)-1).' ';
		}
				
		$sql = 'SELECT c.id,c.coupon_code, SUM(o.order_full_price) as total, COUNT(uu.order_id) as count,uv.address_country as country,uv.address_state as state,uv.address_city AS city,
					 CONCAT(c.id,"-",IF(ISNULL(uv.address_country),"0",uv.address_country),"-",IF(ISNULL(uv.address_state),"0",uv.address_state),"-",uv.address_city) as realid
				  FROM #__awocoupon c
				  JOIN (SELECT coupon_entered_id,order_id FROM #__awocoupon_history GROUP BY order_id,coupon_entered_id) uu ON uu.coupon_entered_id=c.id
				  JOIN #__hikashop_order o ON o.order_id=uu.order_id
				  JOIN #__hikashop_address uv ON uv.address_id=o.order_billing_address_id
				 WHERE c.estore="hikashop"
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$datestr.'
				 '.$where.'
				 GROUP BY c.id,uv.address_country,uv.address_state,uv.address_city';
		$db->setQuery( $sql );
		$order_details = $db->loadAssocList('realid');

		if(empty($order_details)) return;


		$sql = 'SELECT c.id,c.coupon_code, uu.productids,
						SUM(uu.coupon_discount+uu.shipping_discount) as discount,
						uv.address_country as country,uv.address_state as state,uv.address_city AS city
				  FROM #__awocoupon_history uu
				  JOIN #__awocoupon c ON c.id=uu.coupon_entered_id 
				  JOIN #__hikashop_order o ON o.order_id=uu.order_id
				  JOIN #__hikashop_address uv ON uv.address_id=o.order_billing_address_id
				 WHERE c.estore="hikashop"
				 '.(!empty($order_status) && is_array($order_status) ? ' AND o.order_status IN ("'.implode('","',$order_status).'") ' : '').'
				 '.$datestr.'
				 '.$where.'
				 GROUP BY c.id,uv.address_country,uv.address_state,uv.address_city
				 ORDER BY c.coupon_code';
				 
				 
		return (object) array('sql'=>$sql,'order_details'=>$order_details);
	}
	
	static function getOrderStatuses() {
		$db = JFactory::getDBO();
		$sql = 'SELECT category_id AS order_status_id, category_namekey AS order_status_code, category_name AS order_status_name
				  FROM #__hikashop_category
				 WHERE category_type="status" 
				 ORDER BY category_ordering';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}

	static function priceDisplay($amount) {
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		if(!class_exists('hikashop')) require JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php';
		$currencyClass = hikashop_get('class.currency');
		return $currencyClass->format($amount);
	}

}