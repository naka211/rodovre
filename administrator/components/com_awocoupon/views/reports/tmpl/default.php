<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 

switch ($this->report_type) {
	case 'coupon_list': echo $this->loadTemplate('coupon_list'); break;
	case 'purchased_giftcert_list': echo $this->loadTemplate('purchased_giftcert_list'); break;
	case 'coupon_vs_total': echo $this->loadTemplate('coupon_usage_total'); break;
	case 'coupon_vs_location':  echo $this->loadTemplate('coupon_usage_location'); break;
	case 'history_uses_coupons':  echo $this->loadTemplate('history_uses_coupons'); break;
	case 'history_uses_giftcerts':  echo $this->loadTemplate('history_uses_giftcerts'); break;
	default: echo $this->loadTemplate('main');
}
