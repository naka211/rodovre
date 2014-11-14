<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelreports extends AwoCouponModel {
	var $_data = null;
	var $_total = 0;

	function __construct() {

		$this->_type = 'reports';
		parent::__construct();
		
		$this->report_type		= JRequest::getVar( 'report_type' );

		
		// Get pagination request variables
		$limit		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.limit', 'limit', JFactory::getApplication()->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);


	}


	function getData() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {
						
			if($this->report_type == 'coupon_list') $this->rpt_coupon_list();
			elseif($this->report_type == 'purchased_giftcert_list') $this->rpt_purchased_giftcert_list();
			elseif($this->report_type == 'coupon_vs_total') $this->rpt_coupon_vs_total();
			elseif($this->report_type == 'coupon_vs_location') $this->rpt_coupon_vs_location();
			elseif($this->report_type == 'history_uses_coupons') $this->rpt_history_uses_coupons();
			elseif($this->report_type == 'history_uses_giftcerts') $this->rpt_history_uses_giftcerts();


		}

		return $this->_data;
	}

	
	/**
	 * Reports
	 **/

	function rpt_coupon_list($force_all = false) {
		global $AWOCOUPON_lang;
		
		$this->_data = null;
		$function_type		= JRequest::getVar( 'function_type' );
		$coupon_value_type	= JRequest::getVar( 'coupon_value_type' );
		$discount_type		= JRequest::getVar( 'discount_type' );
		$template			= (int)JRequest::getVar( 'templatelist' );
		$published			= JRequest::getVar( 'published' );
		
		$coupon_ids = array();
		$sql = 'SELECT *
				  FROM #__awocoupon
				 WHERE estore="'.AWOCOUPON_ESTORE.'"
				 '.(!empty($function_type) ? 'AND function_type="'.$function_type.'" ' : '').'
				 '.(!empty($coupon_value_type) ? 'AND coupon_value_type="'.$coupon_value_type.'" ' : '').'
				 '.(!empty($discount_type) ? 'AND discount_type="'.$discount_type.'" ' : '').'
				 '.(!empty($template) ? 'AND template_id="'.$template.'" ' : '').'
				 '.(!empty($published) ? 'AND published="'.$published.'" ' : '')
				 ;
		$this->_db->setQuery( $sql );
		$rtn = $this->_db->loadAssocList();
		$this->_total = count($rtn);
		
		if(!$force_all && $this->getState('limit')!=0) $rtn = array_slice($rtn,$this->getState('limitstart'),$this->getState('limit'));
		foreach($rtn as $row) {
			$coupon_ids[] = $row['id'];
			$row['params'] = json_decode($row['params']);
			$row['userlist'] = $row['userliststr'] 
			= $row['usergrouplist'] = $row['usergroupliststr'] 
			= $row['aseet1list'] = $row['asset1liststr'] 
			= $row['asset2list'] = $row['asset2liststr'] 
				= array();

			$row['str_function_type'] = !empty($row['function_type']) ? $AWOCOUPON_lang['function_type'][$row['function_type']] : '';
			$row['str_published'] = !empty($row['published']) ? $AWOCOUPON_lang['published'][$row['published']] : '';
			$row['str_coupon_value_type'] = !empty($row['coupon_value_type']) ? $AWOCOUPON_lang['coupon_value_type'][$row['coupon_value_type']] : '';
			$row['str_discount_type'] = !empty($row['discount_type']) ? $AWOCOUPON_lang['discount_type'][$row['discount_type']] : '';
			$row['str_user_type'] = !empty($row['user_type']) ? $AWOCOUPON_lang['user_type'][$row['user_type']] : '';
			$row['str_user_mode'] = !empty($row['params']->user_mode) ? $AWOCOUPON_lang['asset_mode'][$row['params']->user_mode] : '';
			$row['str_num_of_uses_type'] = !empty($row['num_of_uses']) ? $AWOCOUPON_lang['num_of_uses_type'][$row['num_of_uses_type']] : '';
			$row['str_shipping_module'] = !empty($row['shipping_module']) ? $AWOCOUPON_lang['shipping_module'][$row['shipping_module']] : '';
			$row['str_startdate'] = str_replace(array(':','-'),'',$row['startdate']);
			$row['str_expiration'] = str_replace(array(':','-'),'',$row['expiration']);
			$row['str_coupon_value'] = !empty($row['coupon_value']) ? round($row['coupon_value'],2) : '';
			$row['str_min_value_type'] = !empty($row['min_value']) && !empty($row['params']->min_value_type) ? $AWOCOUPON_lang['min_value_type'][$row['params']->min_value_type] : '';
			$row['str_min_value'] = !empty($row['min_value']) ? round($row['min_value'],2) : '';
			$row['str_exclude_special'] = JText::_(!empty($row['exclude_special']) ? 'COM_AWOCOUPON_GBL_YES' : 'COM_AWOCOUPON_GBL_NO');
			$row['str_exclude_giftcert'] = JText::_(!empty($row['exclude_giftcert']) ? 'COM_AWOCOUPON_GBL_YES' : 'COM_AWOCOUPON_GBL_NO');
			$row['str_asset'] = '';
			$row['str_assetstr'] = '';
			$row['str_asset2'] = '';
			$row['str_assetstr2'] = '';
			$row['str_asset1_type'] = !empty($row['params']->asset1_type) ? $AWOCOUPON_lang['asset_type'][$row['params']->asset1_type] : '';
			$row['str_asset2_type'] = !empty($row['params']->asset2_type) ? $AWOCOUPON_lang['asset_type'][$row['params']->asset2_type] : '';
			$row['str_asset1_qty'] = !empty($row['params']->asset1_qty) ? $row['params']->asset1_qty : '';
			$row['str_asset2_qty'] = !empty($row['params']->asset2_qty) ? $row['params']->asset2_qty : '';
			$row['str_asset1_mode'] = !empty($row['params']->asset1_mode) ? $AWOCOUPON_lang['asset_mode'][$row['params']->asset1_mode] : '';
			$row['str_asset2_mode'] = !empty($row['params']->asset2_mode) ? $AWOCOUPON_lang['asset_mode'][$row['params']->asset2_mode] : '';
			$row['str_max_discount_qty'] = !empty($row['params']->max_discount_qty) ? $row['params']->max_discount_qty : '';
			$row['str_process_type'] = !empty($row['params']->buy_xy_process_type) 
											? $AWOCOUPON_lang['buy_xy_process_type'][$row['params']->buy_xy_process_type]
											: ($row['function_type']=='parent' && !empty($row['parent_type']) ? $AWOCOUPON_lang['parent_type'][$row['parent_type']] : '');
			$row['str_product_match'] = $row['function_type']=='buy_x_get_y' 
											? (empty($row['params']->product_match) ? JText::_('COM_AWOCOUPON_GBL_NO') : JText::_('COM_AWOCOUPON_GBL_YES'))
											: '';
			$row['str_addtocart'] = $row['function_type']=='buy_x_get_y' 
											? (empty($row['params']->addtocart) ? JText::_('COM_AWOCOUPON_GBL_NO') : JText::_('COM_AWOCOUPON_GBL_YES'))
											: '';
			$row['str_note'] = empty($row['note']) ? '' : $row['note'];
			
			$this->_data->rows[$row['id']] = $row;
		}
		
		if(!empty($coupon_ids)) {
			if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
			
			$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoUser'),$coupon_ids);
			foreach($tmp as $row) {
				$this->_data->rows[$row->coupon_id]['userlist'][] = $row->user_id;
				$this->_data->rows[$row->coupon_id]['userliststr'][] = $row->user_name;
			}
			
			$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoShopperGroup'),$coupon_ids);
			if(!empty($tmp)) {
				foreach($tmp as $row) {
					$this->_data->rows[$row->coupon_id]['usergrouplist'][] = $row->user_id;
					$this->_data->rows[$row->coupon_id]['usergroupliststr'][] = $row->user_name;
				}
			}
			
			
			
			

			$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'1',$coupon_ids);
			foreach($tmp as $row) {
				$this->_data->rows[$row->coupon_id]['asset1list'][] = $row->asset_id;
				$this->_data->rows[$row->coupon_id]['asset1liststr'][] = $row->asset_name;
			}
			$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'2',$coupon_ids);//printr($coupon_ids);printrx($tmp);
			foreach($tmp as $row) {
				$this->_data->rows[$row->coupon_id]['asset2list'][] = $row->asset_id;
				$this->_data->rows[$row->coupon_id]['asset2liststr'][] = $row->asset_name;
			}
			
			
			
			foreach($this->_data->rows as $k=>$row) {
				if($this->_data->rows[$k]['user_type']=='user' && !empty($this->_data->rows[$k]['userlist'])) {
					$this->_data->rows[$k]['str_userlist'] = implode(',',$this->_data->rows[$k]['userlist']);
					$this->_data->rows[$k]['str_userliststr'] = implode(',',$this->_data->rows[$k]['userliststr']);
				} elseif($this->_data->rows[$k]['user_type']=='usergroup' && !empty($this->_data->rows[$k]['usergrouplist'])) {
					$this->_data->rows[$k]['str_userlist'] = implode(',',$this->_data->rows[$k]['usergrouplist']);
					$this->_data->rows[$k]['str_userliststr'] = implode(',',$this->_data->rows[$k]['usergroupliststr']);
				} else {
					$this->_data->rows[$k]['str_user_type'] 
						= $this->_data->rows[$k]['str_userlist'] 
						= $this->_data->rows[$k]['str_userliststr'] 
						= '';
				}
				
				if(!empty($this->_data->rows[$k]['asset1list'])) {
					$this->_data->rows[$k]['str_asset'] = implode(',',$this->_data->rows[$k]['asset1list']);
					$this->_data->rows[$k]['str_assetstr'] = implode(',',$this->_data->rows[$k]['asset1liststr']);
				}
				if(!empty($this->_data->rows[$k]['asset2list'])) {
					$this->_data->rows[$k]['str_asset2'] = implode(',',$this->_data->rows[$k]['asset2list']);
					$this->_data->rows[$k]['str_assetstr2'] = implode(',',$this->_data->rows[$k]['asset2liststr']);
				}
				if(empty($this->_data->rows[$k]['str_asset'])) $this->_data->rows[$k]['str_asset1_mode']='';
					
			}
		}
		
		
	}
	
	function rpt_purchased_giftcert_list($force_all = false) {
		global $AWOCOUPON_lang;
		
		$this->_data = null;
		$published			= JRequest::getVar( 'published' );
		$start_date			= JRequest::getVar( 'start_date' );
		$end_date			= JRequest::getVar( 'end_date' );
		$order_status		= JRequest::getVar( 'order_status' );
		$giftcert_product	= (int)JRequest::getVar( 'giftcert_product' );
		
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		$this->_db->setQuery( call_user_func(array(AWOCOUPON_ESTOREHELPER,'rpt_purchased_giftcert_list'),$start_date,$end_date,$order_status) );

		
		$ptr = null;
		foreach($this->_db->loadAssocList() as $row) {
			$row['product_id'] = 0;
			$row['product_name'] = '';
			$row['order_date'] = !empty($row['cdate']) ? date('Y-m-d',$row['cdate']) : '';
			$row['order_number'] = !empty($row['order_id']) ? sprintf("%08d", $row['order_id']) : '';
			$row['order_total'] = !empty($row['order_total']) ? number_format($row['order_total'],2) : '';
			$row['order_subtotal'] = !empty($row['order_subtotal']) ? number_format($row['order_subtotal'],2) : '';
			$row['order_tax'] = !empty($row['order_tax']) ? number_format($row['order_tax'],2) : '';
			$row['order_shipment'] = !empty($row['order_shipment']) ? number_format($row['order_shipment'],2) : '';
			$row['order_shipment_tax'] = !empty($row['order_shipment_tax']) ? number_format($row['order_shipment_tax'],2) : '';
			$row['order_fee'] = !empty($row['order_fee']) ? number_format($row['order_fee'],2) : '';

			@parse_str($row['codes'],$codes);
			if(!empty($codes[0]['p'])) {
				foreach($codes AS $code) {
					$row['product_id'] = $code['p'];
					$ptr[$code['p']] = &$row['product_name'];
					$initial_list[$code['c']] = $row;
				}
			} else {
				$codes = explode(',',$row['codes']);
				foreach($codes AS $code) $initial_list[$code] = $row;
			}
		}
		if(!empty($initial_list)) {
			$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,c.published
					 FROM #__awocoupon c
					WHERE c.estore="'.AWOCOUPON_ESTORE.'" AND c.coupon_code IN ("'.implode('","',array_keys($initial_list)).'")
				 '.(!empty($published) ? 'AND c.published="'.$published.'" ' : '').'
				';
			$this->_db->setQuery( $sql );
			$rtn = $this->_db->loadAssocList();
			$this->_total = count($rtn);
			
			if(!$force_all && $this->getState('limit')!=0) $rtn = array_slice($rtn,$this->getState('limitstart'),$this->getState('limit'));
			foreach($rtn as $row) {
				$initial_list[$row['coupon_code']] = array_merge((array)$row,$initial_list[$row['coupon_code']]);
				$initial_list[$row['coupon_code']]['coupon_valuestr'] = number_format($row['coupon_value'],2);

				$this->_data->rows[$row['id']] = $initial_list[$row['coupon_code']];
			}
		}
		if(!empty($ptr)) {
			$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct'),array_keys($ptr),null,null,false);
			foreach($tmp as $row) $ptr[$row->id] = $row->label;
		}
				 
		
	}

	function rpt_coupon_vs_total($force_all = false) {
		$this->_data = null;
		$function_type		= JRequest::getVar( 'function_type' );
		$coupon_value_type	= JRequest::getVar( 'coupon_value_type' );
		$discount_type		= JRequest::getVar( 'discount_type' );
		$published			= JRequest::getVar( 'published' );
		$start_date			= JRequest::getVar( 'start_date' );
		$end_date			= JRequest::getVar( 'end_date' );
		$order_status		= JRequest::getVar( 'order_status' );
		$template			= (int)JRequest::getVar( 'templatelist' );
	
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		$where =   (!empty($function_type) ? ' AND c.function_type="'.$function_type.'" ' : '').'
				 '.(!empty($coupon_value_type) ? ' AND c.coupon_value_type="'.$coupon_value_type.'" ' : '').'
				 '.(!empty($discount_type) ? ' AND c.discount_type="'.$discount_type.'" ' : '').'
				 '.(!empty($template) ? ' AND c.template_id="'.$template.'" ' : '').'
				 '.(!empty($published) ? ' AND c.published="'.$published.'" ' : '');
		$this->_db->setQuery( call_user_func(array(AWOCOUPON_ESTOREHELPER,'rpt_coupon_vs_total'),$start_date,$end_date,$order_status,$where) );
		$order_details = $this->_db->loadAssocList('id');

		
		$this->_data = new stdClass();
		$this->_data->total = $this->_data->count = 0;
		$coupon_ids = $productids = array();
		
		if(empty($order_details)) return;

		
		$sql = 'SELECT c.id,c.coupon_code, uu.productids,
						SUM(uu.coupon_discount+uu.shipping_discount) as discount
				  FROM #__awocoupon_history uu
				  JOIN #__awocoupon c ON c.id=uu.coupon_entered_id 
				 WHERE c.id IN ('.implode(',',array_keys($order_details)).')
				 GROUP BY c.id
				 ORDER BY c.coupon_code';
		$this->_db->setQuery( $sql );
		$rtn = $this->_db->loadAssocList();
		$this->_total = count($rtn);

		if(!$force_all && $this->getState('limit')!=0) $rtn = array_slice($rtn,$this->getState('limitstart'),$this->getState('limit'));
		foreach($rtn as $row) {
			$row['total'] = $order_details[$row['id']]['total'];
			$row['count'] = $order_details[$row['id']]['count'];
			
			$coupon_ids[] = $row['id'];
			$this->_data->total += $row['total'];
			$this->_data->count += $row['count'];
			
			$row['products'] = array();
			if(!empty($row['productids'])) {
				$tmp = explode(',',$row['productids']);
				foreach($tmp as $tmprow) {
					$tmpid = (int)$tmprow;
					$productids[$tmpid] = '';
					$row['products'][$tmpid] = &$productids[$tmpid];
				}
			}
			$row['totalstr'] = number_format($row['total'],2);
			$row['discountstr'] = number_format($row['discount'],2);
			$row['alltotal'] = 0;
			$row['allcount'] = 0;
			$this->_data->rows[] = $row;
		}
		
		if(!empty($this->_data->rows)) {
			foreach($this->_data->rows as $k=>$row) {
				$this->_data->rows[$k]['alltotal'] = round($this->_data->rows[$k]['total']/$this->_data->total*100,2).'%';
				$this->_data->rows[$k]['allcount'] = round($this->_data->rows[$k]['count']/$this->_data->count*100,2).'%';
			}
			
			if(!empty($productids)) {
				$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct'),array_keys($productids),null,null,false);
				foreach($tmp as $row) $productids[$row->id] = $row->label;
			}
		}
		
		
	}
	
	function rpt_coupon_vs_location($force_all = false) {
		$this->_data = null;
		$function_type		= JRequest::getVar( 'function_type' );
		$coupon_value_type	= JRequest::getVar( 'coupon_value_type' );
		$discount_type		= JRequest::getVar( 'discount_type' );
		$published			= JRequest::getVar( 'published' );
		$start_date			= JRequest::getVar( 'start_date' );
		$end_date			= JRequest::getVar( 'end_date' );
		$order_status		= JRequest::getVar( 'order_status' );
		$template			= (int)JRequest::getVar( 'templatelist' );
		
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		$where =   (!empty($function_type) ? ' AND c.function_type="'.$function_type.'" ' : '').'
				 '.(!empty($coupon_value_type) ? ' AND c.coupon_value_type="'.$coupon_value_type.'" ' : '').'
				 '.(!empty($discount_type) ? ' AND c.discount_type="'.$discount_type.'" ' : '').'
				 '.(!empty($template) ? ' AND c.template_id="'.$template.'" ' : '').'
				 '.(!empty($published) ? ' AND c.published="'.$published.'" ' : '');

		$data = call_user_func(array(AWOCOUPON_ESTOREHELPER,'rpt_coupon_vs_location'),$start_date,$end_date,$order_status,$where);

		
		$this->_data = new stdClass();
		$this->_data->total = $this->_data->count = 0;
		
		
		if(empty($data->sql)) return;


		$coupon_ids = $productids = array();
		$order_details = $data->order_details;
		
		$this->_db->setQuery( $data->sql );
		$rtn = $this->_db->loadAssocList();
		$this->_total = count($rtn);

		
		if(!$force_all && $this->getState('limit')!=0) $rtn = array_slice($rtn,$this->getState('limitstart'),$this->getState('limit'));
		foreach($rtn as $row) {
			$country_id = empty($row['country']) ? '0' : $row['country'];
			$state_id = empty($row['state']) ? '0' : $row['state'];
			
			$row['total'] = $order_details[$row['id'].'-'.$country_id.'-'.$state_id.'-'.$row['city']]['total'];
			$row['count'] = $order_details[$row['id'].'-'.$country_id.'-'.$state_id.'-'.$row['city']]['count'];
			$coupon_ids[] = $row['id'];
			$this->_data->total += $row['total'];
			$this->_data->count += $row['count'];
			
			$row['products'] = array();
			if(!empty($row['productids'])) {
				$tmp = explode(',',$row['productids']);
				foreach($tmp as $tmprow) {
					$tmpid = (int)$tmprow;
					$productids[$tmpid] = '';
					$row['products'][$tmpid] = &$productids[$tmpid];
				}
			}
			$row['totalstr'] = number_format($row['total'],2);
			$row['discountstr'] = number_format($row['discount'],2);
			$row['alltotal'] = 0;
			$row['allcount'] = 0;
			$this->_data->rows[] = $row;
		}
		
		if(!empty($this->_data->rows)) {
			foreach($this->_data->rows as $k=>$row) {
				$this->_data->rows[$k]['alltotal'] = round($this->_data->rows[$k]['total']/$this->_data->total*100,2).'%';
				$this->_data->rows[$k]['allcount'] = round($this->_data->rows[$k]['count']/$this->_data->count*100,2).'%';
			}
			
			if(!empty($productids)) {
				$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct'),array_keys($productids),null,null,false);
				foreach($tmp as $row) $productids[$row->id] = $row->label;
			}
		}
		
	}
	
	function rpt_history_uses_coupons($force_all = false) {
		global $AWOCOUPON_lang;
		
		$this->_data = null;
		$function_type		= JRequest::getVar( 'function_type' );
		$coupon_value_type	= JRequest::getVar( 'coupon_value_type' );
		$discount_type		= JRequest::getVar( 'discount_type' );
		$published			= JRequest::getVar( 'published' );
		$start_date			= JRequest::getVar( 'start_date' );
		$end_date			= JRequest::getVar( 'end_date' );
		$order_status		= JRequest::getVar( 'order_status' );
		$template			= (int)JRequest::getVar( 'templatelist' );

		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		$where =   (!empty($function_type) ? ' AND c.function_type="'.$function_type.'" ' : '').'
				 '.(!empty($coupon_value_type) ? ' AND c.coupon_value_type="'.$coupon_value_type.'" ' : '').'
				 '.(!empty($discount_type) ? ' AND c.discount_type="'.$discount_type.'" ' : '').'
				 '.(!empty($template) ? ' AND c.template_id="'.$template.'" ' : '').'
				 '.(!empty($published) ? ' AND c.published="'.$published.'" ' : '');

		$this->_db->setQuery( call_user_func(array(AWOCOUPON_ESTOREHELPER,'rpt_history_uses_coupons'),$start_date,$end_date,$order_status,$where) );

		$rtn = $this->_db->loadAssocList();
		$this->_total = count($rtn);
		
		if(!$force_all && $this->getState('limit')!=0) $rtn = array_slice($rtn,$this->getState('limitstart'),$this->getState('limit'));
		foreach($rtn as $row) {
			$row['order_date'] = !empty($row['cdate']) ? date('Y-m-d',$row['cdate']) : '';
			$row['order_number'] = !empty($row['order_id']) ? sprintf("%08d", $row['order_id']) : '';
			$row['order_total'] = !empty($row['order_total']) ? number_format($row['order_total'],2) : '';
			$row['order_subtotal'] = !empty($row['order_subtotal']) ? number_format($row['order_subtotal'],2) : '';
			$row['order_tax'] = !empty($row['order_tax']) ? number_format($row['order_tax'],2) : '';
			$row['order_shipment'] = !empty($row['order_shipment']) ? number_format($row['order_shipment'],2) : '';
			$row['order_shipment_tax'] = !empty($row['order_shipment_tax']) ? number_format($row['order_shipment_tax'],2) : '';
			$row['order_fee'] = !empty($row['order_fee']) ? number_format($row['order_fee'],2) : '';
			$row['discountstr'] = number_format($row['discount'],2);
			$row['coupon_code_str'] = $row['coupon_entered_code'].($row['coupon_id']!=$row['coupon_entered_id'] ? ' ('.$row['coupon_code'].')' : '');
			$this->_data->rows[$row['num_uses_id']] = $row;
		}
		
	}
	
	function rpt_history_uses_giftcerts($force_all = false) {
		global $AWOCOUPON_lang;
		
		$this->_data = null;
		$published			= JRequest::getVar( 'published' );
		$start_date			= JRequest::getVar( 'start_date' );
		$end_date			= JRequest::getVar( 'end_date' );
		$order_status		= JRequest::getVar( 'order_status' );
		$giftcert_product	= (int)JRequest::getVar( 'giftcert_product' );



		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		$this->_db->setQuery( call_user_func(array(AWOCOUPON_ESTOREHELPER,'rpt_history_uses_giftcerts'),$start_date,$end_date,$order_status,$published) );
		$rtn = $this->_db->loadAssocList();
		$this->_total = count($rtn);
		
		if(!$force_all && $this->getState('limit')!=0) $rtn = array_slice($rtn,$this->getState('limitstart'),$this->getState('limit'));
		$ptr = null;
		foreach($rtn as $row) {
			$row['product_id'] = 0;
			$row['product_name'] = '';
			@parse_str($row['codes'],$codes);
			if(!empty($codes[0]['p'])) {
				foreach($codes as $crow) 
					if($crow['c']==$row['coupon_code']) {
						$row['product_id'] = $crow['p'];
						$ptr[$crow['p']] = &$row['product_name'];
					}
			}
			
			if(!empty($giftcert_product) && $row['product_id']!=$giftcert_product) continue; // not the product selected
				

			$row['order_date'] = !empty($row['cdate']) ? date('Y-m-d',$row['cdate']) : '';
			$row['order_number'] = !empty($row['order_id']) ? sprintf("%08d", $row['order_id']) : '';
			$row['order_total'] = !empty($row['order_total']) ? number_format($row['order_total'],2) : '';
			$row['order_subtotal'] = !empty($row['order_subtotal']) ? number_format($row['order_subtotal'],2) : '';
			$row['order_tax'] = !empty($row['order_tax']) ? number_format($row['order_tax'],2) : '';
			$row['order_shipment'] = !empty($row['order_shipment']) ? number_format($row['order_shipment'],2) : '';
			$row['order_shipment_tax'] = !empty($row['order_shipment_tax']) ? number_format($row['order_shipment_tax'],2) : '';
			$row['order_fee'] = !empty($row['order_fee']) ? number_format($row['order_fee'],2) : '';
			$row['coupon_valuestr'] = number_format($row['coupon_value'],2);
			$row['coupon_value_usedstr'] = number_format($row['coupon_value_used'],2);
			$row['balancestr'] = number_format($row['balance'],2);
			$this->_data->rows[$row['id']] = $row;
		}
		
		if(!empty($ptr)) {
			$tmp = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct'),array_keys($ptr),null,null,false);
			foreach($tmp as $row) $ptr[$row->id] = $row->label;
		}
		
	}
	
	

	function export($data) {
		if(empty($data['report_type']) || empty($data['rpt_labels']) || empty($data['rpt_columns'])) return;
		
		@$labels = json_decode($data['rpt_labels']);
		@$columns = json_decode($data['rpt_columns']);
				
		if(empty($labels) || empty($columns) || count($labels)!=count($columns)  || !method_exists('AwoCouponModelreports','rpt_'.$data['report_type'])) return;
		
		$columns = array_flip($columns);
		
		$this->_data = null;
		$this->{'rpt_'.$data['report_type']}(true);
		
		if(empty($this->_data)) return;
		
		require_once JPATH_SITE.'/administrator/components/com_awocoupon/helpers/awoparams.php';
		$params = new awoParams();
		$delimiter = $params->get('csvDelimiter', ',') ;
		
		
		$output = '';
		$output .= $this->fputcsv2($labels,$delimiter);
		
		foreach($this->_data->rows as $row) {
			$row = array_intersect_key($row,$columns);
			$d = array_merge($columns,$row);
			
			$output .= $this->fputcsv2($d,$delimiter);
		}
		
		return $output;
		
	}
	function fputcsv2 (array $fields, $delimiter = ',', $enclosure = '"', $mysql_null = false) { 
		$delimiter_esc = preg_quote($delimiter, '/'); 
		$enclosure_esc = preg_quote($enclosure, '/'); 

		$output = array(); 
		foreach ($fields as $field) { 
			if ($field === null && $mysql_null) { 
				$output[] = 'NULL'; 
				continue; 
			} 

			$output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? ( 
				$enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure 
			) : $field; 
		} 

		return join($delimiter, $output) . "\n"; 
	} 

	
	

	function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function getOrderstatuses() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getOrderStatuses'));
	}

	function getGiftCertProducts() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		$this->_db->setQuery(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getQueryGiftCertProducts'),'','',' ORDER BY _product_name,g.product_id '));
		return $this->_db->loadObjectList();
	}
	
	function getTemplateList() {
		$sql = 'SELECT id,coupon_code FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'" AND published=-2 ORDER BY coupon_code,id';
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('id');
	}
}
