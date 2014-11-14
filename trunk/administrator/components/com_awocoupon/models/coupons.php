<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelCoupons extends AwoCouponModel {
	var $_pagination = null;
	var $_id = null;

	/**
	 * Constructor
	 **/
	function __construct() {
		$this->_type = 'coupons';
		parent::__construct();

		$limit		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.limit', 'limit', JFactory::getApplication()->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

	}

	/**
	 * Method to set the identifier
	 **/
	function setId($id) {
		// Set id and wipe data
		$this->_id	 = $id;
		$this->_data = null;
	}

	/**
	 * Method to get data
	 **/
	function getData() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			
			$ids = '';
			$ptr = null;
			foreach($this->_data as $i=>$row) {
				$this->_data[$i]->is_editable = empty($this->_data[$i]->order_id) ? true : false;
				$this->_data[$i]->params = json_decode($this->_data[$i]->params);
				
				$ids .= $row->id.',';
				$ptr[$row->id]['usercount'] = &$this->_data[$i]->usercount;
				$ptr[$row->id]['usergroupcount'] = &$this->_data[$i]->usergroupcount;
				$ptr[$row->id]['asset1count'] = &$this->_data[$i]->asset1count;
				$ptr[$row->id]['asset2count'] = &$this->_data[$i]->asset2count;
			}

			if(!empty($ids)) {
				$ids = substr($ids,0,-1);
				$sql = 'SELECT coupon_id,count(user_id) as cnt FROM #__awocoupon_user WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['usercount'] = $tmp->cnt;

				$sql = 'SELECT coupon_id,count(shopper_group_id) as cnt FROM #__awocoupon_usergroup WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['usergroupcount'] = $tmp->cnt;
				
				$sql = 'SELECT coupon_id,asset_type,count(asset_id) as cnt FROM #__awocoupon_asset1 WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id,asset_type';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['asset1count'] = $tmp->cnt;

				$sql = 'SELECT coupon_id,asset_type,count(asset_id) as cnt FROM #__awocoupon_asset2 WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id,asset_type';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['asset2count'] = $tmp->cnt;

			}
		}
			
		return $this->_data;
	}

	/**
	 * Method to get the total
	 **/
	function getTotal() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object
	 **/
	function getPagination() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query
	 **/
	function _buildQuery() {
		// Get the WHERE, and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses_type,c.num_of_uses,c.coupon_value_type,
						c.coupon_value,c.coupon_value_def,
						c.min_value,c.discount_type,c.user_type,c.function_type,c.startdate,c.expiration,c.order_id,c.published,
						0 as usercount,0 as asset1count,0 as asset2count,c.exclude_special,c.exclude_giftcert,c.note,
						c.parent_type,c.params
				 FROM #__awocoupon c
				WHERE estore="'.AWOCOUPON_ESTORE.'"
					'. $where.'
				GROUP BY c.id '
					. $orderby;

		return $sql;
	}

	/**
	 * Method to build the orderby clause of the query
	 **/
	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to build the where clause of the query
	 **/
	function _buildContentWhere() {

		$filter_state 		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_state', 		'filter_state', '', 'cmd' );
		$filter_coupon_value_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
		$filter_discount_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
		$filter_function_type = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
		$filter_template = (int)JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.filter_template', 		'filter_template', 		'', 'cmd' );
		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.coupons.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
	
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
		if ( $filter_template ) $where[] = '(c.id='.$filter_template.' OR c.template_id='.$filter_template.')';
		//if ($search) $where[] = ' LOWER(c.coupon_code) LIKE '.$this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
		if ($search) {
			$s = $this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
			$where[] = ' (LOWER(c.coupon_code) LIKE '.$s.' OR c.note LIKE '.$s.') ';
		}

		$where 		= ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	/**
	 * Method to (un)publish
	 **/
	function publish($cid = array(), $publish = 1) {
		$user 	= JFactory::getUser();

		if (count( $cid )) {
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__awocoupon SET published = '.(int)$publish.' WHERE id IN ('. $cids .')';
			$this->_db->setQuery( $query );
		
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return $cid;
	}
	
	/**
	 * Method to remove
	 **/
	function delete($cids) {		
		
		$cids = implode( ',', $cids );

		$query = 'DELETE FROM #__awocoupon_usergroup WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__awocoupon_user WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__awocoupon_history WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__awocoupon_asset2 WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}
		$query = 'DELETE FROM #__awocoupon_asset1 WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__awocoupon WHERE id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		return $this->_db->getAffectedRows().' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED');
	}
	
	function getTemplateList() {
		$sql = 'SELECT id,coupon_code FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'" AND published=-2 ORDER BY coupon_code,id';
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('id');
	}
	
}

