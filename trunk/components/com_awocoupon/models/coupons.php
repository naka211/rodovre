<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

defined('_JEXEC') or die( 'Restricted access' );

class AwoCouponModelCoupons extends AwoCouponModel {

	function __construct() {
		parent::__construct();

		$limit		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.limit', 'limit', JFactory::getApplication()->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}
	
	
	function getData() {

		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {
			$this->_data = array();
			$user = JFactory::getUser();
			if(!empty($user->id)) {
				$orderby	= $this->_buildContentOrderBy();
				$cc_codes = $gc_codes = array();

				$current_date = date('Y-m-d H:i:s');
				$sql = 'SELECT u.coupon_id,c.num_of_uses_type,c.num_of_uses
						  FROM #__awocoupon_user u
						  JOIN #__awocoupon c ON c.id=u.coupon_id
						 WHERE c.estore="'.AWOCOUPON_ESTORE.'" AND u.user_id='.$user->id.' AND c.published=1 
						   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
								 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
								 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
								 (c.startdate<="'.$current_date.'"			AND c.expiration>="'.$current_date.'")
							   )'
						; 
				$this->_db->setQuery($sql);
				$rows = $this->_db->loadObjectList('coupon_id');
				if(!empty($rows)) {
					foreach($rows as $row) {
						if(!empty($row->num_of_uses_type) && !empty($row->num_of_uses)) {
							if($row->num_of_uses_type == 'per_user') {
								$userlist = array();		
								$sql = 'SELECT COUNT(id) AS cnt FROM #__awocoupon_history WHERE coupon_id='.$row->coupon_id.' AND user_id='.$user->id.' GROUP BY coupon_id,user_id';
								$this->_db->setQuery($sql);
								$cnt = $this->_db->loadResult();
								if(!empty($cnt) && $cnt>=$row->num_of_uses) unset($rows[$row->coupon_id]);
							}
							elseif($row->num_of_uses_type == 'total') {
								$sql = 'SELECT COUNT(id) FROM #__awocoupon_history WHERE coupon_id='.$row->coupon_id.' GROUP BY coupon_id';
								$this->_db->setQuery($sql);
								$num = $this->_db->loadResult();
								if(!empty($num) && $num>=$row->num_of_uses) unset($rows[$row->coupon_id]);
							}
						}
						
					}
				
				
					if(!empty($rows)) {
						$cc_codes = array_keys($rows);
					}
				}
				
				$this->_db->setQuery('SELECT codes FROM #__awocoupon_giftcert_order WHERE estore="'.AWOCOUPON_ESTORE.'" AND user_id='.(int)$user->id.' ORDER BY order_id DESC'); 
				$rows = $this->_db->loadObjectList(); 
				foreach ($rows as $row) {
					$a3 = array();  
					@parse_str($row->codes, $a3); 
					if (!empty($a3)) {
						foreach ($a3 as $code) { if (!empty($code['c'])) $gc_codes[] = $code['c']; }
					}
				} 
				if(!empty($gc_codes)) {
					$sql = 'SELECT c.id
							  FROM #__awocoupon c
							 WHERE c.estore="'.AWOCOUPON_ESTORE.'" AND c.published=1 AND c.coupon_code IN ("'.implode('","',$gc_codes).'")
							   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
									 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
									 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
									 (c.startdate<="'.$current_date.'"			AND c.expiration>="'.$current_date.'")
								   )'
							; 
					$this->_db->setQuery($sql);
					$gc_codes = version_compare( JVERSION, '3.0.0', 'ge' )
								? $this->_db->loadColumn()
								: $this->_db->loadResultArray();
								
				}
				
				
				
				
				
				
				
				
				$current_date = date('Y-m-d H:i:s');
				$sql = 'SELECT i.coupon_id,c.num_of_uses_type,c.num_of_uses
						  FROM #__awocoupon c
						  JOIN #__awocoupon_image i ON i.coupon_id=c.id
						 WHERE c.estore="'.AWOCOUPON_ESTORE.'" AND i.user_id='.$user->id.' AND c.published=1 
						   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
								 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
								 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
								 (c.startdate<="'.$current_date.'"			AND c.expiration>="'.$current_date.'")
							   )'
						; 
				$this->_db->setQuery($sql);
				$rows = $this->_db->loadObjectList('coupon_id');
				if(!empty($rows)) {
					$cc_codes = array_keys($rows);
				}
				
				
				
				
				if(!empty($cc_codes) || !empty($gc_codes)) {
					$sql = 'SELECT c.id,c.function_type,c.coupon_code,c.coupon_value_type,c.coupon_value,c.startdate,c.expiration,i.filename
							  FROM #__awocoupon c
							  LEFT JOIN #__awocoupon_image i ON i.coupon_id=c.id
							 WHERE c.id IN ('.implode(',',array_merge($gc_codes,$cc_codes)).') '.$orderby;
					$this->_db->setQuery($sql);
					$final_rows = $this->_db->loadObjectList('id');
					
					
					// get gift cert balance
					if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
					$this->_db->setQuery(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryHistoryGift'),' AND c.id IN ('.implode(',',array_merge($gc_codes,$cc_codes)).') ','',''));
					$giftcards = $this->_db->loadObjectList('id');

					
					foreach($final_rows as $i=>$row) {
					
						$price = '';
						if(!empty($row->coupon_value))
							$price = $row->coupon_value_type=='amount' 
											? call_user_func(array(AWOCOUPON_ESTOREHELPER,'priceDisplay'),$row->coupon_value)
											: round($row->coupon_value).'%';
						$final_rows[$i]->str_coupon_value = $price;
		
		
					
					
					
						if(!empty($giftcards[$i])) {
							$final_rows[$i]->balance = $giftcards[$i]->balance;
							$final_rows[$i]->str_balance = call_user_func(array(AWOCOUPON_ESTOREHELPER,'priceDisplay'),$giftcards[$i]->balance);
						}
						$full_filename = JPATH_SITE.'/media/com_awocoupon/customers/'.$user->id.'/'.$row->filename.'.php';
						if (!file_exists($full_filename)) $final_rows[$i]->filename = '';
					}
										
					
					$this->_data = $final_rows;
				}
				
			}
			
			
			

		}
		return $this->_data;
	}
	
	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order', 	'filter_order', 	'coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	
}
