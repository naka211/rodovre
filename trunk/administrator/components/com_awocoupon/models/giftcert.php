<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelGiftCert extends AwoCouponModel {


	function __construct() {
		$this->_type = 'giftcert';
		parent::__construct();
	}

	function &getEntry() {
		parent::getEntry();

		$this->_entry->product_name = '';
		$this->_entry->product_sku = '';

		if(!empty($this->_entry->id)) {
			if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
			$this->_db->setQuery(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryGiftCertProduct'),$this->_entry->product_id));
			$tmp = $this->_db->loadObject();
			if(!empty($tmp)) {
				$this->_entry->product_name = $tmp->product_name;
				$this->_entry->product_sku = $tmp->product_sku;
			}
		}

		return $this->_entry;
	}
	
	function storeEach($data) {
		$errors = array();
		
		
		// set null fields
		if(empty($data['profile_id'])) $data['profile_id'] = null;
		if(empty($data['expiration_number'])) $data['expiration_number'] = null;
		if(empty($data['expiration_type'])) $data['expiration_type'] = null;
		if(empty($data['vendor_name'])) $data['vendor_name'] = null;
		if(empty($data['vendor_email'])) $data['vendor_email'] = null;
		

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		$row 		= JTable::getInstance($this->_type, 'Table');
		$user		= JFactory::getUser();
		
		// bind it to the table
		if (!$row->bind($data)) {
			$errors[] = $this->_db->getErrorMsg();
		}


		// sanitise fields
		$row->id 			= (int) $row->id;
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


	

	function _buildQuery() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';

		// Get the WHERE, and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$having		= $this->_buildContentHaving();
		$orderby	= $this->_buildContentOrderBy();
		
		return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryGiftCertProducts'),$where,$having,$orderby);

	}

	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcert.filter_order', 	'filter_order', 	'_product_name', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcert.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	function _buildContentWhere() {
		$filter_state 		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcert.filter_state', 		'filter_state', '', 'cmd' );
	
		$where = array();
		

		if ( $filter_state ) {
			$where[] = 'g.published='.(int)$filter_state;
		}
		$where 		= ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	function _buildContentHaving() {
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcert.search', 			'search', 		'', 'string' );
		$search 			= trim(JString::strtolower( $search ) );
	
		$having = array();
		

		if ($search) {
			$search = $this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
			$having[] = ' _product_name LIKE '.$search.' OR c.coupon_code LIKE '.$search;
		}
		$having 		= ( count( $having ) ? ' HAVING ' . implode( ' AND ', $having ) : '' );

		return $having;
	}
	
	
	function getProfileList() {
		$sql = 'SELECT id,title FROM #__awocoupon_profile ORDER BY title,id';
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

}

