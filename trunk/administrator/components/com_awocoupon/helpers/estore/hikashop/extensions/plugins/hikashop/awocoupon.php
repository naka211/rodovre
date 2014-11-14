<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgHikashopAwoCoupon extends JPlugin {

	function plgHikashopAwoCoupon(& $subject, $config){ parent::__construct($subject, $config); }
	
	
	function onBeforeCouponLoad($coupon_code,&$continue_execution) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponHikashopCouponHandler::validate_coupon($coupon_code,$continue_execution);
		}
		return null;
	}
	
	
	function onAfterCartProductsLoad ( &$cart ) {
		if(!empty($cart->coupon)) return;
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			$coupon = 'discount_auto_load';
			$total = &$cart->full_total;
			$zones = null;
			$products = &$cart->products;
			$display_error = '';
			$error_message = '';
			$continue_execution = true;
			$rtn = AwoCouponHikashopCouponHandler::process_coupon_code($coupon,$total,$zones,$products,$display_error,$error_message,$continue_execution );
			if(!empty($rtn)) {
				if(is_string($rtn) && $rtn=='reprocess') {
					$cartClass = hikashop_get('class.cart');
					$done = $cartClass->loadFullCart(true);
					return;
				}
				else $cart->awocoupon_discount = 1;
			}
		}
		return null;
	}
	
	
	function onBeforeCouponCheck(&$coupon,&$total,&$zones,&$products,&$display_error, &$error_message, & $continue_execution) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			$rtn = AwoCouponHikashopCouponHandler::process_coupon_code($coupon,$total,$zones,$products,$display_error, $error_message,$continue_execution );
			if(!empty($rtn)) {
				if(is_string($rtn) && $rtn=='reprocess') {
					$cartClass = hikashop_get('class.cart');
					$done = $cartClass->loadFullCart(true);
					return;
				}
			}
		}
		return null;
	}
	
	
	function onAfterCartShippingLoad(&$cart) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			$couponHandler = new AwoCouponHikashopCouponHandler($cart);
			$couponHandler->finalize_coupon_store_calc($cart);
		}
		return null;
	}
	
	
	function onBeforeCartUpdate(&$cartclass, &$cart, $product_id, $quantity, $add, $type,$resetCartWhenUpdate,$force,&$do) {
		$is_remove = JRequest::getInt('removecoupon',0);
		if($is_remove) {
			$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/couponhandler.php';
			if(file_exists($awo_file)) {
				require_once $awo_file;
				$couponHandler = new AwoCouponHikashopCouponHandler();
				$couponHandler->initialize_coupon($cartclass);
			}
		}
		return null;
	}
	
	
	function onAfterOrderCreate(& $order,&$send_email) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			AwoCouponHikashopCouponHandler::remove_coupon_code($order);
		}
		
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/giftcerthandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			AwoCouponHikashopGiftcertHandler::process($order);
		}
		
		return null;
	}
	
	
	function onAfterOrderUpdate(& $order,&$send_email) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/giftcerthandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			AwoCouponHikashopGiftcertHandler::process($order);
		}
	
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/hikashop/couponhandler.php';
		if(file_exists($awo_file)) {
			require_once $awo_file;
			return AwoCouponHikashopCouponHandler::order_cancel_check($order);
		}

		return null;
	}
	
	
	
	function onHikashopBeforeDisplayView(&$view){ if(@$view->ctrl=='checkout') ob_start(); }
	function onHikashopAfterDisplayView(&$view){
		if(@$view->ctrl=='checkout'){
			$html = ob_get_clean();
			if(strpos($html,'hikashop_checkout_coupon_input')===false) {
				$html = str_replace(
					'<span class="hikashop_checkout_coupon" id="hikashop_checkout_coupon">',
					'<span class="hikashop_checkout_coupon" id="hikashop_checkout_coupon">
						'.JText::_('HIKASHOP_ENTER_COUPON').' <input id="hikashop_checkout_coupon_input" type="text" name="coupon" value="" />
						<input type="submit" class="button hikashop_cart_input_button" name="refresh" value="'.JText::_('ADD').'" onclick="return hikashopCheckCoupon(\'hikashop_checkout_coupon_input\');"/><br />',
					$html
				);
						//'.$view->cart->displayButton(JText::_('ADD'),'refresh',$view->params,hikashop_completeLink('checkout'),'','onclick="return hikashopCheckCoupon(\'hikashop_checkout_coupon_input\');"').'<br />',
			
			}
			echo $html;
		}
	}

}

