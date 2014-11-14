<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estorecouponhandler.php';

class AwoCouponVirtuemartCouponHandler extends AwoCouponEstoreCouponHandler {



	var $params = null;
	var $inparams = null;
	
	var $cart = null;
	
	var $vmcoupon_code = '';
	var $vmcart = null;
	var $vmcartData = null;
	var $vmcartPrices = null;
	var $product_total = 0;
	var $default_err_msg = '';

	function AwoCouponVirtuemartCouponHandler () {
		parent::__construct();

		$this->estore = 'virtuemart';
		
		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.'/helpers/cart.php');
		$this->vmcart = VirtueMartCart::getCart(false);
		$this->default_err_msg = JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID');
		
	}

	static function process_coupon_code( $code, &$data, &$prices, $inparams=null ) {
		$instance = new AwoCouponVirtuemartCouponHandler();
		$instance->vmcoupon_code = $code;
		$instance->vmcartData =& $data;
		$instance->vmcartPrices =& $prices;
		$instance->inparams = $inparams;
		
	  	return $instance->process_coupon_helper();
	}
	
	static function remove_coupon_code( $code ) {
		$instance = new AwoCouponVirtuemartCouponHandler();
		$instance->vmcoupon_code = $code;
	  	$instance->session = JFactory::getSession();
		
		$order_id = JRequest::getVar ( 'virtuemart_order_id' );
		if(empty($order_id)){
			$db = JFactory::getDBO();
			$q = 'SELECT `virtuemart_order_id` FROM `#__virtuemart_orders` WHERE `order_number`="'.$_REQUEST['ordernumber'].'"';
			$db->setQuery($q);
			$order_id = $db->loadResult();
		}

		$instance->cleanup_coupon_code_helper($order_id);
		
		return true;
	}
	
	static function order_cancel_check($data) {
		$instance = new AwoCouponVirtuemartCouponHandler();
	  	$instance->session = JFactory::getSession();
	
	
		$order_id = @$data->virtuemart_order_id;
		$order_status = @$data->order_status;
		$instance->cleanup_ordercancel_helper($order_id,$order_status);
		
		return true;
	}
	
	static function process_autocoupon() {
		if(!class_exists('VirtueMartCart')) require JPATH_ROOT.'/components/com_virtuemart/helpers/cart.php';
		$cart = VirtueMartCart::getCart();
		if(empty($cart)) return;

		$instance = new AwoCouponVirtuemartCouponHandler();
		$instance->vmcartPrices = $cart->getCartPrices();
		$code = $instance->process_autocoupon_helper();
		if(empty($code)) return;
		
		$cart->setCouponCode($code);
	}

	static function validate_coupon($_code) {
		$instance = new AwoCouponVirtuemartCouponHandler();

		$db = JFactory::getDBO();
		
		$jlang = JFactory::getLanguage();
		$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, null, true);

		//------START VIRTURMART COUPON SYSTEM ----------------------------------------------------------------------------------------------
		if($_code!='('.JText::_('COM_AWOCOUPON_CP_DISCOUNT_AUTO').')' && $instance->params->get('enable_store_coupon', 0) == 1) {
			$db->setQuery( 'SELECT id FROM #__awocoupon WHERE estore="virtuemart" AND coupon_code='.$db->Quote( awolibrary::dbEscape(trim($_code))) );
			$tmp = $db->loadResult();
			if(empty($tmp)) {
				$db->setQuery('SELECT virtuemart_coupon_id FROM #__virtuemart_coupons WHERE coupon_code='.$db->Quote( awolibrary::dbEscape(trim($_code))) );
				$tmp = $db->loadResult();
				if(!empty($tmp)) {
					// run coupon through virtuemart system
					$code = $instance->vmcart->couponCode;
					$instance->initialize_coupon();
					$instance->vmcart->couponCode = $code;
					return null;
				}
				
				$_awo_displaying_coupon = '';
				$session = JFactory::getSession();
				$coupon_row = $session->get('coupon', '', 'awocoupon');
				if(!empty($coupon_row)) { 
					 $coupon_row = unserialize($coupon_row); 
					 $_awo_displaying_coupon = $coupon_row['coupon_code'];
				}
				if($_code!=$_awo_displaying_coupon) return JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID');
				//if(strpos($_code,',')===false) return JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID');
			}
		}
		//------END   VIRTURMART COUPON SYSTEM ----------------------------------------------------------------------------------------------
		
		// reset cache so coupon code can be processed
		if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.'/helpers/calculationh.php');
		$calculator = calculationHelper::getInstance();
		if(method_exists($calculator, 'setCartPrices')) $calculator->setCartPrices(array()); //this line deletes the cache	
		
		
		return '';
	}

	
	function initialize_coupon() {
		parent::initialize_coupon();
		
		// remove from vm session so coupon code is not called constantly
		$this->vmcart->couponCode = '';
		$this->vmcart->setCartIntoSession();
		
	}
	
	protected function finalize_coupon($master_output) {
		$session_array = $this->calc_coupon_session($master_output);
		if(empty($session_array)) return false;
		
		$this->session->set('coupon', serialize($session_array), 'awocoupon');
		
		// update vm session so coupon code
		$this->vmcart->couponCode = $session_array['coupon_code'];
		$this->vmcart->setCartIntoSession();

		$this->finalize_coupon_store($session_array);
		
		return true;
		
	}
	protected function finalize_coupon_store ($coupon_session) {

		require_once(JPATH_VM_ADMINISTRATOR.DS.'version.php'); 
		$vmversion = VmVersion::$RELEASE;	
		if(preg_match('/\d/',substr($vmversion,-1))==false) $vmversion = substr($vmversion,0,-1);
		
		$coupon_taxbill = 0;
		if (version_compare($vmversion, '2.0.20', '>=')) {
			if($this->coupon_discount_before_tax && !empty($this->vmcartData['taxRulesBill'])) {
				$billtaxrate = 0;
				foreach($this->vmcartData['taxRulesBill'] as $rate) {
					if($rate['calc_value_mathop']=='+%') $billtaxrate += $rate['calc_value'];
					elseif($rate['calc_value_mathop']=='-%') $billtaxrate -= $rate['calc_value'];
				}
				if(!empty($billtaxrate)) {
					$billtaxrate /= 100;
					$coupon_taxbill = ($coupon_session['product_discount'] + $coupon_session['shipping_discount'])*$billtaxrate;
				}
				
			}
		}

		$negative_multiplier = version_compare($vmversion, '2.0.21', '>=') ? -1 : 1;

		// update cart objects
		$this->vmcartData['couponCode'] = $coupon_session['coupon_code'];
		$this->vmcartData['couponDescr'] = '';
		
		$this->vmcartPrices['couponTax'] = ($coupon_session['product_discount_tax'] + $coupon_session['shipping_discount_tax'] + $coupon_taxbill) * $negative_multiplier;
		$this->vmcartPrices['couponValue'] = ($coupon_session['product_discount_notax'] + $coupon_session['shipping_discount_notax']) * $negative_multiplier;
		$this->vmcartPrices['salesPriceCoupon'] = ($coupon_session['product_discount'] + $coupon_session['shipping_discount'] + $coupon_taxbill) * $negative_multiplier;
		if(isset($this->vmcartPrices['billSub'])) $this->vmcartPrices['billSub'] -= $this->vmcartPrices['couponValue'];
		if(isset($this->vmcartPrices['billTaxAmount'])) $this->vmcartPrices['billTaxAmount'] -= $this->vmcartPrices['couponTax'];
		if(isset($this->vmcartPrices['billTotal'])) $this->vmcartPrices['billTotal'] -= $this->vmcartPrices['salesPriceCoupon'];
		
		$this->vmcartData['vmVat'] = false;

		if (version_compare($vmversion, '2.0.20', '>=')) {
			{ # get vm tax calculation
				$vm_calculated_taxes_coupon = 0;
				
				if (!class_exists('CurrencyDisplay')) require JPATH_VM_ADMINISTRATOR.'/helpers/currencydisplay.php';
				$currencyDisplay = CurrencyDisplay::getInstance();
			
				if(!empty($this->vmcartData['VatTax'])) {
					foreach($this->vmcartData['VatTax'] as $vattax){
						unset($couponamt);
						if (isset($vattax['subTotal'])) $vattax['percentage'] = $vattax['subTotal'] / $this->vmcartPrices['salesPrice'];
						$vattax['DBTax'] = isset($vattax['DBTax']) ? $vattax['DBTax'] : 0;
						if (!isset($vattax['discountTaxAmount']) && isset($vattax['calc_value'])) {
							$couponamt = round(($this->vmcartPrices['salesPriceCoupon'] * $vattax['percentage'] + abs($vattax['DBTax'])) / (100 + $vattax['calc_value']) * $vattax['calc_value'],$currencyDisplay->_priceConfig['taxAmount'][1]);
						}
						if (isset($couponamt)) $vm_calculated_taxes_coupon += $couponamt;
					}
				}
			}
			
			$coupon_offset = $this->vmcartPrices['couponTax'] - $vm_calculated_taxes_coupon ;
			if(!empty($coupon_offset)) {
				$this->vmcartData['vmVat'] = true;
				if(!isset($this->vmcartData['VatTax'])) $this->vmcartData['VatTax'] = array();
				$this->vmcartData['VatTax'][] = array(
					'virtuemart_calc_id'=>0,
					'calc_name'=>'',
					'calc_value_mathop'=>'',
					//'calc_value'=>0,
					'calc_currency'=>'',
					'ordering'=>'',
					'discountTaxAmount'=>$coupon_offset,
				);
			}
		}

	}
	protected function finalize_autocoupon($coupon_codes) {
		foreach($coupon_codes as $coupon) {
			$this->vmcoupon_code = $coupon->coupon_code;
			//$_POST['coupon_code'] = $coupon->coupon_code;
			$this->process_coupon_helper();
		}
	}
		
	protected function getProduct($virtuemart_product_id) {
		JModel::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'models');
		$model = JModel::getInstance('Product', 'VirtueMartModel');
		$product = $model->getProduct($virtuemart_product_id, true, false);

		if ( VmConfig::get('oncheckout_show_images')){
			$model->addImages($product,1);

		}
		return $product;
	}

	protected function getuniquecartstring($coupon_code=null) {
		if(empty($coupon_code)) @$coupon_code = $this->vmcart->couponCode;
		if(!empty($coupon_code)) {
			$user = JFactory::getUser();
			$user_email = !empty($this->vmcart->BT['email']) ? $this->vmcart->BT['email'] : '';
			$string = $this->vmcartPrices['basePriceWithTax'].'|'.$this->vmcartPrices['salesPriceShipment'].'|'.$coupon_code.'|'.$user->id.'|'.$user_email;
			foreach($this->vmcart->products as $k=>$r) $string .= '|'.$k.'|'.$r->quantity;
			return $string.'|ship|'.@$this->vmcart->virtuemart_shipmentmethod_id;
		}
		return;
	}
	protected function getuniquecartstringauto() {
		$user = JFactory::getUser();
		$string = $this->vmcartPrices['basePriceWithTax'].'|'.$this->vmcartPrices['salesPriceShipment'].'|'.$user->id;
		foreach($this->vmcart->products as $k=>$r) $string .= '|'.$k.'|'.$r->quantity;
		return $string.'|ship|'.@$this->vmcart->virtuemart_shipmentmethod_id;
	}
	
	protected function get_storeshoppergroupids($user_id) {
        if(empty($user_id)) return array(1);
		$db = JFactory::getDBO();	
		$sql = 'SELECT virtuemart_shoppergroup_id FROM #__virtuemart_vmuser_shoppergroups WHERE virtuemart_user_id='.(int)$user_id;
		$db->setQuery($sql);
		$shopper_group_ids = $db->loadResultArray();
		if(empty($shopper_group_ids)) {
			$db->setQuery('SELECT virtuemart_shoppergroup_id FROM #__virtuemart_shoppergroups WHERE published=1 AND `default`=1');
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
				$db->setQuery('SELECT virtuemart_product_id as product_id FROM #__virtuemart_products WHERE product_parent_id IN ('.$ids_to_check.')');
				$tmp = $db->loadObjectList();
				foreach($tmp as $tmp2) $rtn[$tmp2->product_id] = $tmp2->product_id;
			}
		}
		return $rtn;
	}
	protected function get_storecategory($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT virtuemart_category_id AS category_id,virtuemart_product_id AS product_id
				  FROM #__virtuemart_product_categories
				 WHERE virtuemart_product_id IN ('.$ids.')';
		$db->setQuery($sql);
		$tmp1 = $db->loadObjectList();
		
		// get category list of parent products
		$sql = 'SELECT c.virtuemart_category_id AS category_id,p.virtuemart_product_id AS product_id
				  FROM #__virtuemart_products p 
				  JOIN #__virtuemart_product_categories c ON c.virtuemart_product_id=p.product_parent_id
				 WHERE p.virtuemart_product_id IN ('.$ids.')';
		$db->setQuery($sql);
		$tmp2 = $db->loadObjectList();
		
		return array_merge($tmp1,$tmp2);
	}
	protected function get_storemanufacturer($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT virtuemart_manufacturer_id AS manufacturer_id,virtuemart_product_id AS product_id
				  FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id IN ('.$ids.')';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	protected function get_storevendor($ids) {
		$db = JFactory::getDBO();	
		$sql = 'SELECT virtuemart_vendor_id AS vendor_id,virtuemart_product_id AS product_id 
				  FROM #__virtuemart_products WHERE virtuemart_product_id IN ('.$ids.')';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	protected function get_storeshipping() {
		return (object) array(
			'shipping_id'=>@$this->vmcart->virtuemart_shipmentmethod_id,
			'total_notax'=>(float)$this->vmcartPrices['shipmentValue'],
			'total'=>(float)$this->vmcartPrices['salesPriceShipment'],
		);
	}
	protected function get_submittedcoupon() { 
		$awosess = $this->session->get('coupon', '', 'awocoupon');
		if(!empty($awosess) ){
			$awosess = unserialize($awosess);
			if($awosess['coupon_code']==$this->vmcoupon_code) return '';
		}
		return $this->vmcoupon_code; 
	}
	//protected function get_submittedcoupon() { return @$_POST['coupon_code']; }
	protected function get_orderemail($order_id) {
		return !empty($this->vmcart->BT['email']) ? $this->vmcart->BT['email'] : '';
	}

	protected function is_customer_num_uses($coupon_id,$max_num_uses,$customer_num_uses,$is_parent=false) {
		
		$email = !empty($this->vmcart->BT['email']) ? $this->vmcart->BT['email'] : '';
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
		//		  LEFT JOIN #__virtuemart_orders o on o.virtuemart_order_id=h.order_id
		//		  LEFT JOIN #__virtuemart_order_userinfos u on u.virtuemart_order_id=o.virtuemart_order_id AND u.email="'.awolibrary::dbEscape($email).'" and u.address_type="BT"
		//		 WHERE h.estore="'.$this->estore.'" AND h.coupon_id='.$coupon_id.'
		//		 GROUP BY h.coupon_id';
	}

	//protected function get_submittedcoupon() { return @$_POST['coupon_code']; }
	protected function define_cart_items() {
		$this->cart = new stdClass();
		$this->cart->items = array();
		$this->cart->items_def = array();

		//if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.'/helpers/calculationh.php');
		//$calculator = calculationHelper::getInstance();
		
		/*{ # seyi_code
			$extratax = $this->vmcart->cartData['DBTaxRulesBill'];
			if(!empty($extratax)) {
				foreach($extratax as $it) {
					if($it['calc_descr'] = 'Arizona Sales Tax') $extratax = $it['calc_value'];
				}
			}
		}*/
		foreach ($this->vmcart->products as $cartpricekey=>$product){
			$productId = $product->virtuemart_product_id;
			if(!empty($product->param)) {
			// stockable check
				foreach($product->param as $productparam) {
					if(!empty($productparam['stockable']['child_id'])) {
						$tmp = (int)$productparam['stockable']['child_id'];
						if($tmp>0) $productId = $tmp;
					}
				}
			}
			if (empty($product->quantity) || empty( $productId )){
				continue;
			}
			$this->vmcartPrices[$cartpricekey]['discountAmount'] = round($this->vmcartPrices[$cartpricekey]['discountAmount'],2);
			$product_discount = empty($this->vmcartPrices[$cartpricekey]['discountAmount']) ? 0 : $this->vmcartPrices[$cartpricekey]['discountAmount'];
			if(empty($product_discount)) {
				$product_table = $this->getProduct($productId);
				$product_discount = $product_table->product_special;
			}
			//$variantmod = '';//$calculator->parseModifier($product->variant);
			//$cartpricekey = $productId.$variantmod;
			//exit($cartpricekey);
			
			
			//$this->cart->items_def[$productId] = array();
			//if(!empty($extratax)) $this->vmcartPrices[$cartpricekey]['taxAmount'] = $this->vmcartPrices[$cartpricekey]['salesPrice'] - ($this->vmcartPrices[$cartpricekey]['salesPrice']/(1+($extratax/100)));
			$this->cart->items_def[$productId]['product'] = $productId;
			$this->cart->items [] = array(
				'product_id' => $productId,
				'cartpricekey' => $cartpricekey,
				'discount' => $product_discount,
				'product_price' => $this->vmcartPrices[$cartpricekey]['salesPrice'],
				//'product_price_notax' => $this->vmcartPrices[$cartpricekey]['priceWithoutTax'],
				'product_price_notax' => $this->vmcartPrices[$cartpricekey]['salesPrice']-$this->vmcartPrices[$cartpricekey]['taxAmount'],
				'product_price_tax' => $this->vmcartPrices[$cartpricekey]['subtotal_with_tax'],
				'qty' => $product->quantity,
				
				'orig_product_price'=> $this->vmcartPrices[$cartpricekey]['salesPrice'],
			);
			$this->product_total += $product->quantity*$this->vmcartPrices[$cartpricekey]['salesPrice'];
		}	
//printrx($this->cart->items);
	}
	
	protected function return_false($key) {
		// strip out Virtuemart successful message
		$orig_messages = $messages = JFactory::getApplication()->getMessageQueue();
		if(!empty($messages)) {
			foreach($messages as $k=>$message) {
				if($message['message']==JText::_('COM_VIRTUEMART_CART_COUPON_VALID')) {
					unset($messages[$k]);
				}
			}
			if($orig_messages != $messages) {
				$session = JFactory::getSession();
				$session->set('application.queue', empty($messages) ? null : $messages);
				JFactory::getApplication()->set('_messageQueue',empty($messages) ? '' : $messages);
			}
		}
		
		return parent::return_false($key);
	}
	
	
	function buyxy_getproduct($mode,$type,$assetlist) {
	
		$db = JFactory::getDBO();

		$the_product = 0;
		$ids = implode(',',$assetlist);
		if(empty($ids)) return;
		
		if($mode == 'include') {
			if($type == 'product') $the_product = current($assetlist);
			elseif($type== 'category') {
				$db->setQuery('SELECT c.virtuemart_product_id FROM #__virtuemart_product_categories c JOIN #__virtuemart_products p ON p.virtuemart_product_id=c.virtuemart_product_id WHERE p.published=1 AND c.virtuemart_category_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'manufacturer') {
				$db->setQuery('SELECT m.virtuemart_product_id FROM #__virtuemart_product_manufacturers m JOIN #__virtuemart_products p ON p.virtuemart_product_id=m.virtuemart_product_id WHERE p.published=1 AND m.virtuemart_manufacturer_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'vendor') {
				$db->setQuery('SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published=1 AND virtuemart_vendor_id IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
		}
		elseif($mode == 'exclude') {
			if($type == 'product') {
				$db->setQuery('SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published=1 AND virtuemart_product_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type== 'category') {
				$db->setQuery('SELECT c.virtuemart_product_id FROM #__virtuemart_product_categories c JOIN #__virtuemart_products p ON p.virtuemart_product_id=c.virtuemart_product_id WHERE p.published=1 AND c.virtuemart_category_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'manufacturer') {
				$db->setQuery('SELECT m.virtuemart_product_id FROM #__virtuemart_product_manufacturers m JOIN #__virtuemart_products p ON p.virtuemart_product_id=m.virtuemart_product_id WHERE p.published=1 AND m.virtuemart_manufacturer_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
			elseif($type == 'vendor') {
				$db->setQuery('SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published=1 AND virtuemart_vendor_id NOT IN ('.$ids.') LIMIT 1');
				$the_product = $db->loadResult();
			}
		}

		return $the_product;
	
	}
	
	function add_to_cart($product_id,$qty) {
		$qty = (int)$qty;
		$product_id = (int)$product_id;
		if(empty($qty) || empty($product_id)) return;
		
		$cart = VirtueMartCart::getCart();
		if (!$cart) return;
		
		$is_update = false; $cartpricekey = 0;
		foreach($this->cart->items as $k=>$r) {			
			if($r['product_id']==$product_id) {
				$is_update = true;
				$qty += $r['qty'];
				$cartpricekey = $r['cartpricekey'];
				break;
			}
		}
		
		if($is_update && !empty($cartpricekey) ) {
			$_REQUEST['quantity'] = $qty;
			$cart->updateProductCart($cartpricekey);
		}
		else {
			$_REQUEST['quantity'][0] = $qty;
			$cart->add(array($product_id));
		}

		$cart->couponCode = '';
		if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.'/helpers/calculationh.php');
		$calculator = calculationHelper::getInstance();
		if(method_exists($calculator, 'setCartPrices')) $calculator->setCartPrices(array()); //this line deletes the cache	
		$this->vmcartPrices = $calculator->getCheckoutPrices($cart);

		return;
	}

}

