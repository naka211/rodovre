<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estoregiftcerthandler.php';

class AwoCouponVirtuemartGiftcertHandler  extends AwoCouponEstoreGiftcertHandler{

	var $estore = 'virtuemart';
	var $order = null;
	
	function AwoCouponVirtuemartGiftcertHandler () {
		parent::__construct();
		if (!class_exists( 'VmConfig' )) require JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php';
		VmConfig::get('tester_for_vm2016','',true); // needed in vm2016 otherwise the configuration variables may not have been loaded and never will be if called

	}

	static function process( $order, $old_order_status ) {
		$instance = new AwoCouponVirtuemartGiftcertHandler();
		$instance->order = $instance->get_storeorder($order);
		
	  	return $instance->process_order_status_change();
	}
	
	static function process_resend ( $order, $rows) {
		$instance = new AwoCouponVirtuemartGiftcertHandler();
		$instance->order = $instance->get_storeorder($order);
		$instance->is_entry_new = false;
		
		return $instance->generate_auto_email($rows);
	}
	
	/*protected function process_order_status_change( ) {
		$auto_order_status = $this->params->get('giftcert_order_status', 'C') ;
		
		if( $this->order->order_status==$auto_order_status ) {
		// order confirmed now mail out gift certs if any
			
			$sql = 'SELECT i.virtuemart_order_item_id AS order_item_id,i.virtuemart_order_id AS order_id,p.product_parent_id,i.product_item_price,i.product_quantity,
							u.virtuemart_user_id AS user_id,u.email,u.first_name,u.last_name,ap.expiration_number,ap.expiration_type,ap.coupon_template_id,
							i.order_item_currency,ap.profile_id,i.virtuemart_product_id AS product_id,i.product_attribute,i.order_item_name,ap.vendor_name,ap.vendor_email
							
					  FROM #__virtuemart_order_items i 
					  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.virtuemart_product_id
					  JOIN #__virtuemart_products p ON p.virtuemart_product_id=i.virtuemart_product_id
					  JOIN #__virtuemart_order_userinfos u ON u.virtuemart_order_id=i.virtuemart_order_id AND u.address_type="BT"
					  LEFT JOIN #__awocoupon_giftcert_order g ON g.order_id=i.virtuemart_order_id AND g.estore="virtuemart" AND g.email_sent=1
					 WHERE i.virtuemart_order_id='.$this->order->order_id.' AND g.order_id IS NULL AND ap.published=1 AND ap.estore="virtuemart"
					 GROUP BY i.virtuemart_order_item_id';
			$this->db->setQuery( $sql ); 
			$rows = $this->db->loadObjectList();
			$this->generate_auto_email($rows);

		}
	}*/
	//T.Trung
    
    protected function process_order_status_change( ) {
		$auto_order_status = $this->params->get('giftcert_order_status', 'C') ;
		
		if( $this->order->order_status==$auto_order_status ) {
		// order confirmed now mail out gift certs if any
			
			$sql = 'SELECT i.virtuemart_order_item_id AS order_item_id,i.virtuemart_order_id AS order_id,p.product_parent_id,i.product_item_price,i.product_quantity,
							u.virtuemart_user_id AS user_id,u.email1 as email, u.first_name, u.last_name, u.message1 as message, ap.expiration_number, ap.expiration_type, ap.coupon_template_id,
							i.order_item_currency,ap.profile_id,i.virtuemart_product_id AS product_id,i.product_attribute,i.order_item_name,ap.vendor_name,ap.vendor_email
							
					  FROM #__virtuemart_order_items i 
					  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.virtuemart_product_id
					  JOIN #__virtuemart_products p ON p.virtuemart_product_id=i.virtuemart_product_id
					  JOIN #__virtuemart_order_userinfos u ON u.virtuemart_order_id=i.virtuemart_order_id AND u.address_type="ST"
					  LEFT JOIN #__awocoupon_giftcert_order g ON g.order_id=i.virtuemart_order_id AND g.estore="virtuemart" AND g.email_sent=1
					 WHERE i.virtuemart_order_id='.$this->order->order_id.' AND g.order_id IS NULL AND ap.published=1 AND ap.estore="virtuemart"
					 GROUP BY i.virtuemart_order_item_id';
			$this->db->setQuery( $sql ); 
			$rows = $this->db->loadObjectList();
			$this->generate_auto_email($rows);

		}
	}
    
    //T.Trung end
	
	
	protected function formatcurrency($val) {
			
		if (!class_exists( 'CurrencyDisplay' )) require JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/currencydisplay.php';
		$currency_class = CurrencyDisplay::getInstance('',$this->order->vendor_id);
		return $currency_class->priceDisplay($val);

	}
	
	protected function get_storeorder($in) {
		$order = new stdClass();
		$order->order_id = $in->virtuemart_order_id;
		$order->order_number = $in->order_number;
		$order->order_status = $in->order_status;
		$order->order_total = $in->order_total;
		$order->created_on = strtotime($in->created_on);
		
		$order->vendor_id = $in->virtuemart_vendor_id;
		$order->order_pass = $in->order_pass;

		return $order;
	}
	protected function get_storename() {
		$this->db->setQuery( 'SELECT vendor_name FROM #__virtuemart_vendors WHERE virtuemart_vendor_id='.$this->order->vendor_id );
		return $this->db->loadResult();
	}
	protected function get_storeemail() {
		$config = JFactory::getConfig ();
		return $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'mailfrom' );
	}
	protected function get_orderstatuslist(){
		$sql = 'SELECT order_status_code AS value, order_status_name AS text FROM #__virtuemart_orderstates';
		$this->db->setQuery($sql);
		
		$orderstatuses = array();
		foreach ($this->db->loadObjectList() as $_ordstat) $orderstatuses[$_ordstat->value] = $_ordstat->text;
		
		return $orderstatuses;
	}
	protected function get_orderlink() {
		return 'index.php?option=com_virtuemart&controller=orders&task=details&order_number='.$this->order->order_number.'&order_pass='.$this->order->order_pass;
	}
	

	protected function getproductattributes($row) {
		$recipient_email = $row->email;
		$recipient_name = $row->first_name.' '.$row->last_name;
		//$message = '';
		//T.Trung
        $message = $row->message;
        //T.Trung end
		$attrlist = array();
		if(!empty($row->product_attribute)) {
			$name_id = (int)$this->params->get('virtuemart_giftcert_field_recipient_name', 0);
			$email_id = (int)$this->params->get('virtuemart_giftcert_field_recipient_email', 0);
			$message_id = (int)$this->params->get('virtuemart_giftcert_field_recipient_message', 0);
			
			$sql = 'SELECT virtuemart_custom_id,virtuemart_customfield_id as id
					  FROM #__virtuemart_product_customfields 
					 WHERE virtuemart_product_id='.$row->product_id.' AND virtuemart_custom_id IN ('.$name_id.','.$email_id.','.$message_id.')';
			$this->db->setQuery($sql);
			$custom_arr = $this->db->loadObjectList('virtuemart_custom_id');

			if(empty($custom_arr) && !empty($row->product_parent_id)) {
				$sql = 'SELECT virtuemart_custom_id,virtuemart_customfield_id as id
						  FROM #__virtuemart_product_customfields 
						 WHERE virtuemart_product_id='.$row->product_parent_id.' AND virtuemart_custom_id IN ('.$name_id.','.$email_id.','.$message_id.')';
				$this->db->setQuery($sql);
				$custom_arr = $this->db->loadObjectList('virtuemart_custom_id');
			}

			$attr = json_decode($row->product_attribute);
			@$tmp_recipient_email = !empty($attr->{$custom_arr[$email_id]->id}) ? $attr->{$custom_arr[$email_id]->id}->textinput->comment : '';
			if(!empty($tmp_recipient_email) && JMailHelper::isEmailAddress($tmp_recipient_email)) $recipient_email = $tmp_recipient_email;
			
			if(!empty($attr->{$custom_arr[$name_id]->id}) && !empty($attr->{$custom_arr[$name_id]->id}->textinput->comment)) $recipient_name = $attr->{$custom_arr[$name_id]->id}->textinput->comment;
			if(!empty($attr->{$custom_arr[$message_id]->id}) && !empty($attr->{$custom_arr[$message_id]->id}->textinput->comment)) $message =  $attr->{$custom_arr[$message_id]->id}->textinput->comment;
		}
		return array('recipient_name'=>$recipient_name,'email'=>$recipient_email,'message'=>$message);
	}


}

