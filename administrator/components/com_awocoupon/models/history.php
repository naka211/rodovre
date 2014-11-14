<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelHistory extends AwoCouponModel {

	function __construct() {
		$this->_type = 'history';
		parent::__construct();
	}


	function &getEntry() {
		parent::getEntry();

		$this->_entry->username = '';
		$this->_entry->order_id = '';

		return $this->_entry;
	}
	


	function storeEach($data) {
		$errors = array();
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// set null fields
		$data['coupon_entered_id'] = null;
		$data['productids'] = null;
		if(empty($data['coupon_discount'])) $data['coupon_discount'] = 0;
		if(empty($data['shipping_discount'])) $data['shipping_discount'] = 0;
		if(empty($data['order_id'])) $data['order_id'] = null;
		
		$sql = 'SELECT id FROM #__users WHERE username="'.$data['username'].'"';
		$this->_db->setQuery($sql);
		$data['user_id'] = $this->_db->loadResult();
		
		if(!empty($data['order_id'])) {
			if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
			$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getOrder'),$data['order_id']);
			if(empty($tmp)) {
				$errors[] = JText::_('COM_AWOCOUPON_GC_ORDER_NUM').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			}
		}

		$row 		= JTable::getInstance('history', 'Table');
		$user		= JFactory::getUser();
		
		// bind it to the table
		if (!$row->bind($data)) {
			$errors[] = $this->_db->getErrorMsg();
		}

		$row->estore = AWOCOUPON_ESTORE;

		// Make sure the data is valid
		if (!$row->check()) {
			foreach($row->getErrors() as $err) $errors[] = $err;
		}

		// take a break and return if there are any errors
		if(!empty($errors)) return $errors;
		
		
		// Store the entry to the database
		if (!$row->store(true)) {
			$errors[] = $this->_db->stderr();
			return $errors;
		}

		// clean out the products/users tables
		if(!empty($row->id)) {
		}
		
				
		$this->_entry	=& $row;
		
		return;
	}
	
	
	
	
	
	function getCouponList() {
		$query = 'SELECT id,coupon_code,id as dd_id,coupon_code as dd_name 
				  FROM #__awocoupon 
				 WHERE estore="'.AWOCOUPON_ESTORE.'" AND published=1 AND function_type!="parent"
				 ORDER BY coupon_code,id';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('id');
	}
	
	


	/**
	 * Method to build the query
	 **/
	function _buildQuery() {

		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		switch($this->_layout) {
			case 'order': return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryHistoryOrder'),'','',$this->_buildContentOrderByOrder()); break;
			case 'gift': return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryHistoryGift'),$this->_buildContentWhereGift(),$this->_buildContentHavingGift(),$this->_buildContentOrderByGift()); break;
			default: return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryHistoryCoupon'),$this->_buildContentWhere(),$this->_buildContentHaving(),$this->_buildContentOrderBy()); break;
		}
	}
	
	

	function _buildContentWhere() {
		$filter_state 		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.filter_state', 		'filter_state', '', 'cmd' );
		$filter_coupon_value_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
		$filter_discount_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
		$filter_function_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
		$search_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.search_type', 		'search_type', 		'', 'cmd' );
	
		$where = array();
		
		if ( $filter_state ) {
			if($filter_state==1) {
				$current_date = date('Y-m-d H:i:s');
				$where[] = 'c.published=1 
				   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
						 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
						 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
						 (c.startdate<="'.$current_date.'"			AND c.expiration>="'.$current_date.'")
					   )
				'; 
			}
			elseif($filter_state==-1) {
				$current_date = date('Y-m-d H:i:s');
				$where[] = '(c.published=-1 OR c.startdate>"'.$current_date.'" OR c.expiration<"'.$current_date.'")';
			}
			else $where[] = 'c.published='.(int)$filter_state;
		}
		if ( $filter_coupon_value_type ) $where[] = 'c.coupon_value_type = \''.$filter_coupon_value_type.'\'';
		if ( $filter_discount_type ) $where[] = 'c.discount_type = \''.$filter_discount_type.'\'';
		if ( $filter_function_type ) $where[] = 'c.function_type = \''.$filter_function_type.'\'';
		if ($search) {
			if($search_type == 'coupon') $where[] = 'LOWER(c.coupon_code) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );

		}

		$where 		= ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	function _buildContentHaving() {
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
		$search_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.search_type', 		'search_type', 		'', 'cmd' );
	
		$having = array();
		
		if ($search) {
			if($search_type == 'user') $having[] = 'LOWER(_username) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
			elseif($search_type == 'last') $having[] = 'LOWER(_lname) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
			elseif($search_type == 'first') $having[] = 'LOWER(_fname) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );

		}

		$having 		= ( count( $having ) ? ' HAVING ' . implode( ' AND ', $having ) : '' );

		return $having;
	}
	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyscoupon.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	function _buildContentWhereGift() {
		$filter_state 		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.filter_state', 	'filter_state', '', 'cmd' );
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
		$search_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.search_type', 		'search_type', 		'', 'cmd' );
	
		$where = array();
		
		if ( $filter_state ) {
			if($filter_state==1) {
				$current_date = date('Y-m-d H:i:s');
				$where[] = 'c.published=1 
				   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
						 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
						 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
						 (c.startdate<="'.$current_date.'"			AND c.expiration>="'.$current_date.'")
					   )
				'; 
			}
			elseif($filter_state==-1) {
				$current_date = date('Y-m-d H:i:s');
				$where[] = '(c.published=-1 OR c.startdate>"'.$current_date.'" OR c.expiration<"'.$current_date.'")';
			}
		}
		if ($search) {
			if($search_type == 'coupon') $where[] = 'LOWER(c.coupon_code) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
		}

		$where 		= ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	function _buildContentHavingGift() {
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
		$search_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.search_type', 		'search_type', 		'', 'cmd' );
	
		$having = array();
		
		if ($search) {
			if($search_type == 'user') $having[] = 'LOWER(_username) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
			elseif($search_type == 'last') $having[] = 'LOWER(_lname) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
			elseif($search_type == 'first') $having[] = 'LOWER(_fname) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
		}

		$having 		= ( count( $having ) ? ' HAVING ' . implode( ' AND ', $having ) : '' );

		return $having;
	}
	function _buildContentOrderByGift() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.filter_order', 	'filter_order', 	'c.id', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historygift.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}


	function _buildContentOrderByOrder() {

		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyorder.filter_order', 	'filter_order', 	'go.order_id', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.historyorder.filter_order_Dir',	'filter_order_Dir',	'desc', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}


	
	function resend_giftcert($order_id) {
		
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.AWOCOUPON_ESTORE.'/giftcerthandler.php';
		
		$params = new awoParams();

		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		$db  = JFactory::getDBO();

		$rows = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getHistorySentGift'),$order_id);

		$codes = array();
		foreach($rows as $k=>$row) {
			if(empty($codes)) {
				@parse_str($row->codes,$codes);
				if(empty($codes[0]['i']) && (count($codes)!=1 || count($rows)!=1)) {
					JFactory::getApplication()->enqueueMessage(JText::_('ERROR'), 'error');
					return false;
				}
			}
			
			$coupons = array();
			for($i=0; $i<$row->product_quantity; $i++) {
				foreach($codes as $k2=>$code) {
					//if($code['p'] == $row->product_id) {
					if((	!empty($code['i']) && $code['i']==$row->order_item_id && $code['p']==$row->product_id)
						|| (count($codes)==1 && $code['p']==$row->product_id)) {
						
						$db->setQuery('SELECT id,coupon_code,expiration,coupon_value,coupon_value_type FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'" AND coupon_code="'.$code['c'].'"');
						$coupon_row = $db->loadObject();
						if(empty($coupon_row)) break;
						
						$price = '';
						if(!empty($coupon_row->coupon_value))
							$price = $coupon_row->coupon_value_type=='amount' 
											? $coupon_row->coupon_value
											: round($coupon_row->coupon_value).'%';
							
						$coupons[] = array(
							'id'=>$coupon_row->id,
							'order_item_id'=>$row->order_item_id,
							'user_id'=>$row->user_id,
							'product_id'=>$row->product_id,
							'product_name'=>$row->order_item_name,
							'email'=>$row->email,
							'code'=>$coupon_row->coupon_code,
							'price'=>$price,
							'currency'=>$row->order_item_currency,
							'expiration'=>$coupon_row->expiration,
							'expirationraw'=>!empty($coupon_row->expiration) ? strtotime($coupon_row->expiration) : 0,
							'profile'=>'',
							'file'=>'',						
						);
						
						$codes[$k2]['p'] = -999;
						break;
					}
				}
			}
			
			$rows[$k]->coupons = $coupons;
		
		}
		if(empty($rows)) return false;
		 
		$order = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getOrder'),$order_id);
//printr($order);
//print_r($rows);exit;
		
		return call_user_func(array('AwoCoupon'.AWOCOUPON_ESTORE.'GiftcertHandler','process_resend'),$order,$rows);

		//$giftclass_name = 'AwoCoupon'.AWOCOUPON_ESTORE.'GiftcertHandler';
		//$giftclass = new $giftclass_name();
		//$giftclass->order = $giftclass->get_storeorder($order);
		//return $giftclass->generate_auto_email($rows);
		
		
		//return call_user_func(array('AwoCoupon'.AWOCOUPON_ESTORE.'GiftcertHandler','generate_auto_email'),$order,$rows,$params,false);
	}	

	
	
	
	
	
	

}

