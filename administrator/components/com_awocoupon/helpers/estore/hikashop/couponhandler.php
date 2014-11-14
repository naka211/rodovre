<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estorecouponhandler.php';

class AwoCouponHikashopCouponHandler extends AwoCouponEstoreCouponHandler {

	
	var $params = null;
	
	var $cart = null;
	
	var $loaded_coupon = '';
	var $cart_products = null;
	var $cart_shipping = null;
	var $order_total = 0;
	var $product_total = 0;
	var $is_display_error = true;
	var $continue_execution = false;
	var $discount_auto_load = false;
	var $default_err_msg = '';
	var $customer = null;
	var $hikashop_version = null;


	function AwoCouponHikashopCouponHandler ($cart = null) {
		parent::__construct();
		
		$this->estore = 'hikashop';

		if(file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop.xml')) $parser = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop.xml');
		elseif(file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop_j3.xml')) $parser = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_hikashop/hikashop_j3.xml');
		if(!empty($parser)) $this->hikashop_version = (string)$parser->version;
		
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		$this->store_config =& hikashop_config();
		
		$this->default_currency = $this->store_config->get('main_currency',1);
		//$this->discount_before_tax = (int)$this->store_config->get('discount_before_tax',0);

		$this->cart_shipping = JFactory::getApplication()->getUserState( HIKASHOP_COMPONENT.'.shipping_data');
		if(!empty($this->cart_shipping) && !isset($this->cart_shipping->shipping_price_with_tax)) {
			$currencyClass = hikashop_get('class.currency');
			//$shippings = array(&$this->cart_shipping);
			//$currencyClass->processShippings($shippings);
			if(version_compare($this->hikashop_version,'2.2.0','>=')) $currencyClass->processShippings($this->cart_shipping);
			else {
				$shippings = array(&$this->cart_shipping);
				$currencyClass->processShippings($shippings);
			}
		}

		$this->default_err_msg = JText::_('COUPON_NOT_VALID');
		
		
		
		
		//customer
		$hika_user_id = hikashop_loadUser();
		if($hika_user_id){
			$userClass = hikashop_get('class.user');
			$this->customer = $userClass->get($hika_user_id);
		}
		

		
		$app = JFactory::getApplication();
		$current_cart_id = !empty($cart->cart_id) ? $cart->cart_id : $app->getUserStateFromRequest( HIKASHOP_COMPONENT.'.cart_id', 'cart_id', 0, 'int' );
		$coupon_session = $this->session->get('coupon', '', 'awocoupon');
		if(!empty($coupon_session) ) {
			$coupon_session = unserialize($coupon_session);
			if($coupon_session['cart_id']!=$current_cart_id) $this->initialize_coupon();
		}
		
	}

	static function process_coupon_code( &$coupon,&$total,&$zones,&$products,&$display_error, &$error_message, &$continue_execution ) {
//return;
		$instance = new AwoCouponHikashopCouponHandler();
		
		if(is_string($coupon) && $coupon == 'discount_auto_load') {
			$instance->discount_auto_load = true;
		}
		else $instance->loaded_coupon =& $coupon;
		$instance->cart_products =& $products;
		$instance->order_total =& $total;
		$instance->continue_execution =& $continue_execution;
		$instance->is_display_error = $display_error;
		$display_error = false;
		$instance->continue_execution = false;

	  	$rtn = !$instance->discount_auto_load ? $instance->process_coupon_helper() : $instance->process_autocoupon_helper();
		return $instance->reprocess ? 'reprocess' : $rtn;
	}
	
	static function remove_coupon_code( &$order ) {
		$instance = new AwoCouponHikashopCouponHandler();
	  	$instance->session = JFactory::getSession();
	
	
		if(empty($order)) return null;
		
		$coupon_session = $instance->session->get('coupon', '', 'awocoupon');
		if(empty($coupon_session) ) return null;
		$coupon_session = unserialize($coupon_session);
		
		if($coupon_session['cart_id']!=$order->cart->cart_id) return null;
		
		$instance->cleanup_coupon_code_helper($order->order_id);

		return true;

	}

	static function order_cancel_check($data) {
		$instance = new AwoCouponHikashopCouponHandler();
	  	$instance->session = JFactory::getSession();
	
	
		$order_id = @$data->order_id;
		$order_status = @$data->order_status;
		$instance->cleanup_ordercancel_helper($order_id,$order_status);
		
		return true;
	}
	
	static function validate_coupon($_code,&$continue_execution) {
		$instance = new AwoCouponHikashopCouponHandler();
		
		$db = JFactory::getDBO();	

		//------START STORE COUPON SYSTEM ----------------------------------------------------------------------------------------------
		if($instance->params->get('enable_store_coupon', 0) == 1) {
			$db->setQuery( 'SELECT id FROM #__awocoupon WHERE estore="hikashop" AND coupon_code='.$db->Quote( awolibrary::dbEscape(trim($_code))) );
			$tmp = $db->loadResult();
			if(empty($tmp)) {
				$db->setQuery('SELECT discount_id FROM #__hikashop_discount WHERE discount_code='.$db->Quote( awolibrary::dbEscape(trim($_code))) );
				$tmp = $db->loadResult();
				if(!empty($tmp)) {
					$continue_execution = true;
					return null;
				}
			}
		}
		//------END   STORE COUPON SYSTEM ----------------------------------------------------------------------------------------------
		
		$continue_execution = false;
		return (object) array('coupon_code'=>$_code,'discount_auto_load'=>false,);
	}
	
	
	function initialize_coupon(&$cartclass=null) {

		parent::initialize_coupon();
		JFactory::getApplication()->setUserState( HIKASHOP_COMPONENT.'.coupon_code','');		
		
		// remove from session so coupon code is not called constantly
		$this->loaded_coupon = '';
		if(!empty($cartclass)) {
			$cartclass->cart->cart_coupon = '';
			$cartclass->save($cartclass->cart);
		}
				
	}
	
	function finalize_coupon_store_calc (&$cart) {

		if(empty($cart->coupon->awocoupon) && empty($cart->awocoupon_discount)) return null;

		$coupon_awo_entered_coupon_ids = array();
		$coupon_session = $this->session->get('coupon', '', 'awocoupon');
		if(empty($coupon_session) ) return null;
			
		$this->order_total = clone($cart->full_total);
		$coupon_session = unserialize($coupon_session);

		if($this->default_currency!=$this->order_total->prices[0]->price_currency_id) {
			$currencyClass = hikashop_get('class.currency');
			$coupon_session['product_discount'] = $currencyClass->convertUniquePrice($coupon_session['product_discount'],$this->default_currency,$this->order_total->prices[0]->price_currency_id);
			$coupon_session['product_discount_notax'] = $currencyClass->convertUniquePrice($coupon_session['product_discount_notax'],$this->default_currency,$this->order_total->prices[0]->price_currency_id);
			$coupon_session['shipping_discount'] = $currencyClass->convertUniquePrice($coupon_session['shipping_discount'],$this->default_currency,$this->order_total->prices[0]->price_currency_id);
			$coupon_session['shipping_discount_notax'] = $currencyClass->convertUniquePrice($coupon_session['shipping_discount_notax'],$this->default_currency,$this->order_total->prices[0]->price_currency_id);
		}
		
		$price_with_tax = $this->store_config->get('price_with_tax');//echo $tester; exit;
		$object = (object) array(
			'awocoupon'=>1,
			'discount_code'=>$coupon_session['coupon_code'],
			'total'=>clone($this->order_total),
			'discount_value_without_tax'=> empty($price_with_tax) && !$this->coupon_discount_before_tax
												? $coupon_session['product_discount']+$coupon_session['shipping_discount']
												: $coupon_session['product_discount_notax']+$coupon_session['shipping_discount_notax'],
			//'discount_value_without_tax'=>$coupon_session['product_discount_notax']+$coupon_session['shipping_discount_notax'],
			'discount_value'=>$coupon_session['product_discount']+$coupon_session['shipping_discount'],
			'taxes'=>array(),
		);
		$object->total->prices[0]->price_value_without_discount_with_tax = $object->total->prices[0]->price_value_with_tax;
		$object->total->prices[0]->price_value_without_discount = $object->total->prices[0]->price_value;
		$object->total->prices[0]->price_value_with_tax -= $object->discount_value;
		//$object->total->prices[0]->price_value -= $object->discount_value_without_tax;
		if(version_compare($this->hikashop_version,'2.2.0','>=')) {
			if($this->coupon_discount_before_tax) {
				//if(empty($price_with_tax)) 
				$object->total->prices[0]->price_value -= $object->discount_value_without_tax;
				//else $object->total->prices[0]->price_value -= $object->discount_value;
			}
			else {
				if(!empty($price_with_tax)) $object->total->prices[0]->price_value -= $object->discount_value;
				else $object->total->prices[0]->price_value -= $object->discount_value_without_tax;
			}
		}
		else {
			if(!empty($price_with_tax) || $this->coupon_discount_before_tax)
				$object->total->prices[0]->price_value -= $object->discount_value_without_tax;
		}
		
		if(!empty($object->total->prices[0]->taxes)) {
			$object->total->prices[0]->taxes_without_discount = array();
			$coupon_tax_amount = $coupon_session['product_discount_tax']+$coupon_session['shipping_discount_tax'];
			$total_tax_before_discount = 0;
			foreach($object->total->prices[0]->taxes as $namekey => $tax){ 
				$object->total->prices[0]->taxes_without_discount[$namekey] = clone($tax); 
				$total_tax_before_discount += $tax->tax_amount;
			}
		
			if(!empty($coupon_tax_amount)) {
				foreach($object->total->prices[0]->taxes as $namekey => $tax){ 
					$current_tax = $object->total->prices[0]->taxes[$namekey]->tax_amount;
					$current_tax_discount = empty($total_tax_before_discount) ? 0 : $coupon_tax_amount/$total_tax_before_discount*$current_tax;
					$object->total->prices[0]->taxes[$namekey]->tax_amount = round($current_tax - $current_tax_discount,2);
					$object->taxes[] = (object)array('tax_namekey'=>$namekey,'tax_amount'=>$current_tax_discount);
				}
				
				//$tax = reset($object->total->prices[0]->taxes);
				//$object->total->prices[0]->taxes[$tax->tax_namekey]->tax_amount -= $coupon_tax_amount;
				//$object->taxes[0]->tax_namekey = $tax->tax_namekey;
				//$object->taxes[0]->tax_amount = $coupon_tax_amount;
			}
		}

		if(class_exists('hikamarket')) {
			$object->discount_target_vendor = 1;
			$hikamarket_config = hikamarket::config();
			$totaldiscount_str = $hikamarket_config->get('calculate_vendor_price_with_tax',false) ? 'totaldiscount' : 'totaldiscount_notax';
			$object->products = array();
			foreach($cart->products as $product) {           
				foreach($coupon_session['cart_items'] as $item) {
					if($product->product_id == $item['product_id']) {
						if(isset($product->variants)) {
							foreach($product->variants as $variant) {
								$tmp_product = clone($variant);
								$tmp_product->processed_discount_value =round($item[$totaldiscount_str],2);
								$object->products[] = $tmp_product;
							}
						}
						else {
							$tmp_product = clone($product);
							$tmp_product->processed_discount_value =round($item[$totaldiscount_str],2);
							$object->products[] = $tmp_product;
						}
						break;
					}
				}
			}
		}

		$cart->coupon = $object;
		$cart->full_total = $object->total;
//printrx($object);		
		return null;		
		
	}

	
	
	protected function return_false($key) {
		if($this->is_display_error) return parent::return_false($key);
		return false;
	}
	
	protected function finalize_coupon($master_output) {
		$session_array = $this->calc_coupon_session($master_output);
		if(empty($session_array)) return false;
//printr($session_array);

		$item_in_cart = reset($this->cart_products);
		$cart_id = $item_in_cart->cart_id;

		$session_array['cart_id'] = $cart_id;
		$this->session->set('coupon', serialize($session_array), 'awocoupon');
				
		$this->finalize_coupon_store($session_array);
		
		return true;
		
	}
	protected function finalize_coupon_store($coupon_session) {
		if(!$this->discount_auto_load) {
			if(empty($this->loaded_coupon)) $this->loaded_coupon = new stdclass;
			$this->loaded_coupon->awocoupon = 1;
			$this->loaded_coupon->total = clone($this->order_total);
		}
	}
	protected function finalize_autocoupon($coupon_codes) {
		foreach($coupon_codes as $coupon) {
			$this->loaded_coupon = new stdclass;
			$this->loaded_coupon->coupon_code = $coupon->coupon_code;
			$this->process_coupon_helper();
		}
	}
	
	protected function getuniquecartstring($coupon_code=null) {
		if(empty($coupon_code)) @$coupon_code = $this->loaded_coupon->coupon_code;
		if(!empty($coupon_code)) {
			$user = JFactory::getUser();
			$user_email = !empty($this->customer->user_email) ? $this->customer->user_email : '';
			@$shipping_id = version_compare($this->hikashop_version,'2.2.0','>=') ? $this->cart_shipping[0]->shipping_id : $this->cart_shipping->shipping_id;
			$string = $this->order_total->prices[0]->price_value_with_tax.'|'.$coupon_code.'|'.$user->id.'|'.$user_email;
			foreach ($this->cart_products as $product) { $string .= '|'.$product->cart_product_id.'|'.$product->product_id.'|'.$product->cart_product_quantity; }
			return $string.'|ship|'.$shipping_id.'|currency|'.$this->order_total->prices[0]->price_currency_id;
		}
		return;
	}
	protected function getuniquecartstringauto() {
		$user = JFactory::getUser();
		@$shipping_id = version_compare($this->hikashop_version,'2.2.0','>=') ? $this->cart_shipping[0]->shipping_id : $this->cart_shipping->shipping_id;
		$string = $this->order_total->prices[0]->price_value_with_tax.'|'.$user->id;
		foreach ($this->cart_products as $product) { $string .= '|'.$product->cart_product_id.'|'.$product->product_id.'|'.$product->cart_product_quantity; }
		return $string.'|ship|'.$shipping_id.'|currency|'.$this->order_total->prices[0]->price_currency_id;
	}

	protected function get_storeshoppergroupids($user_id) { return array(); }
	protected function get_storecategory($ids) {
		$db = JFactory::getDBO();	
		$db->setQuery('SELECT category_id,product_id FROM #__hikashop_product_category WHERE product_id IN ('.$ids.')');
		return $db->loadObjectList();
	}
	protected function get_storemanufacturer($ids) {
		$db = JFactory::getDBO();	
		$db->setQuery('SELECT product_manufacturer_id AS manufacturer_id, product_id FROM #__hikashop_product WHERE product_id IN ('.$ids.')');
		return $db->loadObjectList();
	}
	protected function get_storevendor($ids) { return array(); }
	protected function get_storeshipping() {
		$shipping = (object) array(
			'shipping_id'=>0,
			'total_notax'=>0,
			'total'=>0,
		);
		if(!empty($this->cart_shipping)) {
		
			$shipping_object = version_compare($this->hikashop_version,'2.2.0','>=') ? $this->cart_shipping[0] : $this->cart_shipping;
		
			$total_shipping_notax = $shipping_object->shipping_price;
			$total_shipping = isset($shipping_object->shipping_price_with_tax) ? $shipping_object->shipping_price_with_tax : $total_shipping_notax;
			if($this->default_currency!=$shipping_object->shipping_currency_id) {
				// use default currency
				$total_shipping_notax = $shipping_object->shipping_price_orig;
				$total_shipping = isset($shipping_object->shipping_price_orig_with_tax) ? $shipping_object->shipping_price_orig_with_tax : $total_shipping_notax;
			}
			$shipping = (object) array(
				'shipping_id'=>$shipping_object->shipping_id,
				'total_notax'=>$total_shipping_notax,
				'total'=>$total_shipping,
			);
				
		}
		return $shipping;
	}
	protected function get_submittedcoupon() { return @$this->loaded_coupon->coupon_code; }
	protected function get_orderemail($order_id) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT u.user_email
				  FROM #__hikashop_order o
				  JOIN #__hikashop_user u on u.user_id=o.order_user_id
				 WHERE o.order_id='.(int)$order_id;
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


		//$sql = 'SELECT COUNT(*)
		//		  FROM #__awocoupon_history h
		//		  LEFT JOIN #__hikashop_order o on o.order_id=h.order_id
		//		  LEFT JOIN #__hikashop_user u on u.user_id=o.order_user_id AND u.user_email="'.awolibrary::dbEscape($email).'"
		//		 WHERE h.estore="'.$this->estore.'" AND h.coupon_id='.$coupon_id.'
		//		 GROUP BY h.coupon_id';
	}
	protected function define_cart_items() {
		// retreive cart items
		$this->cart = new stdClass();
		$this->cart->items = array();
		$this->cart->items_def = array();

//printr($this->cart_products);
		foreach ($this->cart_products as $product){
			$product_id = $product->product_id;
			if(empty($product_id)) continue;
			if($product->product_type != 'main') continue;
			if (empty($product->cart_product_quantity) && empty( $product->variants )){ continue; }
			
			
			if(!empty($product->cart_product_quantity)) {
				$this->cart->items_def[$product_id]['product'] = $product_id;
				$price_notax = $product->prices[0]->unit_price->price_value;
				$price = $product->prices[0]->unit_price->price_value_with_tax;
				if($this->default_currency!=$product->prices[0]->unit_price->price_currency_id) {
					// price in default currency
					$price_notax = $product->prices[0]->unit_price->price_orig_value;
					$price = $product->prices[0]->unit_price->price_orig_value_with_tax;
				}
				$this->cart->items [] = array(
					'product_id' => $product_id,
					'cart_product_id' => $product->cart_product_id,
					'actual_product_id'=>$product_id,
					'discount' => !empty($product->prices[0]->unit_price->price_value_without_discount) && $product->prices[0]->unit_price->price_value_without_discount> $product->prices[0]->unit_price->price_value ? 1 : 0,
					'product_price' => $price,
					'product_price_notax' => $price_notax,
					'tax_rate' => ($price-$price_notax)/$price_notax,
					'qty' => $product->cart_product_quantity,
				);
				$this->product_total += $product->cart_product_quantity*$price;
			}
			else {
				foreach($product->variants as $variant) {
					$this->cart->items_def[$product_id]['product'] = $product_id;
					$price_notax = $variant->prices[0]->unit_price->price_value;
					$price = $variant->prices[0]->unit_price->price_value_with_tax;
					if($this->default_currency!=$variant->prices[0]->unit_price->price_currency_id) {
						// price in default currency
						$price_notax = $variant->prices[0]->unit_price->price_orig_value;
						$price = $variant->prices[0]->unit_price->price_orig_value_with_tax;
					}
					$this->cart->items [] = array(
						'product_id' => $product_id,
						'cart_product_id' => $variant->cart_product_id,
						'actual_product_id'=>$variant->product_id,
						'discount' => 0,
						'discount' => !empty($variant->prices[0]->unit_price->price_value_without_discount) && $variant->prices[0]->unit_price->price_value_without_discount>$variant->prices[0]->unit_price->price_value ? 1 : 0,
						'product_price' => $price,
						'product_price_notax' => $price_notax,
						'tax_rate' => ($price-$price_notax)/$price_notax,
						'qty' => $variant->cart_product_quantity,
					);
					$this->product_total += $variant->cart_product_quantity*$price;
				}
			}
		}
//printrx($this->cart->items);
	}
	

	function buyxy_getproduct($mode,$type,$assetlist) {
	
		$db = JFactory::getDBO();

		$the_product = 0;
		$ids = implode(',',$assetlist);
		if(empty($ids)) return;
		
		if($mode == 'include') {
			if($type == 'product') $the_product = current($assetlist);
			elseif($type== 'category') {
				$db->setQuery('SELECT c.product_id FROM #__hikashop_product_category c JOIN #__hikashop_product p ON p.product_id=c.product_id WHERE p.product_published=1 AND c.category_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'manufacturer') {
				$db->setQuery('SELECT product_id FROM #__hikashop_product WHERE product_published=1 AND product_manufacturer_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'vendor') ;
		}
		elseif($mode == 'exclude') {
			if($type == 'product') {
				$db->setQuery('SELECT product_id FROM #__hikashop_product WHERE product_published=1 AND product_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type== 'category') {
				$db->setQuery('SELECT c.product_id FROM #__hikashop_product_category c JOIN #__hikashop_product p ON p.product_id=c.product_id WHERE p.product_published=1 AND c.category_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'manufacturer') {
				$db->setQuery('SELECT product_id FROM #__hikashop_product WHERE product_published=1 AND product_manufacturer_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'vendor') ;
		}

		return $the_product;
	
	}
	
	

	function add_to_cart($product_id,$qty) {
		$qty = (int)$qty;
		$product_id = (int)$product_id;
		if(empty($qty) || empty($product_id)) return;
		
		$product_id_toadd = $product_id;
		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM #__hikashop_product WHERE product_parent_id ='.$product_id.' AND product_published=1');
		$variants = $db->loadObjectList();
		if(!empty($variants)) {
			$is_found = false;
			foreach($variants as $variant) {
				foreach($this->cart->items as $k2=>$item) {
					if($item['actual_product_id']==$variant->product_id) {
						$is_found = true;
						$product_id_toadd = $variant->product_id;
						break;
					}
				}
			}
			if(!$is_found) $product_id_toadd = $variants[0]->product_id;
			
		}
		
		$add=1;
		$class = hikashop_get('class.cart');
		$status = $class->update($product_id_toadd,$qty,$add,'product');
		
		if($status) {
			$item_in_cart = reset($this->cart_products);
			$cart_id = $item_in_cart->cart_id;
			$class->cart->cart_id = $cart_id; // needed or receive a cart is empty error message

			$this->reprocess = true;
		}

		return;
		
	}




}

