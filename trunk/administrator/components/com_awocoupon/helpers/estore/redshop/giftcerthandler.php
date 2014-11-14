<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estoregiftcerthandler.php';

class AwoCouponRedshopGiftcertHandler extends AwoCouponEstoreGiftcertHandler  {

	var $estore = 'redshop';
	var $order = null;
	
	function AwoCouponRedshopGiftcertHandler () {
		parent::__construct();
		require_once JPATH_ADMINISTRATOR.'/components/com_redshop/helpers/redshop.cfg.php';
	}

	static function process( $order_id ) {
		$instance = new AwoCouponRedshopGiftcertHandler();
		$instance->order = $instance->get_storeorder($order_id);
		
	  	return $instance->process_order_status_change();
	}
	
	static function process_resend ( $order, $rows) {
		$instance = new AwoCouponRedshopGiftcertHandler();
		$instance->order = $instance->get_storeorder($order);
		$instance->is_entry_new = false;
		
		return $instance->generate_auto_email($rows);
	}




	
	protected function process_order_status_change( ) {
		//$auto_order_status = $params->get('giftcert_order_status', 'C') ;
		
		//if( $order->order_status==$auto_order_status ) {
		//if( $order->order_payment_status == "Paid" ) {

		// order confirmed now mail out gift certs if any
			
			$sql = 'SELECT i.order_item_id,i.order_id,i.product_item_price,i.product_quantity,u.user_id,u.user_email as email,u.firstname as first_name,u.lastname as last_name,
							ap.expiration_number,ap.expiration_type,ap.coupon_template_id,i.order_item_currency,ap.profile_id,
							i.product_id,i.product_attribute,i.order_item_name,ap.vendor_name,ap.vendor_email
					  FROM #__redshop_order_item i 
					  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.product_id
					  JOIN #__redshop_order_users_info u ON u.order_id=i.order_id AND u.address_type="BT"
					  LEFT JOIN #__awocoupon_giftcert_order g ON g.order_id=i.order_id AND g.estore="redshop" AND g.email_sent=1
					 WHERE i.order_id='.$this->order->order_id.' AND g.order_id IS NULL AND ap.published=1 AND i.is_giftcard=0 AND ap.estore="redshop"
					 GROUP BY i.order_item_id';
			$this->db->setQuery( $sql ); 
			$rows = $this->db->loadObjectList();
			$this->generate_auto_email($rows);

		//}
	}


	
	
	
	protected function formatcurrency($product_price,$currency_symbol=REDCURRENCY_SYMBOL) {

		$price = '';
		if (is_numeric ($product_price)) {
			if(CURRENCY_SYMBOL_POSITION == 'front'){
				$price =  $currency_symbol.number_format((double)$product_price,PRICE_DECIMAL,PRICE_SEPERATOR,THOUSAND_SEPERATOR);
			}elseif(CURRENCY_SYMBOL_POSITION == 'behind'){
				$price =  number_format((double)$product_price,PRICE_DECIMAL,PRICE_SEPERATOR,THOUSAND_SEPERATOR).$currency_symbol;
			}elseif(CURRENCY_SYMBOL_POSITION == 'none'){
				$price =  number_format((double)$product_price,PRICE_DECIMAL,PRICE_SEPERATOR,THOUSAND_SEPERATOR);
			}else{
				$price =  $currency_symbol.number_format((double)$product_price,PRICE_DECIMAL,PRICE_SEPERATOR,THOUSAND_SEPERATOR);
			}
		}
		return $price;
	}
	
	protected function get_storeorder($in) {
		if(!is_object($in)) {
			$this->db->setQuery( 'SELECT * FROM #__redshop_orders WHERE order_id='.(int)$in );
			$in = $this->db->loadObject();
		}

		$order = new stdClass();
		$order->order_id = $in->order_id;
		$order->order_number = $in->order_number;
		$order->order_status = $in->order_status;
		$order->order_total = $in->order_total;
		$order->created_on = $in->cdate;
		
		return $order;
	}
	protected function get_storename() { return SHOP_NAME; }
	protected function get_storeemail() {
		$config = JFactory::getConfig ();
		return $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'mailfrom' );
	}
	protected function get_orderstatuslist(){
		$sql = 'SELECT order_status_code AS value, order_status_name AS text FROM #__redshop_order_status';
		$this->db->setQuery($sql);
		
		$orderstatuses = array();
		foreach ($this->db->loadObjectList() as $_ordstat) $orderstatuses[$_ordstat->value] = $_ordstat->text;
		
		return $orderstatuses;
	}
	protected function get_orderlink() {
		return 'index.php?option=com_redshop&view=order_detail&layout=receipt&oid='.$this->order->order_id;
	}
	
	
	

	protected function getproductattributes($row) {
		$recipient_email = $row->email;
		$recipient_name = $row->first_name.' '.$row->last_name;
		$message = '';
	
		$name_id = (int)$this->params->get('redshop_giftcert_field_recipient_name', 0);
		$email_id = (int)$this->params->get('redshop_giftcert_field_recipient_email', 0);
		$message_id = (int)$this->params->get('redshop_giftcert_field_recipient_message', 0);

		$sql = 'SELECT fd.*,f.field_title,f.field_type,f.field_name 
				  FROM #__redshop_fields_data AS fd 
				  LEFT JOIN #__redshop_fields AS f ON f.field_id=fd.fieldid
				 WHERE fd.itemid='.$row->order_item_id.'
				   AND fd.section=12
				   AND fd.fieldid IN ('.$name_id.','.$email_id.','.$message_id.')';
		$this->db->setQuery($sql);
		$custom_attr = $this->db->loadObjectlist('fieldid');
	
	
		$attrlist = array();
		if(!empty($custom_attr[$email_id])) {
			$tmp = trim($custom_attr[$email_id]->data_txt);
			if(!empty($tmp) && JMailHelper::isEmailAddress($tmp)) $recipient_email = $tmp;
			
			$tmp = trim($custom_attr[$name_id]->data_txt);
			if(!empty($tmp)) $recipient_name = $tmp;
			
			@$message = trim($custom_attr[$message_id]->data_txt);
		}
		
		return array('recipient_name'=>$recipient_name,'email'=>$recipient_email,'message'=>$message);
	}

		

}

