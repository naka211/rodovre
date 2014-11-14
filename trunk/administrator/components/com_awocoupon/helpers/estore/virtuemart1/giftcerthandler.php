<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/estoregiftcerthandler.php';

class AwoCouponVirtuemart1GiftcertHandler  extends AwoCouponEstoreGiftcertHandler{

	var $estore = 'virtuemart1';
	var $order = null;
	
	function __construct () {
		parent::__construct();

	}

	static function process( $d, $old_order_status ) {
		$instance = new AwoCouponVirtuemart1GiftcertHandler();
		$instance->order = $instance->get_storeorder($d);
		
	  	return $instance->process_order_status_change();
	}
	
	static function process_resend ( $d, $rows) {
		$instance = new AwoCouponVirtuemart1GiftcertHandler();
		$instance->order = $instance->get_storeorder($d);
		$instance->is_entry_new = false;
		
		return $instance->generate_auto_email($rows);
	}
	
	protected function process_order_status_change( ) {
		$auto_order_status = $this->params->get('giftcert_order_status', 'C') ;
		
		if( $this->order->order_status==$auto_order_status ) {
		// order confirmed now mail out gift certs if any
			
			$sql = 'SELECT i.order_item_id,i.order_id,i.product_item_price,i.product_quantity,u.user_id,u.user_email AS email,u.first_name,u.last_name,
							ap.expiration_number,ap.expiration_type,ap.coupon_template_id,i.order_item_currency,ap.profile_id,
							i.product_id,i.product_attribute,i.order_item_name,ap.vendor_name,ap.vendor_email,p.product_parent_id
					  FROM #__vm_order_item i 
					  JOIN #__awocoupon_giftcert_product ap ON ap.product_id=i.product_id
					  JOIN #__vm_product p ON p.product_id=i.product_id
					  JOIN #__vm_order_user_info u ON u.order_id=i.order_id AND u.address_type="BT"
					  LEFT JOIN #__awocoupon_giftcert_order g ON g.order_id=i.order_id AND g.estore="virtuemart1" AND g.email_sent=1
					 WHERE i.order_id='.$this->order->order_id.' AND g.order_id IS NULL AND ap.published=1 AND ap.estore="virtuemart1"
					 GROUP BY i.order_item_id';
			$this->db->setQuery( $sql ); 
			$rows = $this->db->loadObjectList();
			$this->generate_auto_email($rows);

		}
	}
	
	
	
	protected function formatcurrency($val) {
		return str_replace('&euro;','€',html_entity_decode($GLOBALS['CURRENCY_DISPLAY']->getFullValue($val)));
	}
	
	protected function get_storeorder($d) {
		$order_id = (int)$d['order_id'];
		$db = JFactory::getDBO();
		$db->setQuery('SELECT order_id,order_number,vendor_id,order_status,order_total,cdate AS created_on FROM #__vm_orders WHERE order_id='.(int)$order_id);
		$order = $db->loadObject();
		if(empty($order)) $order->order_id = 0;
		return $order;
	}
	protected function get_storename() {
		$this->db->setQuery( 'SELECT vendor_name FROM #__vm_vendor WHERE vendor_id='.$this->order->vendor_id );
		return $this->db->loadResult();
	}
	protected function get_storeemail() {
		$config = JFactory::getConfig ();
		return $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'mailfrom' );
	}
	protected function get_orderstatuslist(){
		$sql = 'SELECT order_status_code AS value, order_status_name AS text FROM #__vm_order_status';
		$this->db->setQuery($sql);
		
		$orderstatuses = array();
		foreach ($this->db->loadObjectList() as $_ordstat) $orderstatuses[$_ordstat->value] = $_ordstat->text;
		
		return $orderstatuses;
	}
	protected function get_orderlink() {
		return 'index.php?option=com_virtuemart&page=account.order_details&order_id='.$this->order->order_id;
	}
	

	protected function getproductattributes($row) {
		$attrlist = array();
		if(!empty($row->product_attribute)) {
			foreach(explode('<br/>',$row->product_attribute) as $v) {
				list($a,$b) = explode(':',$v);
				$attrlist[strtolower(trim($a))] = trim($b);
			}
			$name_field = (int)$this->params->get('virtuemart1_giftcert_field_recipient_name', 'recipient name');
			$email_field = (int)$this->params->get('virtuemart1_giftcert_field_recipient_email', 'recipient email');
			$message_field = (int)$this->params->get('virtuemart1_giftcert_field_recipient_message', 'message');
			if(!empty($attrlist[$email_field]) && JMailHelper::isEmailAddress($attrlist[$email_field])) {
				return array('recipient_name'=>@$attrlist[$name_field],'email'=>$attrlist[$email_field],'message'=>@$attrlist[$message_field]);
			}
		}
		return array('recipient_name'=>$row->first_name.' '.$row->last_name,'email'=>$row->email,'message'=>'');
	}


}

