<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estorecouponhandler.php';

class AwoCouponVirtuemart1CouponHandler extends AwoCouponEstoreCouponHandler {



	var $params = null;
	var $inparams = null;
	
	var $cart = null;
	
	var $vmcoupon_code = '';
	var $vmcart = null;
	var $vmcartData = null;
	var $vmcartPrices = null;
	var $product_total = 0;
	var $default_err_msg = '';
	var $refresh_cart = false;

	function __construct () {
		parent::__construct();

		$this->estore = 'virtuemart1';
		
		global $VM_LANG;
		$this->default_err_msg = $VM_LANG->_('PHPSHOP_COUPON_CODE_INVALID');
		
	}

	static function process_coupon_code( $d ) {
		$instance = new AwoCouponVirtuemart1CouponHandler();
		
	  	return $instance->process_coupon_helper();
	}
	
	static function remove_coupon_code( $d ) {
		$instance = new AwoCouponVirtuemart1CouponHandler();
		
		$order_id = 0;
		if(!empty($d['order_number'])) {
			$db = JFactory::getDBO();
			$db->setQuery('INSERT INTO #__awocoupon_vm1ids (type,value) VALUES ("order_number","'.awolibrary::dbEscape($d['order_number']).'")');
			$db->query();
			$order_id = $db->insertid();
		}
		$instance->cleanup_coupon_code_helper($order_id);
		
		return true;
	}
	
	static function order_cancel_check($d,$order_status) {
		$instance = new AwoCouponVirtuemart1CouponHandler();
	  	$instance->session = JFactory::getSession();
	
	
		$order_id = 0;
		$order_status = '';
		$db = JFactory::getDBO();
		$db->setQuery('SELECT o.order_status,vm1.id AS order_id 
						FROM #__vm_orders o 
						JOIN #__awocoupon_vm1ids vm1 ON vm1.value=o.order_number
						WHERE o.order_id='.@(int)$d['order_id']);
		$rtn = $db->loadObject();
		if(!empty($rtn->order_id)) {
			$order_id = $rtn->order_id;
			$order_status = trim($rtn->order_status);
		}

		$instance->cleanup_ordercancel_helper($order_id,$order_status);
		
		return true;
	}
	
	static function process_autocoupon($d) {

		if(empty($_SESSION['cart']['idx'])) return;

		$instance = new AwoCouponVirtuemart1CouponHandler();
		$code = $instance->process_autocoupon_helper();
		//if(empty($code)) return;
		
		//$cart->setCouponCode($code);
	}


	
	function initialize_coupon() {
		parent::initialize_coupon();
		
		// remove from vm session so coupon code is not called constantly
		unset(	$_SESSION['coupon_id'],
				$_SESSION['coupon_discount'],
				$_SESSION['coupon_redeemed'],
				$_SESSION['coupon_code'],
				$_SESSION['coupon_type']
			);
		
	}
	

	function finalize_coupon($master_output) {
		$session_array = $this->calc_coupon_session($master_output);
		if(empty($session_array)) return false;


		
		$_SESSION['coupon_redeemed'] = true;
		$_SESSION['coupon_id'] = $session_array['coupon_id'];
		$_SESSION['coupon_code'] = $session_array['coupon_code'];
		//$_SESSION['coupon_discount'] = $this->is_discount_before_tax ? $product_discount_notax: $product_discount;
		$_SESSION['coupon_discount'] = $session_array['product_discount'] + $session_array['shipping_discount'] ;
		$_SESSION['coupon_type'] = 'gift'; // always call cleanup function
		
		
		if($this->refresh_cart) JFactory::getApplication()->redirect('index.php?option=com_virtuemart&page=shop.cart');
		return true;
		//return $coupon_codes;
		
	}



	protected function finalize_autocoupon($coupon_codes) {
		foreach($coupon_codes as $coupon) {
			$_REQUEST['coupon_code'] = $coupon->coupon_code;
			$this->process_coupon_helper();
		}
	}

	protected function getuniquecartstring($coupon_code=null) { return mt_rand(); }
	protected function getuniquecartstringauto() {
		global $auth;
		
		require_once(CLASSPATH.'ps_checkout.php');
		$ps_checkout = new ps_checkout();
			
		$d = $_REQUEST;
		$totals = $ps_checkout->calc_order_totals($d);

			
		$string = ($totals['order_subtotal'] + $totals['order_tax']).'|'.@$auth->user_id;
		$session_cart = $_SESSION['cart'];
		for($i = 0; $i < $session_cart['idx']; $i++) $string .= '|'.$i.'|'.$session_cart[$i]['product_id'].'|'.$session_cart[$i]['description'].'|'.$session_cart[$i]['quantity'];
		return $string.'|ship|'.@$_REQUEST['shipping_rate_id'];
	}
	
	protected function get_storeshoppergroupids($user_id) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT shopper_group_id FROM #__vm_shopper_vendor_xref WHERE user_id='.(int)$user_id;
		$db->setQuery($sql);
		$shopper_group_ids = $db->loadResultArray();
		if(empty($shopper_group_ids)) {
			$db->setQuery('SELECT shopper_group_id FROM #__vm_shopper_vendor_xref WHERE `default`=1');
			$shopper_group_ids = array($db->loadResult());
		}
		return $shopper_group_ids;
	}
	protected function get_awocouponasset($id,$type,$_table='1') {
		$rtn = parent::get_awocouponasset($id,$type,$_table);
				
		if($type=='product') {
			$ids_to_check = implode(',',$rtn);
			if(!empty($ids_to_check)) {
				$db = JFactory::getDBO();	
				$db->setQuery('SELECT product_id FROM #__vm_product WHERE product_parent_id IN ('.$ids_to_check.')');
				$tmp = $db->loadObjectList();
				foreach($tmp as $tmp2) $rtn[$tmp2->product_id] = $tmp2->product_id;
			}
		}
		return $rtn;
	}
	protected function get_storecategory($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT category_id,product_id FROM #__vm_product_category_xref WHERE product_id IN ('.$ids.')';
		$db->setQuery($sql);
		$tmp1 = $db->loadObjectList();
		
		// get category list of parent products
		$sql = 'SELECT c.category_id,p.product_id
				  FROM #__vm_product p 
				  JOIN #__vm_product_category_xref c ON c.product_id=p.product_parent_id
				 WHERE p.product_id IN ('.$ids.')';
		$db->setQuery($sql);
		$tmp2 = $db->loadObjectList();
		
		return array_merge($tmp1,$tmp2);
	}
	protected function get_storemanufacturer($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT manufacturer_id,product_id FROM #__vm_product_mf_xref WHERE product_id IN ('.$ids.')';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	protected function get_storevendor($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT vendor_id,product_id FROM #__vm_product WHERE product_id IN ('.$ids.')';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	protected function get_storeshipping() {
	
		$obj = (object) array(
				'shipping_id'=>0,
				'total_notax'=>0,
				'total'=>0,
		);
		
		if( !empty( $_REQUEST['shipping_rate_id'] )) {
			require_once(CLASSPATH.'ps_checkout.php');
			$ps_checkout = new ps_checkout();
			
			$d = $_REQUEST;
			$totals = $ps_checkout->calc_order_totals($d);
			$rate_array = explode( '|', urldecode(vmGet($_REQUEST,"shipping_rate_id")) );
			
			$id = 0;
			$filename = basename( $rate_array[0] );
			if(file_exists(JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart1/shipping/'.$filename.'.php')) {
				require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/virtuemart1/shipping/'.$filename.'.php';
				if(class_exists('awo_'.$filename)) {
					$shipping_class = 'awo_'.$filename;
					$shipping_class = new $shipping_class();
					$shipping_rate_id = $shipping_class->get_rate_id($rate_array) ;
					
					$value = $filename.'-'.$shipping_rate_id;
					$db = JFactory::getDBO();
					$db->setQuery('SELECT id FROM #__awocoupon_vm1ids WHERE type="shipping_rate_id" AND value="'.$value.'"');
					$id = (int)$db->loadResult();
				}
			}
			$obj = (object) array(
				'shipping_id'=>$id,
				'total_notax'=>(float) $totals['order_shipping'],
				'total'=>(float) $rate_array[3],
			);
		}
		return $obj;
	}
	protected function get_submittedcoupon() { 
		$vmcode = trim(vmGet( $_REQUEST, 'coupon_code' ));
		//JRequest::getVar( 'coupon_code', '', 'post' );
		
		$awosess = $this->session->get('coupon', '', 'awocoupon');
		if(!empty($awosess) ){
			$awosess = unserialize($awosess);
			if($awosess['coupon_code']==$vmcode) return '';
		}
		return $vmcode; 
	}
	protected function get_orderemail($order_id) {
		global $auth;
		
		$email = '';
		if(!empty($auth->user_id)) {
			$db = JFactory::getDBO();
			$db->setQuery('SELECT user_email FROM #__vm_user_info WHERE user_id='.(int)$auth->user_id.' AND address_type="BT"');
			$email = $db->loadResult();
		}
		return $email;
	}

	protected function is_customer_num_uses($coupon_id,$max_num_uses,$customer_num_uses,$is_parent=false) {
		global $auth;
		
		$db = JFactory::getDBO();
		
		
		$email = '';
		if(!empty($auth->user_id)) {
			$db->setQuery('SELECT user_email FROM #__vm_user_info WHERE user_id='.(int)$auth->user_id.' AND address_type="BT"');
			$email = $db->loadResult();
		}
		
		$customer_num_uses = (int)$customer_num_uses;
		$max_num_uses = (int)$max_num_uses;

		if(!empty($email)) {
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
		//		  LEFT JOIN #__virtuemart_orders o on o.virtuemart_order_id=h.order_id
		//		  LEFT JOIN #__virtuemart_order_userinfos u on u.virtuemart_order_id=o.virtuemart_order_id AND u.email="'.awolibrary::dbEscape($email).'" and u.address_type="BT"
		//		 WHERE h.estore="'.$this->estore.'" AND h.coupon_id='.$coupon_id.'
		//		 GROUP BY h.coupon_id';
	}

	protected function define_cart_items() {
		// retreive cart items
		$this->cart = null;
		$this->cart->items = array();
		$this->cart->items_def = array();
		
		require_once(CLASSPATH.'ps_product.php');
		require_once(CLASSPATH.'ps_checkout.php');
		$ps_product= new ps_product;
		$session_cart = $_SESSION['cart'];
		for($i = 0; $i < $session_cart['idx']; $i++) {
			$discount = $ps_product->get_discount($session_cart[$i]['product_id']);
			
			$price = $ps_product->get_adjusted_attribute_price($session_cart[$i]['product_id'], $session_cart[$i]['description']);
						
			// retrieve and add tax to product price
			$my_taxrate =  $this->get_product_taxrate($ps_product,$session_cart[$i]['product_id']);
			$product_price = round($price['product_price'] * (1+$my_taxrate),2);

			$product_price = $GLOBALS['CURRENCY']->convert( $product_price, @$price['product_currency'] );
			
			$this->cart->items_def[$session_cart[$i]['product_id']]['product'] = $session_cart[$i]['product_id'];
			$this->cart->items [] = array(
				'product_id' => $session_cart[$i]['product_id'],
				'description' => $session_cart[$i]['description'],
				'discount' => empty($discount) ? 0 : $discount['amount'],
				'product_price' => $product_price,
				'product_price_notax' => $price['product_price'],
				'tax_rate' => $my_taxrate,
				
				
				'product_tax' => $product_price - $price['product_price'],
				'qty' => $session_cart[$i]['quantity'],
			);
			$this->product_total += $session_cart[$i]['quantity']*$product_price;
		}
	}
	

	function buyxy_getproduct($mode,$type,$assetlist) {
	
		$db = JFactory::getDBO();

		$ids = implode(',',$assetlist);
		if(empty($ids)) return;
		
		$extra_where = '';
		$db->setQuery('SELECT DISTINCT product_parent_id FROM #__vm_product WHERE product_parent_id IS NOT NULL AND product_parent_id!=0');
		$parents = $db->loadResultArray();
		
		$sql = '';
		if(empty($parents)) {
			if($mode == 'include') {
				if($type == 'product') return current($assetlist);
				elseif($type== 'category') {
					$sql = 'SELECT c.product_id FROM #__vm_product_category_xref c JOIN #__vm_product p ON p.product_id=c.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND c.category_id IN ('.$ids.') LIMIT 1';
				}
				elseif($type == 'manufacturer') {
					$sql = 'SELECT m.product_id FROM #__vm_product_mf_xref m JOIN #__vm_product p ON p.product_id=m.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND m.manufacturer_id IN ('.$ids.') LIMIT 1';
				}
				elseif($type == 'vendor') {
					$sql = 'SELECT p.product_id FROM #__vm_product p WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND vendor_id IN ('.$ids.') LIMIT 1';
				}
			}
			elseif($mode == 'exclude') {
				if($type == 'product') {
					$sql = 'SELECT product_id FROM #__vm_product p WHERE product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND product_id NOT IN ('.$ids.') LIMIT 1';
				}
				elseif($type== 'category') {
					$sql = 'SELECT c.product_id FROM #__vm_product_category_xref c JOIN #__vm_product p ON p.product_id=c.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND c.category_id NOT IN ('.$ids.') LIMIT 1';
				}
				elseif($type == 'manufacturer') {
					$sql = 'SELECT m.product_id FROM #__vm_product_mf_xref m JOIN #__vm_product p ON p.product_id=m.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND m.manufacturer_id NOT IN ('.$ids.') LIMIT 1';
				}
				elseif($type == 'vendor') {
					$sql = 'SELECT product_id FROM #__vm_product p WHERE product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND vendor_id NOT IN ('.$ids.') LIMIT 1';
				}
			}
		}
		else {
			$extra_where1 = ' AND p.product_id NOT IN ('.implode(',',$parents).') ';
			$extra_where2 = ' AND p.product_parent_id IN ('.implode(',',$parents).') ';
			if($mode == 'include') {
				if($type == 'product') return current($assetlist);
				elseif($type== 'category') {
					$sql = 'SELECT c.product_id FROM #__vm_product_category_xref c JOIN #__vm_product p ON p.product_id=c.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND c.category_id IN ('.$ids.') '.$extra_where1.'
								UNION
							SELECT p.product_id FROM #__vm_product_category_xref c JOIN #__vm_product p ON p.product_parent_id=c.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND c.category_id IN ('.$ids.') '.$extra_where2.'
							LIMIT 1';
				}
				elseif($type == 'manufacturer') {
					$sql = 'SELECT m.product_id FROM #__vm_product_mf_xref m JOIN #__vm_product p ON p.product_id=m.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND m.manufacturer_id IN ('.$ids.') '.$extra_where1.'
								UNION
							SELECT p.product_id FROM #__vm_product_mf_xref m JOIN #__vm_product p ON p.product_parent_id=m.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND m.manufacturer_id IN ('.$ids.') '.$extra_where2.'
							LIMIT 1';
				}
				elseif($type == 'vendor') {
					$sql = 'SELECT p.product_id FROM #__vm_product p WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND vendor_id IN ('.$ids.') '.$extra_where1.' LIMIT 1';
				}
			}
			elseif($mode == 'exclude') {
				if($type == 'product') {
					$sql = 'SELECT product_id FROM #__vm_product p WHERE product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND product_id NOT IN ('.$ids.') '.$extra_where1.' LIMIT 1';
				}
				elseif($type== 'category') {
					$sql = 'SELECT c.product_id FROM #__vm_product_category_xref c JOIN #__vm_product p ON p.product_id=c.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND c.category_id NOT IN ('.$ids.') '.$extra_where1.'
								UNION
							SELECT p.product_id FROM #__vm_product_category_xref c JOIN #__vm_product p ON p.product_parent_id=c.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND c.category_id NOT IN ('.$ids.') '.$extra_where2.'
							LIMIT 1';
				}
				elseif($type == 'manufacturer') {
					$sql = 'SELECT m.product_id FROM #__vm_product_mf_xref m JOIN #__vm_product p ON p.product_id=m.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND m.manufacturer_id NOT IN ('.$ids.') '.$extra_where1.'
								UNION
							SELECT p.product_id FROM #__vm_product_mf_xref m JOIN #__vm_product p ON p.product_parent_id=m.product_id WHERE p.product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND m.manufacturer_id NOT IN ('.$ids.') '.$extra_where2.'
							LIMIT 1';
				}
				elseif($type == 'vendor') {
					$sql = 'SELECT product_id FROM #__vm_product p WHERE product_publish="Y" AND (p.attribute IS NULL OR p.attribute="") AND (p.custom_attribute IS NULL OR p.custom_attribute="") AND vendor_id NOT IN ('.$ids.') '.$extra_where1.' LIMIT 1';
				}
			}
		}
		
		if(empty($sql)) return;
		
		$db->setQuery($sql);
		return $db->loadResult();
	
	}
	
	function add_to_cart($product_id,$qty) {

		$qty = (int)$qty;
		$product_id = (int)$product_id;
		if(empty($qty) || empty($product_id)) return;
		
		$is_update = false;
		$description = '';
		foreach($this->cart->items as $k=>$r) {			
			if($r['product_id']==$product_id) {
				$is_update = true;
				$qty += $r['qty'];
				$description = $r['description'];
				break;
			}
		}
		require_once JPATH_ROOT.'/components/com_virtuemart/virtuemart_parser.php';
		require_once( CLASSPATH.'ps_cart.php' );
		$ps_cart = new ps_cart();
		
		if($is_update) {
			global $func;
			$func = 'cartupdate';
			$d = array(
					'quantity'=>$qty,
					'product_id'=>$product_id,
					'prod_id'=>$product_id,
					'description'=>$description, 
			);
			$ok = $ps_cart->update($d);
		}
		else {
		
			$db = JFactory::getDBO();
			$db->setQuery( 'SELECT product_id,product_parent_id, product_name FROM #__vm_product WHERE product_id = '.(int)$product_id );
			$product_row = $db->loadObject();
			$product_id = (int)$product_row->product_id;
			$product_parent_id = (int)$product_row->product_parent_id;
			$product_name = $product_row->product_name;
			if(empty($product_id)) return;
			
			
			$d = array (
					'product_id'=>empty($product_parent_id) ? $product_id : $product_parent_id,
					'prod_id'=>array($product_id),
					'quantity'=>array($qty),
					'category_id'=>0,
					'manufacturer_id'=>0,
					'Itemid'=>1,
			);
			$ok = $ps_cart->add($d);
		}
		
		if($ok) $this->refresh_cart = true;
		return;

	}
	
	
	
	
	
	
	
	function order_total (&$d) {
		require_once(CLASSPATH.'ps_checkout.php');
		$ps_checkout = new ps_checkout();

		$totals = $ps_checkout->calc_order_totals($d);
		return $totals;
	}
	
	function get_product_taxrate($ps_product,$product_id) {
//		if( $this->is_discount_before_tax && PAYMENT_DISCOUNT_BEFORE=='1') return 0;

		$my_taxrate = $ps_product->get_product_taxrate($product_id);
		
		$auth = $_SESSION['auth'];
		// If discounts are applied after tax, but prices are shown without tax,
		// AND tax is EU mode and shopper is not in the EU,
		// then ps_product::get_product_taxrate() returns 0, so $my_taxrate = 0.
		// But, the discount still needs to be reduced by the shopper's tax rate, so we obtain it here:
		if( $auth["show_price_including_tax"]!=1 && !ps_checkout::tax_based_on_vendor_address() ) {
			$db = & JFactory::getDBO();	
			if( $auth["user_id"] > 0 ) {

				$db->setQuery("SELECT state, country FROM #__vm_user_info WHERE user_id='". $auth["user_id"] . "'");
				$tmp = $db->loadObject();
				@$state = $tmp->state;
				@$country = $tmp->country;

				$q = "SELECT tax_rate FROM #__vm_tax_rate WHERE tax_country='$country' ";
				if( !empty($state)) {
					$q .= "AND (tax_state='$state' OR tax_state=' $state ' OR tax_state='-')";
				}
				$db->setQuery($q);
				$my_taxrate = (float)$db->loadResult();
			}
			else {
				$my_taxrate = 0;
			}
		}
		return $my_taxrate;

	}


}

