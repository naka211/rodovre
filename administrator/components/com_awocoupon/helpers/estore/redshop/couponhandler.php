<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estorecouponhandler.php';

class AwoCouponRedshopCouponHandler extends AwoCouponEstoreCouponHandler {
	var $params = null;

	var $cart = null;
	var $rscart = null;
	var $rscoupon_code = '';
	var $default_err_msg = '';
	
	function AwoCouponRedshopCouponHandler ($c_data=array()) {
		parent::__construct();
		
		$this->estore = 'redshop';
		$this->default_err_msg = '';

		$this->rscart  = empty($c_data) || count($c_data)<=0 ? $this->session->get('cart') : $c_data;
		//$this->product_total = @$this->rscart['total'];
		$this->product_total = @$this->rscart['product_subtotal'];
		if(!empty($this->rscart['coupon'])) {
			foreach($this->rscart['coupon'] as $c) $this->product_total += $c['product_discount'];
		}
		
		require_once JPATH_ADMINISTRATOR.'/components/com_redshop/helpers/order.php';
		$order_func = new order_functions();
		$user 		= JFactory::getUser ();
		if($user->id) {
			$this->customer = $order_func->getBillingAddress($user->id);
		} else {
			$auth 				= $this->session->get( 'auth') ;
			if ($auth['users_info_id']) {
				$uid = -$auth['users_info_id'];
				$this->customer = $order_func->getBillingAddress($uid);
			}
		}
//printrx($this->customer);
	}

	static function process_coupon_code( $c_data ) {
	//exit('processing');
		$instance = new AwoCouponRedshopCouponHandler($c_data);
		$instance->rscoupon_code	= JRequest::getVar('discount_code', '');
	
		$bool = $instance->process_coupon_helper( );
		return !empty($c_data) ? $instance->rscart : $bool;
	}
	
	static function process_autocoupon() {

		$instance = new AwoCouponRedshopCouponHandler();
		//$instance->rscart = $instance->session->get('cart');
		$code = $instance->process_autocoupon_helper();
		if(empty($code)) return;
		
		//$cart->setCouponCode($code);
	}


	static function remove_coupon_code( $order_id ) {
		$instance = new AwoCouponRedshopCouponHandler();
		//$instance->rscart = $instance->session->get( 'cart');
	
	
		return $instance->cleanup_coupon_code_helper($order_id);
	}
	
	
	
	
	
	function initialize_coupon() {
		parent::initialize_coupon();
		
		
		if(empty($this->rscart['coupon'])) $this->rscart['coupon'] = array();
		foreach($this->rscart['coupon'] as $k=>$row) {
			if(empty($row['awocoupon'])) continue;
			unset($this->rscart['coupon'][$k]);
		}
		$this->rscart['coupon'] = array_values($this->rscart['coupon']);
		$this->rscart['free_shipping'] = 0;
		// store data
		$this->session->set('cart',$this->rscart);		
		
	}	
	protected function finalize_coupon($master_output) {
		$session_array = $this->calc_coupon_session($master_output);
		if(empty($session_array)) return false;

		$this->session->set('coupon', serialize($session_array), 'awocoupon');
		
		
		// awocoupon
		$data = array(
			'coupon_code'=>$session_array['coupon_code'],
			'coupon_id'=>-1,
			'used_coupon'=>1,
			'coupon_value'=>$session_array['product_discount']+$session_array['shipping_discount'],
			'remaining_coupon_discount'=>0,
			'transaction_coupon_id'=>0,
			'awocoupon'=>true,
			'product_discount'=>$session_array['product_discount'],
			'shipping_discount'=>$session_array['shipping_discount'],
		);
		$current_coupon_arr = !empty($this->rscart['coupon']) ? $this->rscart['coupon'] : array();
		$current_coupon_arr = array_merge(array($data),$current_coupon_arr);
		$this->rscart['coupon_discount'] = $data['coupon_value'];
		$this->rscart['coupon'] = $current_coupon_arr;
		$this->rscart['free_shipping'] = 0;
		// store data
		$this->session->set('cart',$this->rscart);
		
//printr($session_array);
		
		return true;
		
	}
	protected function finalize_coupon_store ($coupon_session) { return; }	
	protected function finalize_autocoupon($coupon_codes) {
		foreach($coupon_codes as $coupon) {
			$this->rscoupon_code = $coupon->coupon_code;
			$this->process_coupon_helper();
		}
	}
		
	
	
	protected function getuniquecartstring($coupon_code=null) {
return mt_rand();
		if(empty($coupon_code)) {
			foreach($this->rscart['coupon'] as $k=>$row) {
				if(empty($row['awocoupon'])) continue;
				$coupon_code = $row['coupon_code'];
				break;
			}
		}
		if(!empty($coupon_code)) {
			$string = $this->rscart['product_subtotal'].'|'.$coupon_code;
			for($i = 0; $i < $this->rscart['idx']; $i ++) {
				$string .= '|'.$this->rscart[$i]['product_id']
							.'|'.$this->rscart[$i]['quantity'];
			}
			return $string.'|ship|'.@$this->rscart['shipping'];
		}
		return;
	}
	protected function getuniquecartstringauto() { return mt_rand(); }
	protected function get_submittedcoupon() { return $this->rscoupon_code; }
	protected function get_storeshoppergroupids($user_id) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT shopper_group_id FROM #__redshop_users_info WHERE user_id='.(int)$user_id;
		$db->setQuery($sql);
		$rtn = (int)$db->loadResult();
		return empty($rtn) ? array() : array($rtn);
	}
	protected function get_awocouponasset($id,$type,$_table='1') {
		$rtn = parent::get_awocouponasset($id,$type,$_table);
				
		if($type=='product') {
			$ids_to_check = implode(',',$rtn);
			if(!empty($ids_to_check)) {
				$db = JFactory::getDBO();	
				$db->setQuery('SELECT product_id FROM #__redshop_product WHERE product_parent_id IN ('.$ids_to_check.')');
				$tmp = $db->loadObjectList();
				foreach($tmp as $tmp2) $rtn[$tmp2->product_id] = $tmp2->product_id;
			}
		}
		return $rtn;
	}
	protected function get_storecategory($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT category_id,product_id
				  FROM #__redshop_product_category_xref
				 WHERE product_id IN ('.$ids.')';
		$db->setQuery($sql);
		$tmp1 = $db->loadObjectList();
		
		// get category list of parent products
		$sql = 'SELECT category_id,p.product_id 
				  FROM #__redshop_product p 
				  JOIN #__redshop_product_category_xref c ON c.product_id=p.product_parent_id
				 WHERE p.product_id IN ('.$ids.')';
		$db->setQuery($sql);
		$tmp2 = $db->loadObjectList();
		
		return array_merge($tmp1,$tmp2);
	}
	protected function get_storemanufacturer($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT manufacturer_id,product_id
				  FROM #__redshop_product WHERE product_id IN ('.$ids.')';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	protected function get_storevendor($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT supplier_id AS vendor_id,product_id
				  FROM #__redshop_product WHERE product_id IN ('.$ids.')';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	protected function get_storeshipping_isdefaultbypass($coupon_id) { 
		$shipping = $this->get_storeshipping();
		$shippinglist = $this->get_awocouponasset($coupon_id,'shipping');
		return (empty($shippinglist) && empty($shipping->shipping_id) && !empty($this->rscart['shipping'])) ? true : false;
	}
	protected function get_storeshipping() {
		$shipping = (object) array(
			'shipping_id'=>0,
			'total_notax'=>$this->realcart['shipping_total_notax'],
			'total'=>$this->realcart['shipping_total'],
		);
		
		include_once JPATH_ADMINISTRATOR.'/components/com_redshop/helpers/shipping.php';
		$shippinghelper	 		= new shipping();

		$shipping_rate_id 		= JRequest::getVar ( 'shipping_rate_id' );
		if(!empty($shipping_rate_id)) $order_shipping = explode ( "|", $shippinghelper->decryptShipping( str_replace(" ","+",$shipping_rate_id) ));
	
		if(!empty($order_shipping)) {
			//$shipping->shipping_id = $order_shipping[4];
			$this->rscart['shipping'] = $order_shipping[3]+$order_shipping [6];
			$this->rscart['shipping_tax'] = $order_shipping [6];
			$shipping = (object) array(
				'shipping_id'=>$order_shipping[4],
				'total_notax'=>$order_shipping[3],
				'total'=>$order_shipping[3]+$order_shipping [6] ,
			);
		}

		return $shipping;
	}
	protected function realtotal_verify(&$_SESSION_product,&$_SESSION_product_notax) {
		$orig_product = $_SESSION_product;
		if( $this->realcart['product_total'] < $_SESSION_product ) {
			$_SESSION_product_notax = $this->realcart['product_total'] * $_SESSION_product_notax / $_SESSION_product;
			$_SESSION_product = $this->realcart['product_total'];
		}
	}

	protected function get_orderemail($order_id) {
		if(!empty($this->customer->user_email)) return $this->customer->user_email;
		
		$db = JFactory::getDBO();	
		$sql = 'SELECT u.user_email
				  FROM #__redshop_orders o 
				  JOIN #__redshop_order_users_info u on u.order_id=o.order_id
				 WHERE o.order_id='.(int)$order_id.' AND u.address_type="BT"';
		$db->setQuery($sql);
		return $db->loadResult();
	}

	protected function is_customer_num_uses($coupon_id,$max_num_uses,$customer_num_uses,$is_parent=false) {
		
		$email = !empty($this->customer->user_email) ? $this->customer->user_email : '';
		$customer_num_uses = (int)$customer_num_uses;
		$max_num_uses = (int)$max_num_uses;

		if(!empty($email)) {
			$db = JFactory::getDBO();	
			if(!$is_parent) {
				$sql = 'SELECT COUNT(id) FROM #__awocoupon_history
						 WHERE estore="'.$this->estore.'" AND coupon_id='.$coupon_id.' AND user_email="'.awolibrary::dbEscape($email).'"
						 GROUP BY coupon_id';
			}
			else {
				$sql = 'SELECT COUNT(DISTINCT order_id) FROM #__awocoupon_history 
						 WHERE estore="'.$this->estore.'" AND coupon_entered_id='.$coupon_id.' AND user_email="'.awolibrary::dbEscape($email).'"
						 GROUP BY coupon_entered_id';
			
			}
			$db->setQuery($sql);
			$customer_num_uses += (int)$db->loadResult();
		}
		
		if(!empty($customer_num_uses) && $customer_num_uses>=$max_num_uses) {
		// per user: already used max number of times
			return false;
		}
		return true;


	}
	protected function define_cart_items() {
		// retreive cart items
		$this->cart = new stdClass();
		$this->cart->items = array();
		$this->cart->items_def = array();
		
		
		
		for($i = 0; $i < $this->rscart['idx']; $i ++) {
			$product_id = $this->rscart[$i]['product_id'];
			if(empty($product_id)) continue; # skip redSHOP gift cards
			
			//$this->cart->items_def[$product_id] = array();
			$this->cart->items_def[$product_id]['product'] = $product_id;
			$this->cart->items [] = array(
				'product_id' => $product_id,
				'discount' => $this->rscart[$i]['product_price']<$this->rscart[$i]['product_old_price'] ? 1 : 0,
				'product_price' => $this->rscart[$i]['product_price'],					
				'product_price_notax' => $this->rscart[$i]['product_price_excl_vat'],					
				'tax_rate' => ($this->rscart[$i]['product_price']-$this->rscart[$i]['product_price_excl_vat'])/$this->rscart[$i]['product_price_excl_vat'],
				'qty' => $this->rscart[$i]['quantity'],
			);
		}
		
		// get real totals
		$this->get_real_cart_total();
	}




	function buyxy_getproduct($mode,$type,$assetlist) {
	
		$db = JFactory::getDBO();

		$the_product = 0;
		$ids = implode(',',$assetlist);
		if(empty($ids)) return;
		
		if($mode == 'include') {
			if($type == 'product') $the_product = current($assetlist);
			elseif($type== 'category') {
				$db->setQuery('SELECT c.product_id FROM #__redshop_product_category_xref c JOIN #__redshop_product p ON p.product_id=c.product_id WHERE p.published=1 AND c.category_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'manufacturer') {
				$db->setQuery('SELECT product_id FROM #__redshop_product WHERE published=1 AND manufacturer_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'vendor') {
				$db->setQuery('SELECT product_id FROM #__redshop_product WHERE published=1 AND supplier_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
		}
		elseif($mode == 'exclude') {
			if($type == 'product') {
				$db->setQuery('SELECT product_id FROM #__redshop_product WHERE published=1 AND product_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type== 'category') {
				$db->setQuery('SELECT c.product_id FROM #__redshop_product_category_xref c JOIN #__redshop_product p ON p.product_id=c.product_id WHERE p.published=1 AND c.category_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'manufacturer') {
				$db->setQuery('SELECT product_id FROM #__redshop_product WHERE published=1 AND manufacturer_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'vendor') {
				$db->setQuery('SELECT product_id FROM #__redshop_product WHERE published=1 AND supplier_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
		}

		return $the_product;
	
	}
	
	function add_to_cart($product_id,$qty) {
		$qty = (int)$qty;
		$product_id = (int)$product_id;
		if(empty($qty) || empty($product_id)) return;
		
		
		
		include_once (JPATH_COMPONENT.DS.'helpers'.DS.'helper.php');
		include_once (JPATH_COMPONENT.DS.'helpers'.DS.'cart.php');

		
		$carthelper = new rsCarthelper();
		$result = $carthelper->addProductToCart(array('product_id'=>$product_id,'quantity'=>$qty,'sel_wrapper_id'=>0,));
		//$carthelper->carttodb();
		//$carthelper->cartFinalCalculation();

		$this->rscart = $this->session->get('cart');
		
		return;
	}



	

	protected function get_real_cart_total() {
		$subtotal = 0; for($i=0; $i<$this->rscart['idx']; $i++) $subtotal += $this->rscart[$i]['product_price']*$this->rscart[$i]['quantity'];
		$this->realcart['product_total'] = $subtotal - $this->rscart['voucher_discount'] - $this->rscart['cart_discount'] ;// - $cart['coupon_discount'];
		$this->realcart['shipping_total'] = $this->rscart['shipping'];
		$this->realcart['shipping_total_notax'] = $this->rscart['shipping']-$this->rscart['shipping_tax'];
//echo $this->realcart['product_total'].' = '.$this->rscart['subtotal'].' - '.$this->rscart['voucher_discount'].' - '.$this->rscart['cart_discount'] ;// - $cart['coupon_discount'];
		
		$product_discount = $shipping_total = 0;
		if(!empty($this->rscart['coupon'])) {
			foreach($this->rscart['coupon'] as $row) {
				if(!empty($row['awocoupon']) && $row['awocoupon']) continue;
				$product_discount += $row['coupon_value'];
			}
		}
		
//printr($this->realcart);
		$this->realcart['product_total'] -= $product_discount;
		$this->realcart['product_total'] = max(0, $this->realcart['product_total']);
		//$this->realcart['shipping_discount'] -= $shipping_discount;
		//$this->realcart['shipping_discount'] = max(0, $this->realcart['shipping_discount']);
//echo $exclude_coupon_id.' '.$product_total.' '.$shipping_total;
//printr($this->realcart);
//printrx($this->rscart);
	}
	



}
