<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelGiftcertcode extends AwoCouponModel {

	function __construct() {
		$this->_type = 'giftcertcode';
		parent::__construct();

	}


	//function store($product_id, $data, $store_none_errors) {
	function store($entry) {
	
		$error_check_only = $entry['store_none_errors'] ? false : true;

		$product_id = (int)$entry['product_id'];
		$data = $entry['data'];
		$arrdistinctcodes = array();
		foreach($data  as $row) {
			@$arrdistinctcodes[$row[1]]++;
		}
		
		
		$_map = array(
			'status' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_ACTIVE' )))=>'active',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_INACTIVE' )))=>'inactive',
			),
		);
		$sql = 'SELECT code FROM #__awocoupon_giftcert_code WHERE product_id='.$product_id;
		$this->_db->setQuery($sql);
		$_map_code = $this->_db->loadObjectList('code');
		

		$errors = array();
		$datadistinct = array();
		foreach($data as $row) {
		// first level error checking
			$id = $row[0];
			if(trim($id) == '') {
				$errors['          '][] = JText::_('COM_AWOCOUPON_IMP_NO_ID');
				continue;
			}
			if(isset($datadistinct[$id])) {
				$errors[$id][] = JText::_('COM_AWOCOUPON_ERR_DUPLICATE_CODE');
				continue;
			}
			$datadistinct[$id] = $row;
			if(count($row)<2) {
				$errors[$id][] = JText::_('COM_AWOCOUPON_IMP_COLUMNS_INVALID');
				continue;
			}
			$row = array_pad($row, 3, '');

			if(empty($row[0])) $errors[$id][] = JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM');
			elseif(isset($_map_code[$row[0]])) $errors[$id][] = JText::_('COM_AWOCOUPON_ERR_DUPLICATE_CODE');
			
			$datadistinct[$id][1] = $row[1] = trim(strtolower($row[1]));
			if(empty($_map['status'][$row[1]])) $errors[$id][] = JText::_('COM_AWOCOUPON_RPT_STATUS').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			$datadistinct[$id][2] = trim($row[2]);
		}

//printr($datadistinct);
		if($error_check_only && !empty($errors)) return $errors;
		
		$sql_arr = array();
		foreach($datadistinct as $id=>$row) {
			if(empty($errors[$id])) {
				$sql_arr[] = '("'.AWOCOUPON_ESTORE.'",'.$product_id.',"'.$id.'","'.$_map['status'][$row[1]].'","'.$row[2].'")';
			}
		}	
		
		if(!empty($sql_arr)) {
			for($i=0; $i<count($sql_arr); $i=$i+300) {
				$sql = 'INSERT INTO #__awocoupon_giftcert_code (estore,product_id,code,status,note) VALUES '.implode(',',array_slice($sql_arr,$i,300));
				$this->_db->setQuery($sql);
				$this->_db->query();
			}
		}
		return $errors;		
	}		


	
	
	
	
	function _buildQuery() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';

		// Get the WHERE, and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$having		= $this->_buildContentHaving();
		$orderby	= $this->_buildContentOrderBy();
		return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryGiftCertProductCodes'),$where,$having,$orderby);
	}

	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_order', 	'filter_order', 	'_product_name', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	function _buildContentWhere() {

		$filter_state 		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_state', 		'filter_state', '', 'cmd' );
		$filter_product 		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.filter_product', 		'filter_product', '', 'cmd' );
	
		$where = array();
		

		if ( $filter_state ) { $where[] = 'g.status="'.$filter_state.'"'; }
		if ( $filter_product ) { $where[] = 'g.product_id="'.(int)$filter_product.'"'; }

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	function _buildContentHaving() {

		$search 			= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.giftcertcode.search', 			'search', 		'', 'string' );
		$search 			= awoLibrary::dbEscape( trim(JString::strtolower( $search ) ) );
	
		$having = array();
		

		if ($search) {
			$s = $this->_db->Quote( '%'.awoLibrary::dbEscape( $search, true ).'%', false );
			$having[] = ' LOWER(_product_name) LIKE '.$s.' OR LOWER(g.note) LIKE '.$s;
		}

		$having 		= ( count( $having ) ? ' HAVING ' . implode( ' AND ', $having ) : '' );

		return $having;
	}
	
	
	
	function publish($cids = array(), $publish = 1) {
		$row 		= JTable::getInstance($this->_type, 'Table');
		$tablename = $row->getTableName();
		$keyname = $row->getKeyName();
		foreach($cids as $k=>$i) $cids[$k] = (int)$i;
		$publish = (int)$publish;
		$publish = $publish==1 ? 'active' : 'inactive';

		if (count( $cids )) {
			$query = 'UPDATE '.$this->_db->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'quoteName' : 'nameQuote'}($tablename).' SET status = "'.awoLibrary::dbEscape($publish).'" WHERE '.$keyname.' IN ('. implode( ',', $cids ) .')';
			$this->_db->setQuery( $query );
		
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}	
	
	
	
	
	
	
	function getGiftCertProductList() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		$this->_db->setQuery(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryGiftCertProducts'),'','','ORDER BY _product_name,g.product_id'));
		return $this->_db->loadObjectList();

	}


	
}
