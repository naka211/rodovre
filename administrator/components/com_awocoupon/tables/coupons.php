<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableCoupons extends JTable {
	var $id						= null;
	var $estore					= null;
	var $coupon_code			= null;
	var $passcode				= null;
	var $parent_type			= null;
	var $coupon_value_type		= null;
	var $coupon_value			= null;
	var $coupon_value_def		= null;
	var $function_type			= null;
	var $num_of_uses_type		= null;
	var $num_of_uses			= null;
	var $min_value				= null;
	var $discount_type			= null;
	var $user_type				= null;
	var $startdate				= null;
	var $expiration				= null;
	var $exclude_special		= null;
	var $exclude_giftcert		= null;
	var $order_id				= null;
	var $template_id			= null;
	var $published				= null;
	var $note					= null;
	var $params					= null;

	
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon', 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {
		global $AWOCOUPON_lang;
		$err = array();
		
		if(empty($this->coupon_code)) $err[] = JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(empty($AWOCOUPON_lang['estore'][$this->estore])) $err[] = JText::_('COM_AWOCOUPON_GBL_ERROR');

		
		if($this->function_type == 'giftcert') {
			if(empty($this->coupon_value) || !is_numeric($this->coupon_value)) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		} 		
		elseif($this->function_type == 'parent') {
			if(empty($this->parent_type) || ($this->parent_type!='first' && $this->parent_type!='all' && $this->parent_type!='allonly' && $this->parent_type!='lowest' && $this->parent_type!='highest'))
				$err[] = JText::_('COM_AWOCOUPON_CP_PARENT_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
		} 
		else {
			if(empty($this->coupon_value_type) || ($this->coupon_value_type!='percent' && $this->coupon_value_type!='amount')) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			if($this->user_type!='user' && $this->user_type!='usergroup') $err[] = JText::_('COM_AWOCOUPON_CP_USER_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');

			if($this->function_type == 'coupon') {
				if(!is_null($this->coupon_value) && (!is_numeric($this->coupon_value) || $this->coupon_value<0)) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(!empty($this->coupon_value_def) && !preg_match("/^(\d+\-\d+([.]\d+)?;)+(\[[_a-z]+\=[a-z]+(\&[_a-z]+\=[a-z]+)*\])?$/",$this->coupon_value_def)) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE_DEFINITION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(is_null($this->coupon_value) && empty($this->coupon_value_def) ) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(empty($this->discount_type) || ($this->discount_type!='specific' && $this->discount_type!='overall')) $err[] = JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			} 
			elseif($this->function_type == 'shipping') {
				if(!is_numeric($this->coupon_value) || $this->coupon_value<0) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(!empty($this->discount_type) && ($this->discount_type!='specific' && $this->discount_type!='overall')) $err[] = JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			} 
			elseif($this->function_type == 'buy_x_get_y') {
				if(!is_numeric($this->coupon_value) || $this->coupon_value<0) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			} 
			else $err[] = JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				
			if(!empty($this->num_of_uses_type) && $this->num_of_uses_type!='total' && $this->num_of_uses_type!='per_user') $err[] = JText::_('COM_AWOCOUPON_CP_NUMBER_USES_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			if(!empty($this->num_of_uses) && !is_numeric($this->num_of_uses)) $err[] = JText::_('COM_AWOCOUPON_CP_NUMBER_USES').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			if( (!empty($this->num_of_uses_type) && empty($this->num_of_uses)) || (empty($this->num_of_uses_type) && !empty($this->num_of_uses))) $err[] = JText::_('COM_AWOCOUPON_CP_NUMBER_USES').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			if(!empty($this->min_value) && !is_numeric($this->min_value)) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE_MIN').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		}
		
		$is_start = true;
		if(!empty($this->startdate)) {
			if(!preg_match("/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/",$this->startdate)) {
				$is_start = false;
				$err[] = JText::_('COM_AWOCOUPON_CP_DATE_START').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			}
			else {
				list($dtmp,$ttmp) = explode(' ',$this->startdate);
				list($Y,$M,$D) = explode('-',$dtmp);
				list($h,$m,$s) = explode(':',$ttmp);
				if($Y>2100 || $M>12 || $D>31 || $h>23 || $m>59 || $s>59) {
					$is_start = false;
					$err[] = JText::_('COM_AWOCOUPON_CP_DATE_START').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				}
			}
		} else $is_start = false;
		$is_end = true;
		if(!empty($this->expiration)) {
			if(!preg_match("/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/",$this->expiration)) {
				$is_end = true;
				$err[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			}
			else {
				list($dtmp,$ttmp) = explode(' ',$this->expiration);
				list($Y,$M,$D) = explode('-',$dtmp);
				list($h,$m,$s) = explode(':',$ttmp);
				if($Y>2100 || $M>12 || $D>31 || $h>23 || $m>59 || $s>59) {
					$is_end = true;
					$err[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				}
			}
		} else $is_end = false;
		if($is_start && $is_end) {
			list($dtmp,$ttmp) = explode(' ',$this->startdate);
			list($Y,$M,$D) = explode('-',$dtmp);
			list($h,$m,$s) = explode(':',$ttmp);
			$c1 = (int)$Y.$M.$D.'.'.$h.$m.$s;
			list($dtmp,$ttmp) = explode(' ',$this->expiration);
			list($Y,$M,$D) = explode('-',$dtmp);
			list($h,$m,$s) = explode(':',$ttmp);
			$c2 = (int)$Y.$M.$D.'.'.$h.$m.$s;
			if($c1>$c2) $err[] = JText::_('COM_AWOCOUPON_CP_DATE_START').'/'.JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		}
		if(!empty($this->exclude_special) && $this->exclude_special!='1') $err[] = JText::_('COM_AWOCOUPON_GBL_INVALID');
		if(!empty($this->exclude_giftcert) && $this->exclude_giftcert!='1') $err[] = JText::_('COM_AWOCOUPON_GBL_INVALID');
		if(!empty($this->order_id) && !ctype_digit($this->order_id)) $err[] = JText::_('COM_AWOCOUPON_GBL_INVALID');
		if(!empty($this->template_id) && !ctype_digit($this->template_id)) $err[] = JText::_('COM_AWOCOUPON_GBL_INVALID');
		if(empty($this->published) || ($this->published!='1' && $this->published!='-1' && $this->published!='-2')) $err[] = JText::_('COM_AWOCOUPON_CP_PUBLISHED').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		

		if(!empty($err)) {
			foreach($err as $error) $this->setError($error);
			return false;
		}

		return true;
	}
}
