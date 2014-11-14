<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelCouponsauto extends AwoCouponModel {


	function __construct() {
		$this->_type = 'couponsauto';
		parent::__construct();
	}

	function &getEntry() {
		parent::getEntry();

		$this->_entry->coupon_code = '';

		if(!empty($this->_entry->id)) {
			$this->_db->setQuery('SELECT coupon_code FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'" AND id='.$this->_entry->coupon_id);
			$this->entry->coupon_code = $this->_db->loadResult();
		}

		return $this->_entry;
	}
	
	function storeEach($data) {
		$errors = array();
		
		// set null fields
		

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		$row 		= JTable::getInstance($this->_type, 'Table');
		$user		= JFactory::getUser();
		
		// bind it to the table
		if (!$row->bind($data)) {
			$errors[] = $this->_db->getErrorMsg();
		}


		$this->_db->setQuery('SELECT MAX(ordering) FROM #__awocoupon_auto');
		$row->ordering = ((int)$this->_db->loadResult()) + 1;
		// sanitise fields
		$row->id 			= (int) $row->id;

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

		// Get the WHERE, and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		
		$sql = 'SELECT a.id,a.coupon_id,a.ordering,a.published,c.coupon_code,c.function_type,
						c.coupon_value_type,c.coupon_value,c.coupon_value_def,c.num_of_uses,c.num_of_uses_type,c.discount_type
				  FROM #__awocoupon_auto a
				  JOIN #__awocoupon c ON c.id=a.coupon_id
				 WHERE c.estore="'.AWOCOUPON_ESTORE.'"
				 '.$where.'
				 '.$orderby;
		return $sql;

	}

	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.couponsauto.filter_order', 	'filter_order', 	'a.ordering', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.couponsauto.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	function _buildContentWhere() {
		$filter_state 		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.couponsauto.filter_state', 		'filter_state', '', 'cmd' );
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.couponsauto.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
	
		$where = array();
		

		if ( $filter_state ) $where[] = 'a.published='.(int)$filter_state;
		if ($search) $where[] = ' LOWER(c.coupon_code) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
		$where 		= ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	
	function getProfileList() {
		$sql = 'SELECT id,title FROM #__awocoupon_profile ORDER BY title,id';
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

}

