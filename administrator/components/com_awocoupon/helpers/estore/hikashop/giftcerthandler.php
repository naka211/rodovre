<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estoregiftcerthandler.php';

class AwoCouponHikashopGiftcertHandler extends AwoCouponEstoreGiftcertHandler  {

	var $estore = 'hikashop';
	var $order = null;
	
	function AwoCouponHikashopGiftcertHandler () {
		parent::__construct();
		
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		require_once JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php';
	}

	static function process( $order ) {
		$instance = new AwoCouponHikashopGiftcertHandler();
		$instance->order = $instance->get_storeorder($order);
		
	  	return $instance->process_order_status_change();
	}
	
	static function process_resend ( $order, $rows) {
		$instance = new AwoCouponHikashopGiftcertHandler();
		$instance->order = $instance->get_storeorder($order);
		$instance->is_entry_new = false;
		
		return $instance->generate_auto_email($rows);
	}


	protected function process_order_status_change(  ) {

		$auto_order_status = $this->params->get('giftcert_order_status', 'C') ;
		
		if( $this->order->order_status==$auto_order_status ) {
		// order confirmed now mail out gift certs if any
			
			$sql = 'SELECT i.order_product_id AS order_item_id,i.order_id,i.order_product_quantity AS product_quantity,
							uh.user_cms_id AS user_id,uh.user_email AS email,u.address_firstname AS first_name,
							u.address_lastname AS last_name,ap.expiration_number,ap.expiration_type,ap.coupon_template_id,
							ap.profile_id,i.product_id,i.order_product_name AS order_item_name,ap.vendor_name,ap.vendor_email
					  FROM #__hikashop_order_product i 
					  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.product_id
					  JOIN #__hikashop_order o ON o.order_id=i.order_id
					  LEFT JOIN #__hikashop_address u ON u.address_id=o.order_billing_address_id
					  LEFT JOIN #__hikashop_user uh ON uh.user_id=o.order_user_id
					  LEFT JOIN #__awocoupon_giftcert_order g ON g.order_id=i.order_id AND g.estore="hikashop" AND g.email_sent=1
					 WHERE i.order_id='.$this->order->order_id.' AND g.order_id IS NULL AND ap.published=1 AND ap.estore="hikashop"
					 GROUP BY i.order_product_id';
			$this->db->setQuery( $sql );
			$rows = $this->db->loadObjectList();
			$this->generate_auto_email($rows);

		}
	}
	
	
	
	
	
	
	protected function formatcurrency($val) {
			
		$currencyClass = hikashop_get('class.currency');
		return $currencyClass->format($val);

	}

	
	protected function get_storeorder($in) {
		if(!isset($in->order_number)) {
			$this->db->setQuery('SELECT order_number,order_full_price,order_created FROM #__hikashop_order WHERE order_id='.$in->order_id);
			$tmp = $this->db->loadObject();
			if(!empty($tmp)) {
				$in->order_number = $tmp->order_number;
				$in->order_full_price = $tmp->order_full_price;
				$in->order_created = $tmp->order_created;
			}
		}
		
		$order = new stdClass();
		$order->order_id = $in->order_id;
		$order->order_number = $in->order_number;
		$order->order_status = $in->order_status;
		$order->order_total = $in->order_full_price;
		$order->created_on = $in->order_created;
		
		return $order;
	}
	protected function get_storename() {
		$config = JFactory::getConfig ();
		return $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'sitename' );
	}
	protected function get_storeemail() {
		$config = JFactory::getConfig ();
		$hk_config =& hikashop_config();
		return $hk_config->get('from_email',$config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'mailfrom' ));

	}
	protected function get_orderstatuslist(){
		$sql = 'SELECT category_id AS order_status_id, category_namekey AS value, category_name AS text
				  FROM #__hikashop_category
				 WHERE category_type="status" 
				 ORDER BY category_ordering';
		$this->db->setQuery($sql);
		$orderstatuses = array();
		foreach ($this->db->loadObjectList() as $_ordstat) $orderstatuses[$_ordstat->value] = $_ordstat->text;
		
		return $orderstatuses;
	}
	protected function get_orderlink() {
		return 'index.php?option=com_hikashop&ctrl=order&task=show&cid='.$this->order->order_id;
	}
	
	
	
	
	
	protected function getproductattributes($row) {
		$recipient_email = $row->email;
		$recipient_name = $row->first_name.' '.$row->last_name;
		$message = '';

		$name_id = $this->params->get('hikashop_giftcert_field_recipient_name', '');
		$email_id = $this->params->get('hikashop_giftcert_field_recipient_email', '');
		$message_id = $this->params->get('hikashop_giftcert_field_recipient_message', '');
		
		if(!empty($email_id)) {
			$this->db->setQuery('SELECT '.$email_id.' FROM #__hikashop_order_product WHERE order_product_id='.$row->order_item_id);
			$tmp = $this->db->loadResult();
			if(!empty($tmp) && JMailHelper::isEmailAddress($tmp)) $recipient_email = $tmp;
		}
		
		if(!empty($name_id)) {
			$this->db->setQuery('SELECT '.$name_id.' FROM #__hikashop_order_product WHERE order_product_id='.$row->order_item_id);
			$tmp = $this->db->loadResult();
			if(!empty($tmp)) $recipient_name = $tmp;
		}
		
		if(!empty($message_id)) {
			$this->db->setQuery('SELECT '.$message_id.' FROM #__hikashop_order_product WHERE order_product_id='.$row->order_item_id);
			$message = $this->db->loadResult();
		}
//echo $recipient_email.'<br>'.$recipient_name.'<br>'.$message;exit;
		
		return array('recipient_name'=>$recipient_name,'email'=>$recipient_email,'message'=>$message);
			
	}





}

