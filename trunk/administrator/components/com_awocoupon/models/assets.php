<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelAssets extends AwoCouponModel {
	var $_id = null;
	var $_type = null;

	/**
	 * Constructor
	 **/
	function __construct() {
		$this->_type = 'assets';
		parent::__construct();

		$id		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.assets.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );
		$type	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.assets.type', 	'type', 	JRequest::getVar( 'type' ), 'cmd' );
		$this->mysetId($id,$type);

	}

	/**
	 * Method to set the identifier
	 **/
	function mysetId($id,$type) {
		// Set id and wipe data
		$this->_id	 = $id;
		$this->_type	 = $type;
		$this->_data = null;
	}

	/**
	 * Method to get data
	 **/
	function getData() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {

			$controller = new AwoCouponController( );
			$model_coupon = $controller->getModel('coupon');
			$model_coupon->setId($this->_id);
			$rows      	= & $model_coupon->getEntry();
			$rows->assets = $this->getassets();
			$rtn = $this->getdetailassets($rows);
			if(!empty($rtn)) {
				$rows->asset1 = $rtn->asset1;
				$rows->asset2 = $rtn->asset2;
			}
			$rows->users = $this->getdetailusers($rows);

			$this->_data = $rows;

		}
			
		return $this->_data;
	}

	function getassets() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';

		$orderby	= $this->_buildContentOrderBy();
		return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'1',$this->_id,$orderby);
	
	}
	
	function getdetailassets($it) {
		if($this->_type!='detail') return;
			if (!class_exists( AWOCOUPON_ESTOREHELPER)) require JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';

		$orderby	= $this->_buildContentOrderBy();
		$asset1 = $asset2 = array();

		$asset1 = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'1',$this->_id,$orderby);
		switch($it->function_type) {
			case 'giftcert': 
			case 'shipping': 
			case 'buy_x_get_y':
				$asset2 = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'2',$this->_id);
				break;
		}
		return (object) array('asset1'=>$asset1,'asset2'=>$asset2);
	}
	
	function getdetailusers($it) {
		if(empty($it->user_type) || $this->_type!='detail') return;
		
		
		switch($it->user_type) {
			case 'user' : $sql = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryAwoUser'),$this->_id,''); break;
			case 'usergroup' : $sql = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryAwoShopperGroup'),$this->_id,''); break;
		}
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Method to build the orderby clause of the query
	 **/
	function _buildContentOrderBy() {

		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.assets_'.$this->_type.'.filter_order', 	'filter_order', 	$this->_type=='shipping' ? '' :'asset_name', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.assets_'.$this->_type.'.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= !empty($filter_order) ? ' ORDER BY '.$filter_order.' '.$filter_order_Dir : '';

		return $orderby;
	}

		
	function order_up($post) {
		$sql = 'SELECT asset_id,order_by FROM #__awocoupon_asset1 WHERE asset_type="coupon" AND coupon_id='.(int)$post['id'].' ORDER BY order_by';
		$this->_db->setQuery($sql);
		$results = $this->_db->loadObjectList();
		if(!empty($results)) {
			$children = array();
			foreach($results as $row) $children[] = $row->asset_id;
			
			if($children[0]==$post['order_by']) return true; //already the first element
			
			foreach($children as $k=>$v)
				if($v==$post['order_by']) {
					$exchange_key = $k-1;
					$exchange_value = $children[$exchange_key];
					
					array_splice($children,$exchange_key,1,$post['order_by']);
					array_splice($children,$k,1,$exchange_value);
				}
				
			foreach($children as $i=>$coupon) {
				$sql = 'UPDATE #__awocoupon_asset1 SET order_by='.($i+1).' WHERE asset_type="coupon" AND coupon_id='.(int)$post['id'].' AND asset_id='.$coupon;
				$this->_db->setQuery($sql);
				$this->_db->query();
			}
		}
		return true;
	}
	function order_down($post) {
		$sql = 'SELECT asset_id,order_by FROM #__awocoupon_asset1 WHERE asset_type="coupon" AND coupon_id='.(int)$post['id'].' ORDER BY order_by';
		$this->_db->setQuery($sql);
		$results = $this->_db->loadObjectList();
		if(!empty($results)) {
			$children = array();
			foreach($results as $row) $children[] = $row->asset_id;
			
			if($children[count($children)-1]==$post['order_by']) return true; //already the first element
			
			foreach($children as $k=>$v)
				if($v==$post['order_by']) {
					$exchange_key = $k+1;
					$exchange_value = $children[$exchange_key];
					
					array_splice($children,$exchange_key,1,$post['order_by']);
					array_splice($children,$k,1,$exchange_value);
				}
				
			foreach($children as $i=>$coupon) {
				$sql = 'UPDATE #__awocoupon_asset1 SET order_by='.($i+1).' WHERE asset_type="coupon" AND coupon_id='.(int)$post['id'].' AND asset_id='.$coupon;
				$this->_db->setQuery($sql);
				$this->_db->query();
			}
		}
		return true;
	}
	

}
