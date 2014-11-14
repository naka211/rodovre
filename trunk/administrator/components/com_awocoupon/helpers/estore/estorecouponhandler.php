<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


if (!function_exists('printr')) { function printr($a) { echo '<pre>'.print_r($a,1).'</pre>'; } }
if (!function_exists('printrx')) { function printrx($a) { echo '<pre>'.print_r($a,1).'</pre>'; exit; } }

class AwoCouponEstoreCouponHandler {

	var $reprocess = false;


	function AwoCouponEstoreCouponHandler() {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
		$this->params = new awoParams();
		
	  	$this->session = JFactory::getSession();
		$this->giftcert_discount_before_tax = $this->params->get('enable_giftcert_discount_before_tax', 0) == 1  ? 1 : 0;
		$this->coupon_discount_before_tax = $this->params->get('enable_coupon_discount_before_tax', 0) == 1  ? 1 : 0;
		
	}

	// function to process a coupon_code entered by a user
	protected function process_autocoupon_helper() {
		$db = JFactory::getDBO();	
		
		// if cart is the same, do not reproccess coupon
		$autosess = $this->get_coupon_auto();
		if(!empty($autosess) ){
			if( !empty($autosess->uniquecartstring) && $autosess->uniquecartstring==$this->getuniquecartstringauto()) {
				if(empty($awosess)) {
					$this->finalize_autocoupon($autosess);
				}
				return $autosess[0]->coupon_code;
			}
		}
		
		$this->initialize_coupon_auto();
	
		// check coupons		
		$auto_coupon_code = array();
		$multiple_coupon_max_auto = (int)$this->params->get('multiple_coupon_max_auto', 100);
		//$current_date = date('Y-m-d H:i:s');
		$current_date = version_compare( JVERSION, '1.6.0', 'ge' ) 
							? JFactory::getDate('now',JFactory::getConfig()->get('offset'))->format('Y-m-d H:i:s',true)
							: JFactory::getDate(time(),JFactory::getConfig()->getValue ( 'offset' )*-1)->toFormat('%Y-%m-%d %H:%M:%S');
		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,c.min_value,c.discount_type,c.user_type,
					c.function_type,c.num_of_uses_type,c.coupon_value_def,c.exclude_special,c.exclude_giftcert,c.parent_type,c.params,1 as isauto,note
				  FROM #__awocoupon c
				  JOIN #__awocoupon_auto a ON a.coupon_id=c.id
				 WHERE c.estore="'.$this->estore.'" AND c.published=1 AND a.published=1
				   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
						 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
						 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
						 (c.startdate<="'.$current_date.'"		AND c.expiration>="'.$current_date.'")
					   )
				 ORDER BY a.ordering';
		$db->setQuery( $sql );
		$coupon_rows = $db->loadObjectList();
		if(empty($coupon_rows)) return false;

	
		// retreive cart items
		$this->define_cart_items();
		if(empty($this->cart->items)) return false;
		foreach($this->cart->items as $k=>$r) {
			$this->cart->items[$k]['totaldiscount'] = 0;
			$this->cart->items[$k]['totaldiscount_notax'] = 0;
		}
		
		foreach($coupon_rows as $coupon_row) {
		
			if(empty($coupon_row)) {
			// no record, so coupon_code entered was not valid
				continue;
			} 

			// coupon returned
			$this->coupon_row = $coupon_row;


			if($coupon_row->function_type != 'parent') {
				
				$return = $this->process_coupon_return ( $coupon_row, false );
				if(!empty($return) && $return['redeemed']) {
					$auto_coupon_code[] = $coupon_row;
					if(count($auto_coupon_code)>=$multiple_coupon_max_auto) break;
				};
				continue;

			} 
			else {


				$test = $this->validate_num_uses($coupon_row);
				if(!empty($test)) continue;
				
				$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,c.min_value,c.discount_type,c.user_type,
							c.function_type,c.num_of_uses_type,c.coupon_value_def,c.exclude_special,c.exclude_giftcert,params
						  FROM #__awocoupon_asset1 ch 
						  JOIN #__awocoupon c ON c.id=ch.asset_id
						 WHERE ch.asset_type="coupon" AND ch.coupon_id='.$coupon_row->id.' 
						   AND c.estore="'.$this->estore.'" AND c.published=1
						   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
								 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
								 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
								 (c.startdate<="'.$current_date.'"		AND c.expiration>="'.$current_date.'")
							   )
						 ORDER BY ch.order_by';
				$db->setQuery( $sql );
				$coupon_children_rows = $db->loadObjectList();
				if(empty($coupon_children_rows)) {
				// no record, so coupon_code entered was not valid
					continue;
				}

				// coupon returned
				
				if($coupon_row->parent_type == 'first' || $coupon_row->parent_type == 'lowest' || $coupon_row->parent_type=='highest' || $coupon_row->parent_type=='all') {
					foreach($coupon_children_rows as $child_row) {
						$return = $this->process_coupon_return ( $child_row, false );
						if(!empty($return) && $return['redeemed']) {
						// mark this order as having used a coupon so people cant go and use coupons over and over 
							$auto_coupon_code[] = $coupon_row;
							if(count($auto_coupon_code)>=$multiple_coupon_max_auto) break 2;
						};
					}
					continue;
				} 
				elseif($coupon_row->parent_type == 'allonly') {
					$found_valid_coupons = array();
					foreach($coupon_children_rows as $child_row) {
						$return = $this->process_coupon_return ( $child_row, false );
						if(!empty($return) && $return['redeemed']) {
						// mark this order as having used a coupon so people cant go and use coupons over and over 
							$found_valid_coupons[] = $return;
						}
					}
						
					if($coupon_row->parent_type == 'allonly' && count($found_valid_coupons)==count($coupon_children_rows)) {
						$auto_coupon_code[] = $coupon_row;
						if(count($auto_coupon_code)>=$multiple_coupon_max_auto) break;
					}
					continue;
				}
			}
		}
		
//printrx($auto_coupon_code);
		$this->set_coupon_auto($auto_coupon_code);
		if(!empty($auto_coupon_code)) {
			$this->finalize_autocoupon($auto_coupon_code);
			return $auto_coupon_code[0]->coupon_code;
		}
		
		return;
		
	}

	protected function process_coupon_helper() {
		$db = JFactory::getDBO();	
		$submitted_coupon_code = trim($this->get_submittedcoupon());
		
		// if cart is the same, do not reproccess coupon
		$awosess = $this->session->get('coupon', '', 'awocoupon');
		if(!empty($awosess) ){
			$awosess = unserialize($awosess);
			if( ((!empty($submitted_coupon_code) && strpos(';'.$awosess['coupon_code_internal'].';',';'.$submitted_coupon_code.';')!==false)
				|| empty($submitted_coupon_code))
			&& $awosess['uniquecartstring']==$this->getuniquecartstring($awosess['coupon_code_internal'])) {
				$this->finalize_coupon_store($awosess);
				return true;
			}
		}
		
		//------START STORE COUPON SYSTEM ----------------------------------------------------------------------------------------------
		if(empty($awosess)) {
			if($this->params->get('enable_store_coupon', 0) == 1) {
				$db->setQuery( 'SELECT id FROM #__awocoupon WHERE estore="'.$this->estore.'" AND coupon_code='.$db->Quote( awolibrary::dbEscape($submitted_coupon_code)) );
				$tmp = $db->loadResult();
				if(empty($tmp)) {
					$this->continue_execution = true;
					return null;
				}
			}
		}
		//------END STORE COUPON SYSTEM ----------------------------------------------------------------------------------------------
		
		$iscaseSensitive = awoLibrary::getCaseSensitive();

		
		$coupon_awo_entered_coupon_ids = $multiple_coupons['auto'] = $multiple_coupons['coupon'] = $multiple_coupons['giftcert'] = array();
		$coupon_session = $this->session->get('coupon', '', 'awocoupon');
		if(!empty($coupon_session) ) {
			$coupon_session = unserialize($coupon_session);
			if(!empty($coupon_session['processed_coupons'])) {
				foreach($coupon_session['processed_coupons'] as $k=>$r) {
					//if($r['coupon_entered_id']!=$k) continue; // parent product
					$coupon_awo_entered_coupon_ids[] = $r['coupon_code'];
					if($r['isauto']) $multiple_coupons['auto'][] = $r['coupon_code'];
					else {
						$multiple_coupons[$r['isgift'] ? 'giftcert' : 'coupon'][] = $r['coupon_code'];
					}
				}
			}
		}
		if(!empty($submitted_coupon_code)) $coupon_awo_entered_coupon_ids[] = awolibrary::dbEscape($submitted_coupon_code);
		$coupon_awo_entered_coupon_ids = $iscaseSensitive ? array_unique($coupon_awo_entered_coupon_ids) : $this->array_iunique($coupon_awo_entered_coupon_ids);

		$this->initialize_coupon();
		//JFactory::getApplication()->set('_messageQueue','');
		
		$auto_codes = $this->get_coupon_auto();
		if(empty($coupon_awo_entered_coupon_ids) && empty($auto_codes)) return $this->return_false('errNoRecord');
	
	
		/*if(!empty($auto_codes))  {
			foreach($coupon_awo_entered_coupon_ids as $k=>$r) {
				foreach($auto_codes as $auto_code) {
					if(
						($iscaseSensitive && trim($auto_code->coupon_code)==$r)
					||	(!$iscaseSensitive && strtolower(trim($auto_code->coupon_code))==strtolower($r))
				
					) { 
						unset($coupon_awo_entered_coupon_ids[$k]);
						break;
					}
				}
			}
		}*/
			
		if($this->params->get('enable_multiple_coupon', 0)==0) $coupon_awo_entered_coupon_ids = array(array_pop($coupon_awo_entered_coupon_ids));
		else {
		// remove coupons is maximums are set
		
		
			if(!empty($coupon_awo_entered_coupon_ids)) {
				
				$multiple_coupon_max_auto = (int)$this->params->get('multiple_coupon_max_auto', 0);
				$multiple_coupon_max_coupon = (int)$this->params->get('multiple_coupon_max_coupon', 0);
				$multiple_coupon_max_giftcert = (int)$this->params->get('multiple_coupon_max_giftcert', 0);
				if($multiple_coupon_max_auto>0 || $multiple_coupon_max_coupon>0 || $multiple_coupon_max_giftcert>0) {
					if(!empty($submitted_coupon_code)) {
						if(
							($iscaseSensitive && (in_array($submitted_coupon_code,$multiple_coupons['coupon']) || in_array($submitted_coupon_code,$multiple_coupons['giftcert'])))
						||	(!$iscaseSensitive && ($this->in_arrayi($submitted_coupon_code,$multiple_coupons['coupon']) || $this->in_arrayi($submitted_coupon_code,$multiple_coupons['giftcert'])))
						) ;
						else {
							$db->setQuery('SELECT function_type FROM #__awocoupon WHERE coupon_code="'.awolibrary::dbEscape($submitted_coupon_code).'"');
							$test = $db->loadResult();
							if(!empty($test)) $multiple_coupons[$test=='giftcert' ? 'giftcert' : 'coupon'][] = $submitted_coupon_code;
						}
					}
					
					if($multiple_coupon_max_auto>0 && count($multiple_coupons['auto'])>1 ) {
						$multiple_coupons['auto'] = $iscaseSensitive ? array_unique($multiple_coupons['auto']) : $this->array_iunique($multiple_coupons['auto']);
						if(count($multiple_coupons['auto'])>$multiple_coupon_max_auto) {
							$removecoupons = array_slice($multiple_coupons['auto'],0,count($multiple_coupons['auto'])-$multiple_coupon_max_auto);
							if(!empty($removecoupons)) {
								foreach($removecoupons as $r) if(($key = array_search($r, $coupon_awo_entered_coupon_ids)) !== false) unset($coupon_awo_entered_coupon_ids[$key]);
							}
						}
					}
					if($multiple_coupon_max_coupon>0 && count($multiple_coupons['coupon'])>1 ) {
						$multiple_coupons['coupon'] = $iscaseSensitive ? array_unique($multiple_coupons['coupon']) : $this->array_iunique($multiple_coupons['coupon']);
						if(count($multiple_coupons['coupon'])>$multiple_coupon_max_coupon) {
							$removecoupons = array_slice($multiple_coupons['coupon'],0,count($multiple_coupons['coupon'])-$multiple_coupon_max_coupon);
							if(!empty($removecoupons)) {
								foreach($removecoupons as $r) if(($key = array_search($r, $coupon_awo_entered_coupon_ids)) !== false) unset($coupon_awo_entered_coupon_ids[$key]);
							}
						}
					}
					if($multiple_coupon_max_giftcert>0 && count($multiple_coupons['giftcert'])>1 ) {
						$multiple_coupons['giftcert'] = $iscaseSensitive ? array_unique($multiple_coupons['giftcert']) : $this->array_iunique($multiple_coupons['giftcert']);
						if(count($multiple_coupons['giftcert'])>$multiple_coupon_max_giftcert) {
							$removecoupons = array_slice($multiple_coupons['giftcert'],0,count($multiple_coupons['giftcert'])-$multiple_coupon_max_giftcert);
							if(!empty($removecoupons)) {
								foreach($removecoupons as $r) if(($key = array_search($r, $coupon_awo_entered_coupon_ids)) !== false) unset($coupon_awo_entered_coupon_ids[$key]);
							}
						}
					}
				}

				$multiple_coupon_max = (int)$this->params->get('multiple_coupon_max', 0);
				if($multiple_coupon_max>0 && count($coupon_awo_entered_coupon_ids)>1 ) $coupon_awo_entered_coupon_ids = array_slice($coupon_awo_entered_coupon_ids,0,$multiple_coupon_max);
			}

			
		}
		
		/*if(!empty($auto_code))  {
			foreach($auto_codes as $auto_code) {
				array_unshift($coupon_awo_entered_coupon_ids,$auto_code->coupon_code); 
			}
		}*/
		
		// check coupons
		$master_output = $coupon_rows = array();
		//$current_date = date('Y-m-d H:i:s');
		$current_date = version_compare( JVERSION, '1.6.0', 'ge' ) 
							? JFactory::getDate('now',JFactory::getConfig()->get('offset'))->format('Y-m-d H:i:s',true)
							: JFactory::getDate(time(),JFactory::getConfig()->getValue ( 'offset' )*-1)->toFormat('%Y-%m-%d %H:%M:%S');
		$coupon_codes = implode('","',$coupon_awo_entered_coupon_ids);
		if(!empty($coupon_codes)) {
			$sql = 'SELECT id,coupon_code,num_of_uses,coupon_value_type,coupon_value,min_value,discount_type,user_type,
						function_type,num_of_uses_type,coupon_value_def,exclude_special,exclude_giftcert,parent_type,params,note
					  FROM #__awocoupon 
					 WHERE estore="'.$this->estore.'" AND published=1
					   AND ( ((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
							 ((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
							 ((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
							 (startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
						   )
					   AND coupon_code IN ("'.$coupon_codes.'")
					  ORDER BY FIELD(coupon_code, "'.$coupon_codes.'")';
			$db->setQuery( $sql );
			$coupon_rows = $db->loadObjectList('id');
		}
		
		if(!empty($auto_codes))  { 
			$valid_auto_codes = array();
			foreach($auto_codes as $auto_code) {
				if(isset($coupon_rows[$auto_code->id])) {
					$valid_auto_codes[] = $auto_code;
					unset($coupon_rows[$auto_code->id]);
				}
			}
			$valid_auto_codes = array_reverse($valid_auto_codes);
			foreach($valid_auto_codes as $auto_code) array_unshift($coupon_rows,$auto_code); 
			//printrx($coupon_rows);
		}
		
		if(!empty($submitted_coupon_code)) {
			$is_found = false;
			foreach($coupon_rows as $tmp) {
				if(
					($iscaseSensitive && trim($tmp->coupon_code)==$submitted_coupon_code)
				||	(!$iscaseSensitive && strtolower(trim($tmp->coupon_code))==strtolower($submitted_coupon_code))
				
				) {
					$is_found = true; 
					break;
				}
			}
			if(!$is_found) {
				$this->coupon_row = new stdclass;
				$this->coupon_row->id = -1;
				$this->coupon_row->coupon_code = $submitted_coupon_code;
				$this->coupon_row->function_type = 'coupon';
				$this->return_false('errNoRecord');
			}
		}
		if(empty($coupon_rows)) return false; //$this->return_false('errNoRecord');

	
		// retreive cart items
		$this->define_cart_items();
		if(empty($this->cart->items)) {
			$this->initialize_coupon();
			return false;
		}
		foreach($this->cart->items as $k=>$r) {
			$this->cart->items[$k]['totaldiscount'] = 0;
			$this->cart->items[$k]['totaldiscount_notax'] = 0;
		}
		
		foreach($coupon_rows as $coupon_row) {
		
			if(empty($coupon_row)) {
			// no record, so coupon_code entered was not valid
				continue;
			} 

			// coupon returned
			$this->coupon_row = $coupon_row;
		

	

			if($coupon_row->function_type != 'parent') {
				
				$return = $this->process_coupon_return ( $coupon_row, true );
				if(!empty($return) && $return['redeemed']) {
					$master_output[$coupon_row->id]=array($coupon_row,$return);
					continue;
				};
				continue;

			} 
			else {

				$test = $this->validate_num_uses($coupon_row);
				if(!empty($test)) {
					$this->return_false($test);
					continue;
				}


				$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,c.min_value,c.discount_type,c.user_type,
							c.function_type,c.num_of_uses_type,c.coupon_value_def,c.exclude_special,c.exclude_giftcert,params
						  FROM #__awocoupon_asset1 ch 
						  JOIN #__awocoupon c ON c.id=ch.asset_id
						 WHERE ch.asset_type="coupon" AND ch.coupon_id='.$coupon_row->id.' 
						   AND c.estore="'.$this->estore.'" AND c.published=1
						   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
								 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
								 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
								 (c.startdate<="'.$current_date.'"		AND c.expiration>="'.$current_date.'")
							   )
						 ORDER BY ch.order_by';
				$db->setQuery( $sql );
				$coupon_children_rows = $db->loadObjectList();
				if(empty($coupon_children_rows)) {
				// no record, so coupon_code entered was not valid
					continue;
				}

				// coupon returned
				
				if($coupon_row->parent_type == 'first') {
					foreach($coupon_children_rows as $child_row) {
						$return = $this->process_coupon_return ( $child_row, true );
						if(!empty($return) && $return['redeemed']) {
						// mark this order as having used a coupon so people cant go and use coupons over and over 
							$return['coupon_entered_id'] = $coupon_row->id;
							$return['coupon_code'] = $coupon_row->coupon_code;
							$master_output[$coupon_row->id]=array($coupon_row,$return);
							break;
						};
					}
					continue;
				} 
				elseif($coupon_row->parent_type == 'lowest' || $coupon_row->parent_type=='highest') {
					$found_valid_coupons = array();
					foreach($coupon_children_rows as $child_row) {
						$return = $this->process_coupon_return ( $child_row, false );
						if(!empty($return) && $return['redeemed']) {
						// mark this order as having used a coupon so people cant go and use coupons over and over 
							$found_valid_coupons[] = $return;
						};
					}
					if(!empty($found_valid_coupons)) {
						$valid_id = -1; $valid_value = 0;
						foreach($found_valid_coupons as $k=>$valid_coupon) {
							if($valid_id == -1) {
								$valid_id = $k;
								$valid_value = $valid_coupon['product_discount']+$valid_coupon['shipping_discount'];
							}
							if($coupon_row->parent_type == 'lowest'){
								if($valid_value > ($valid_coupon['product_discount']+$valid_coupon['shipping_discount'])) {
									$valid_id = $k;
									$valid_value = $valid_coupon['product_discount']+$valid_coupon['shipping_discount'];
								}
							}
							elseif($coupon_row->parent_type == 'highest') {
								if($valid_value < ($valid_coupon['product_discount']+$valid_coupon['shipping_discount'])) {
									$valid_id = $k;
									$valid_value = $valid_coupon['product_discount']+$valid_coupon['shipping_discount'];
								}
							}
						}
						if(!empty($found_valid_coupons[$valid_id])) {
						// mark this order as having used a coupon so people cant go and use coupons over and over 
							foreach($coupon_children_rows as $child_row) {
								if($child_row->coupon_code == $found_valid_coupons[$valid_id]['coupon_code']) {
									$return = $this->process_coupon_return ( $child_row, true);
									if(!empty($return) && $return['redeemed']) {
									// mark this order as having used a coupon so people cant go and use coupons over and over 
										$return['coupon_entered_id'] = $coupon_row->id;
										$return['coupon_code'] = $coupon_row->coupon_code;
										$master_output[$coupon_row->id]=array($coupon_row,$return);
									};
									break;
								}
							}

							//$found_valid_coupons[$valid_id]['coupon_entered_id'] = $coupon_row->id;
							//$found_valid_coupons[$valid_id]['coupon_code'] = $coupon_row->coupon_code;
							//$master_output[$coupon_row->id]=array($coupon_row,$found_valid_coupons[$valid_id]);
							continue;
						}
					}
					continue;
				}
				elseif($coupon_row->parent_type == 'all' || $coupon_row->parent_type == 'allonly') {
					$found_valid_coupons = array();
					foreach($coupon_children_rows as $child_row) {
						$return = $this->process_coupon_return ( $child_row, true );
						if(!empty($return) && $return['redeemed']) {
						// mark this order as having used a coupon so people cant go and use coupons over and over 
							$found_valid_coupons[] = $return;
						}
					}
						
					if($coupon_row->parent_type == 'allonly' && count($found_valid_coupons)!=count($coupon_children_rows)) {
						// all do not match, coupon not found
						$this->return_false('errNoRecord');
						continue;
					}

					$return = array(	'coupon_id'=>$coupon_row->id,
										'coupon_code'=>$coupon_row->coupon_code,
										'product_discount'=>0,
										'product_discount_notax'=>0,
										'product_discount_tax'=>0,
										'shipping_discount'=>0,
										'shipping_discount_notax'=>0,
										'shipping_discount_tax'=>0,
										'usedproducts'=>''
									); 
					$usedproducts = $processed_coupons = array();
					foreach($found_valid_coupons as $row) {
						if(!empty($row['force_add']) || !empty($row['product_discount']) || !empty($row['shipping_discount'])) {
							if(!empty($row['force_add'])) $return['force_add'] = 1;
							$return['product_discount'] += $row['product_discount'];
							$return['product_discount_notax'] += $row['product_discount_notax'];
							$return['product_discount_tax'] += $row['product_discount_tax'];
							$return['shipping_discount'] += $row['shipping_discount'];
							$return['shipping_discount_notax'] += $row['shipping_discount_notax'];
							$return['shipping_discount_tax'] += $row['shipping_discount_tax'];
							//$tmp = !empty($row['usedproducts']) ? array_fill_keys(explode(',',$row['usedproducts']),1) : array();
							$tmp = array();
							$tmpA = !empty($row['usedproducts']) ? explode(',',$row['usedproducts']) : array();
							foreach($tmpA as $t) $tmp[$t] = 1;
							$usedproducts = $usedproducts+$tmp;
							$isauto = false;
							if(!empty($auto_codes))  { 
								foreach($auto_codes as $auto_code) {
									if($auto_code->id == $coupon_row->id) {
										$isauto = true;
										break;
									}
								}
							}
							
							$processed_coupons[$row['coupon_id']] = array(
								'coupon_entered_id'=>$coupon_row->id,
								'coupon_code'=>$coupon_row->coupon_code,
								'orig_coupon_id'=>$row['coupon_id'],
								'orig_coupon_code'=>$row['coupon_code'],
								'product_discount'=>$row['product_discount'],
								'product_discount_notax'=>$row['product_discount_notax'],
								'product_discount_tax'=>$row['product_discount_tax'],
								'shipping_discount'=>$row['shipping_discount'],
								'shipping_discount_notax'=>$row['shipping_discount_notax'],
								'shipping_discount_tax'=>$row['shipping_discount_tax'],
								'usedproducts'=>$row['usedproducts'],
								'isauto'=> $isauto,
								'isgift'=>false,
							);
						}
					}
					
					if(!empty($return['force_add']) || !empty($return['product_discount']) || !empty($return['shipping_discount'])) {
					// mark this order as having used a coupon so people cant go and use coupons over and over 
						$return['usedproducts'] = implode(',',array_keys($usedproducts));
						$master_output[$coupon_row->id]=array($coupon_row,$return,$processed_coupons);
						continue;
					}
					continue;
											
				}
			}
		}
		
		
//printrx($master_output);	
		if($this->finalize_coupon($master_output)) return true;

		if($this->coupon_row->function_type == 'parent') $this->return_false('errNoRecord');
		$this->coupon_row = null;
		$this->initialize_coupon();
		return false;
		
	}

	protected function process_coupon_return( $coupon_row, $track_product_price=false ) {
		$user = JFactory::getUser();

		$db = JFactory::getDBO();	
			

		$_SESSION_product = $_SESSION_product_notax = $_SESSION_product_tax = 0;
		$_SESSION_shipping = $_SESSION_shipping_notax = $_SESSION_shipping_tax = 0;
		$usedproductids = array();
		
		
		if(empty($coupon_row)) return;

		//$coupon_row->params = !empty($coupon_row->params) ? json_decode($coupon_row->params) : '';
		$coupon_row->params = !empty($coupon_row->params) ? ( is_string($coupon_row->params) ? json_decode($coupon_row->params) : $coupon_row->params  ) : new stdclass;
		$coupon_row->cart_items = $this->cart->items;
		$coupon_row->cart_items_def = $this->cart->items_def;

		// ----------------------------------------------------
		// verify this coupon can be used in this circumstance
		// ----------------------------------------------------
		if($coupon_row->function_type == 'giftcert') {
		
			// check value to make sure the full value of the gift cert has not been used
			$sql = 'SELECT SUM(coupon_discount+shipping_discount) FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_id='.$coupon_row->id.' GROUP BY coupon_id';
			$db->setQuery($sql);
			$gift_cert_used_value = (float)$db->loadResult();
			if(!empty($gift_cert_used_value) && $gift_cert_used_value>=$coupon_row->coupon_value) {
			// total value of gift cert is used up
				return $this->return_false('errGiftUsed');
			}
			
			
			// check for giftcert products to exclude
			if(!empty($coupon_row->exclude_giftcert)) {
				$ids = ''; foreach($coupon_row->cart_items as $tmp) $ids .= $tmp['product_id'].',';
				if(!empty($ids)) {
					$sql = 'SELECT product_id FROM #__awocoupon_giftcert_product WHERE estore="'.$this->estore.'" AND product_id IN ('.substr($ids,0,-1).')';
					$db->setQuery($sql);
					$test_list = $db->loadResultArray();

					foreach($coupon_row->cart_items as $k=>$tmp) { if(in_array($tmp['product_id'],$test_list)) unset($coupon_row->cart_items[$k]); }
				}
			}
			
			
			// check products to verify on asset list
			$asset1list = !empty($coupon_row->params->asset1_type) ? $this->get_awocouponasset($coupon_row->id,$coupon_row->params->asset1_type) : '';

			if(!empty($asset1list)) {
				$ids_to_check = implode(',',array_keys($coupon_row->cart_items_def));
				if($coupon_row->params->asset1_type == 'product') {
				}
				elseif($coupon_row->params->asset1_type == 'category') {
					$tmp = $this->get_storecategory($ids_to_check);
					foreach($tmp as $tmp2) {
						if(isset($asset1list[$tmp2->category_id]))
							$coupon_row->cart_items_def[$tmp2->product_id]['category'] = $tmp2->category_id;
					}
				}
				elseif($coupon_row->params->asset1_type == 'manufacturer') {
					$tmp = $this->get_storemanufacturer($ids_to_check);
					foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['manufacturer'] = $tmp2->manufacturer_id;
				}
				elseif($coupon_row->params->asset1_type == 'vendor') {
					$tmp = $this->get_storevendor($ids_to_check);
					foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['vendor'] = $tmp2->vendor_id;
				}
				
				if($coupon_row->params->asset1_mode == 'include') {
					foreach($coupon_row->cart_items as $k=>$row) {
						if(!isset($asset1list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset1_type]])) unset($coupon_row->cart_items[$k]);
					}
				}
				elseif($coupon_row->params->asset1_mode == 'exclude') {
					foreach($coupon_row->cart_items as $k=>$row) {
						if(isset($asset1list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset1_type]])) unset($coupon_row->cart_items[$k]);
					}
				}

			}
			
			// check shipping
			$asset2list = !empty($coupon_row->params->asset2_type) && $coupon_row->params->asset2_type=='shipping' ? $this->get_awocouponasset($coupon_row->id,$coupon_row->params->asset2_type,'2') : '';
			$total_shipping_notax = $total_shipping = 0;
			$shipping_property = $this->get_storeshipping();
			if(!empty($shipping_property->total)) {
				$total_shipping_notax = $shipping_property->total_notax;
				$total_shipping = $shipping_property->total;
				if(!empty($asset2list)) {
					if($coupon_row->params->asset2_mode == 'include' && !isset($asset2list[$shipping_property->shipping_id])) $total_shipping_notax = $total_shipping = 0;
					elseif($coupon_row->params->asset2_mode == 'exclude' && isset($asset2list[$shipping_property->shipping_id])) $total_shipping_notax = $total_shipping = 0;
				}
			}
			$coupon_row->giftcert_shipping = $total_shipping;
			$coupon_row->giftcert_shipping_notax = $total_shipping_notax;
			
			
		} 
		elseif($coupon_row->function_type == 'coupon' || $coupon_row->function_type == 'shipping' || $coupon_row->function_type == 'buy_x_get_y') {

			{ // particulars
				$user_id = (int)$user->id; 
				$shopper_group_ids = array();
				$coupon_row->userlist 
					= $coupon_row->usergrouplist 
					= $coupon_row->productlist 
					= $coupon_row->categorylist 
					= $coupon_row->manufacturerlist 
					= $coupon_row->vendorlist 
					= $coupon_row->shippinglist
					= array();


				// verify total is up to the minimum value for the coupon
				if (!empty($coupon_row->min_value) && round($this->product_total,4)<$coupon_row->min_value) {
					return $this->return_false('errMinVal');
				}	

				if($coupon_row->user_type == 'user') { $coupon_row->userlist = $this->get_awocouponusers($coupon_row->id); } 
				elseif($coupon_row->user_type == 'usergroup') {
					$coupon_row->usergrouplist = $this->get_awocouponusergroup($coupon_row->id);
					$shopper_group_ids = $this->get_storeshoppergroupids($user_id);
				}
				

				if(empty($user_id)) {
					if(!empty($coupon_row->userlist) && (empty($coupon_row->params->user_mode) || $coupon_row->params->user_mode=='include')) {
					// not a logged in user
						return $this->return_false('errUserLogin');
					}
				}

				// verify the user is on the list for this coupon
				if(!empty($coupon_row->userlist)) {
					if( 
						((empty($coupon_row->params->user_mode) || $coupon_row->params->user_mode=='include') && !isset($coupon_row->userlist[$user_id]))
							|| 	
						($coupon_row->params->user_mode=='exclude' && isset($coupon_row->userlist[$user_id]))
					) {
					// not on user list
						return $this->return_false('errUserNotOnList');
					}
				}
				elseif(!empty($coupon_row->usergrouplist)) {					
					
					$is_in_list = false;
					foreach($shopper_group_ids as $shopper_group_id) {
						if (isset($coupon_row->usergrouplist[$shopper_group_id])) {
							$is_in_list = true;
							break;
						}
					}
					if( 
						(empty($coupon_row->params->user_mode) || $coupon_row->params->user_mode=='include' && !$is_in_list)
							|| 	
						($coupon_row->params->user_mode=='exclude' && $is_in_list)
					) {
					// not on shopper group list
						return $this->return_false('errUserGroupNotOnList');
					}
					
				}

				// number of use check
				if($coupon_row->num_of_uses!=0) {
					if($coupon_row->num_of_uses_type=='per_user') {
					// check to make sure user has not used it more than the limit
						$num = 0;
						if(!empty($user_id)) {
							$sql = 'SELECT COUNT(id) FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_id='.$coupon_row->id.' AND user_id='.$user_id.' AND (user_email IS NULL OR user_email="") GROUP BY coupon_id,user_id';
							$db->setQuery($sql);
							$num = (int)$db->loadResult();
						}
						if(!$this->is_customer_num_uses($coupon_row->id,$coupon_row->num_of_uses,$num)) {
						// per user: already used max number of times
							return $this->return_false('errUserMaxUse');
						}
					} elseif($coupon_row->num_of_uses_type=='total') {
					// check to make sure it has not been used more than the limit
						$sql = 'SELECT COUNT(id) FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_id='.$coupon_row->id.' GROUP BY coupon_id';
						$db->setQuery($sql);
						$num = $db->loadResult();
						if(!empty($num) && $num>=$coupon_row->num_of_uses) {
						// total: already used max number of times
							return $this->return_false('errTotalMaxUse');
						}
					}
				}
								
				// check for specials
				if(!empty($coupon_row->exclude_special)) {
					foreach($coupon_row->cart_items as $k=>$tmp) {
						if(!empty($tmp['discount'])) unset($coupon_row->cart_items[$k]);// remove specials
					}
					if(empty($coupon_row->cart_items)) {
						// all products in cart are on special
						return $this->return_false('errDiscountedExclude');
					}
					
				}
			
				// check for giftcert products
				if(!empty($coupon_row->exclude_giftcert) && !empty($coupon_row->cart_items)) {
					$sql = 'SELECT product_id FROM #__awocoupon_giftcert_product WHERE estore="'.$this->estore.'" AND product_id IN ('.implode(',',array_keys($coupon_row->cart_items_def)).')';
					$db->setQuery($sql);
					$test_list = $db->loadResultArray();

					foreach($coupon_row->cart_items as $k=>$tmp) {
						if(in_array($tmp['product_id'],$test_list)) unset($coupon_row->cart_items[$k]);
					}
					if(empty($coupon_row->cart_items)) {
						// all products in cart are gift certs
						return $this->return_false('errGiftcertExclude');
					}					
				}
			
			}
			
			
			$specific_min_value = 0;
			if(empty($coupon_row->params->asset1_type)) $coupon_row->params->asset1_type = '';
			if($coupon_row->function_type=='coupon') {
				if($coupon_row->params->asset1_type=='product') {
					// return product lists
					$coupon_row->productlist = $this->get_awocouponasset($coupon_row->id,'product');

					// verify the product is on the list for this coupon
					if (!empty($coupon_row->productlist)) {
						if(empty($coupon_row->params->asset1_mode) || $coupon_row->params->asset1_mode == 'include') {
						// inclusive list of products
							$is_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if (isset($coupon_row->productlist[$row['product_id']])) {
									$is_in_list = true;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break;
								}
							}
							if (!$is_in_list) {
							// (include) not on product list
								return $this->return_false('errProductInclList');
							}
						}
						elseif($coupon_row->params->asset1_mode == 'exclude') {
						// exclude products
							$is_not_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if (!isset($coupon_row->productlist[$row['product_id']])) {
									$is_not_in_list = true;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break;
								}
							}
							if(!$is_not_in_list) {
							// (exclude) all on product list
								return $this->return_false('errProductExclList');
							}
						}
					}
				}
				elseif($coupon_row->params->asset1_type=='category') {
					// return category lists
					$coupon_row->categorylist = $this->get_awocouponasset($coupon_row->id,'category');

					// verify the category is on the list for this coupon
					if (!empty($coupon_row->categorylist)) {
						// retreive the products in the order and their categories
						// get categories
						$tmp = $this->get_storecategory(implode(',',array_keys($coupon_row->cart_items_def)));
						foreach($tmp as $tmp2) {
							if(isset($coupon_row->categorylist[$tmp2->category_id]))
								$coupon_row->cart_items_def[$tmp2->product_id]['category'] = $tmp2->category_id;
						}
						
						if(empty($coupon_row->params->asset1_mode) || $coupon_row->params->asset1_mode == 'include') {
						// inclusive list of categories
							$is_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if(isset($coupon_row->categorylist[@$coupon_row->cart_items_def[$row['product_id']]['category']])) {
									$is_in_list = true;
									$coupon_row->cart_items_def[$row['product_id']]['is_valid_category'] = 1;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break 2;
								}
							}
							if (!$is_in_list) {
							// (include) not on category list
								return $this->return_false('errCategoryInclList');
							}
						}
						elseif($coupon_row->params->asset1_mode == 'exclude') {
						// exclude categories
							$is_not_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if(!isset($coupon_row->categorylist[@$coupon_row->cart_items_def[$row['product_id']]['category']])) {
									$is_not_in_list = true;
									$coupon_row->cart_items_def[$row['product_id']]['is_valid_category'] = 1;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break 2;
								}
							}
							if(!$is_not_in_list) {
							// (exclude) all on category list
								return $this->return_false('errCategoryExclList');
							}
						}
					}
				}
				elseif($coupon_row->params->asset1_type=='manufacturer') {
					// return manufacturer lists
					$coupon_row->manufacturerlist = $this->get_awocouponasset($coupon_row->id,'manufacturer');

					// verify the manufacturer is on the list for this coupon
					if (!empty($coupon_row->manufacturerlist)) {
						// retreive the products in the order and their manufacturers
						// get manufacturers
						$tmp = $this->get_storemanufacturer(implode(',',array_keys($coupon_row->cart_items_def)));
						foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['manufacturer'] = $tmp2->manufacturer_id;
						
						if(empty($coupon_row->params->asset1_mode) || $coupon_row->params->asset1_mode == 'include') {
						// inclusive list of manufacturers
							$is_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if(isset($coupon_row->manufacturerlist[$coupon_row->cart_items_def[$row['product_id']]['manufacturer']])) {
									$is_in_list = true;
									$coupon_row->cart_items_def[$row['product_id']]['is_valid_manufacturer'] = 1;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break 2;
								}
							}
							if (!$is_in_list) {
							// (include) not on manufacturer list
								return $this->return_false('errManufacturerInclList');
							}
						}
						elseif($coupon_row->params->asset1_mode == 'exclude') {
						// exclude manufacturers
							$is_not_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if(!isset($coupon_row->manufacturerlist[$coupon_row->cart_items_def[$row['product_id']]['manufacturer']])) {
									$is_not_in_list = true;
									$coupon_row->cart_items_def[$row['product_id']]['is_valid_manufacturer'] = 1;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break 2;
								}
							}
							if(!$is_not_in_list) {
							// (exclude) all on manufacturer list
								return $this->return_false('errManufacturerExclList');
							}
						}
					}
				}
				elseif($coupon_row->params->asset1_type=='vendor') {
					// return vendor lists
					$coupon_row->vendorlist = $this->get_awocouponasset($coupon_row->id,'vendor');

					// verify the vendor is on the list for this coupon
					if (!empty($coupon_row->vendorlist)) {
						// retreive the products in the order and their vendors
						// get vendors
						$tmp = $this->get_storevendor(implode(',',array_keys($coupon_row->cart_items_def)));
						foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['vendor'] = $tmp2->vendor_id;
						
						if(empty($coupon_row->params->asset1_mode) || $coupon_row->params->asset1_mode == 'include') {
						// inclusive list of vendors
							$is_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if(isset($coupon_row->vendorlist[$coupon_row->cart_items_def[$row['product_id']]['vendor']])) {
									$is_in_list = true;
									$coupon_row->cart_items_def[$row['product_id']]['is_valid_vendor'] = 1;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break 2;
								}
							}
							if (!$is_in_list) {
							// (include) not on vendor list
								return $this->return_false('errVendorInclList');
							}
						}
						elseif($coupon_row->params->asset1_mode == 'exclude') {
						// exclude vendors
							$is_not_in_list = false;
							foreach($coupon_row->cart_items as $row) {
								if(!isset($coupon_row->vendorlist[$coupon_row->cart_items_def[$row['product_id']]['vendor']])) {
									$is_not_in_list = true;
									$coupon_row->cart_items_def[$row['product_id']]['is_valid_vendor'] = 1;
									$specific_min_value += $row['qty'] * $row['product_price'];
									//break 2;
								}
							}
							if(!$is_not_in_list) {
							// (exclude) all on vendor list
								return $this->return_false('errVendorExclList');
							}
						}
					}
				}
			}

			elseif($coupon_row->function_type=='shipping') {
				// return product lists
				$coupon_row->productlist = $this->get_awocouponasset($coupon_row->id,'product','2');

				// verify the product is on the list for this coupon
				if (!empty($coupon_row->productlist)) {
					if($coupon_row->params->asset2_mode == 'include') {
					// inclusive list of products
						$is_in_list = false;
						foreach($coupon_row->cart_items as $row) {
							if (isset($coupon_row->productlist[$row['product_id']])) {
								$is_in_list = true;
								$specific_min_value += $row['qty'] * $row['product_price'];
								//break;
							}
						}
						if (!$is_in_list) {
						// (include) not on product list
							return $this->return_false('errProductInclList');
						}
					}
					elseif($coupon_row->params->asset2_mode == 'exclude') {
					// exclude products
						$is_not_in_list = false;
						foreach($coupon_row->cart_items as $row) {
							if (!isset($coupon_row->productlist[$row['product_id']])) {
								$is_not_in_list = true;
								$specific_min_value += $row['qty'] * $row['product_price'];
								//break;
							}
						}
						if(!$is_not_in_list) {
						// (exclude) all on product list
							return $this->return_false('errProductExclList');
						}
					}
				
				
					if($coupon_row->discount_type=='specific') {
						$is_not_in_list = false;
						foreach($coupon_row->cart_items as $row) {
							if (!isset($coupon_row->productlist[$row['product_id']])) {
								$is_not_in_list = true;
								break;
							}
						}
						if($is_not_in_list) {
						// (exclude) all on product list
							return $this->return_false('errShippingExclList');
						}
					}
				}
						
			
				if(!$this->get_storeshipping_isdefaultbypass($coupon_row->id)) {
				
					$shipping = $this->get_storeshipping();
					$shipping_id = $shipping->shipping_id;
				
				
					if(empty($shipping_id)) {
						//if($this->params->get('enable_no_shipping_select_coupon', 0) == 1) {
							return array(	'redeemed'=>true,
											'coupon_id'=>$coupon_row->id,
											'coupon_code'=>$coupon_row->coupon_code,
											'product_discount'=>0,
											'product_discount_notax'=>0,
											'product_discount_tax'=>0,
											'shipping_discount'=>0,
											'shipping_discount_notax'=>0,
											'shipping_discount_tax'=>0,
											'usedproducts'=>'',
											'force_add'=>1,
										);
						//} else {
						//	return $this->return_false('errShippingSelect');
						//}
					}
					
			
					// return shipping lists
					$coupon_row->shippinglist = $this->get_awocouponasset($coupon_row->id,'shipping');
					
					// verify the shipping is on the list for this coupon
					if (!empty($coupon_row->shippinglist)) {

						if(empty($coupon_row->params->asset1_mode) || $coupon_row->params->asset1_mode == 'include') {
						// inclusive list of shipping
							if(empty($coupon_row->shippinglist[$shipping_id])) {
							// (include) selected shipping not on shipping list
								return $this->return_false('errShippingInclList');
							}
						}
						elseif($coupon_row->params->asset1_mode == 'exclude') {
						// exclude shipping
							if(!empty($coupon_row->shippinglist[$shipping_id])) {
							// (exclude) selected shipping on shipping list'
								return $this->return_false('errShippingExclList');
							}
						}
					}
				}
			}
			
			elseif($coupon_row->function_type=='buy_x_get_y') {
				$do_continue = false;
				$do_count = 0;
				do {
					$products_x_count = $products_y_count = 0;
					$products_x_list = $products_y_list = $asset1list = array();
				
					$asset1list = $this->get_awocouponasset($coupon_row->id,$coupon_row->params->asset1_type);
					
					$ids_to_check = implode(',',array_keys($coupon_row->cart_items_def));
					if($coupon_row->params->asset1_type == 'product') {
					}
					elseif($coupon_row->params->asset1_type == 'category') {
						$tmp = $this->get_storecategory($ids_to_check);
						foreach($tmp as $tmp2) {
							if(isset($asset1list[$tmp2->category_id]))
								$coupon_row->cart_items_def[$tmp2->product_id]['category'] = $tmp2->category_id;
						}
					}
					elseif($coupon_row->params->asset1_type == 'manufacturer') {
						$tmp = $this->get_storemanufacturer($ids_to_check);
						foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['manufacturer'] = $tmp2->manufacturer_id;
					}
					elseif($coupon_row->params->asset1_type == 'vendor') {
						$tmp = $this->get_storevendor($ids_to_check);
						foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['vendor'] = $tmp2->vendor_id;
					}
					

					if($coupon_row->params->asset1_mode == 'include') {
						$is_in_list = false;
						foreach($coupon_row->cart_items as $row) {
							if(isset($asset1list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset1_type]])) {
								$is_in_list = true;
								$products_x_count += $row['qty'];
								$products_x_list[$row['product_id']] = $row['product_id'];
								$specific_min_value += $row['qty'] * $row['product_price'];
								//break 2;
							}
						}
						if (!$is_in_list) {
						// (include) not on manufacturer list
							return $this->return_false('errBuyXYList1IncludeEmpty');
						}
					}
					elseif($coupon_row->params->asset1_mode == 'exclude') {
						$is_not_in_list = false;
						foreach($coupon_row->cart_items as $row) {
							if(!isset($asset1list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset1_type]])) {
								$is_not_in_list = true;
								$products_x_count += $row['qty'];
								$products_x_list[$row['product_id']] = $row['product_id'];
								$specific_min_value += $row['qty'] * $row['product_price'];
								//break 2;
							}
						}
						if(!$is_not_in_list) {
						// (exclude) all on manufacturer list
							return $this->return_false('errBuyXYList1ExcludeEmpty');
						}
					}


					
					if (!empty($coupon_row->params->min_value_type) && $coupon_row->params->min_value_type=='specific'
					&& !empty($coupon_row->min_value) && round($specific_min_value,4)<$coupon_row->min_value ) {
						return $this->return_false('errMinVal');
					}	
							
					
					$asset2list = $this->get_awocouponasset($coupon_row->id,$coupon_row->params->asset2_type,'2');
					
					$ids_to_check = implode(',',array_keys($coupon_row->cart_items_def));
					if($coupon_row->params->asset2_type == 'product') {
					}
					elseif($coupon_row->params->asset2_type == 'category') {
						$tmp = $this->get_storecategory($ids_to_check);
						foreach($tmp as $tmp2) {
							if(isset($asset2list[$tmp2->category_id]))
								$coupon_row->cart_items_def[$tmp2->product_id]['category'] = $tmp2->category_id;
						}
					}
					elseif($coupon_row->params->asset2_type == 'manufacturer') {
						$tmp = $this->get_storemanufacturer($ids_to_check);
						foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['manufacturer'] = $tmp2->manufacturer_id;
					}
					elseif($coupon_row->params->asset2_type == 'vendor') {
						$tmp = $this->get_storevendor($ids_to_check);
						foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['vendor'] = $tmp2->vendor_id;
					}
				

					if(!$do_continue && !empty($coupon_row->params->addtocart)) {
						if($this->buyxy_addtocart($coupon_row,$products_x_count,$products_x_list,$asset2list)) $do_continue = true;
					}
					if($this->reprocess) return;
					$do_count++;
					
				} while ( $do_count<=1 && $do_continue);
				

				if($coupon_row->params->asset2_mode == 'include') {
					$is_in_list = false;
					foreach($coupon_row->cart_items as $row) {
						if(isset($asset2list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset2_type]])) {
							$is_in_list = true;
							$products_y_count += $row['qty'];
							$products_y_list[$row['product_id']] = $row['product_id'];
							//$specific_min_value += $row['qty'] * $row['product_price'];
							//break 2;
						}
					}
					if (!$is_in_list) {
					// (include) not on manufacturer list
						return $this->return_false('errBuyXYList2IncludeEmpty');
					}
				}
				elseif($coupon_row->params->asset2_mode == 'exclude') {
					$is_not_in_list = false;
					foreach($coupon_row->cart_items as $row) {
						if(!isset($asset2list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset2_type]])) {
							$is_not_in_list = true;
							$products_y_count += $row['qty'];
							$products_y_list[$row['product_id']] = $row['product_id'];
							//$specific_min_value += $row['qty'] * $row['product_price'];
							//break 2;
						}
					}
					if(!$is_not_in_list) {
						return $this->return_false('errBuyXYList2ExcludeEmpty');
					}
				}

				
			}
			
			
			//if ($coupon_row->discount_type == 'specific' && !empty($coupon_row->min_value) && round($specific_min_value,4)<$coupon_row->min_value) {
			//	return $this->return_false('errMinVal');
			//}	
			if (!empty($coupon_row->params->min_value_type) && $coupon_row->params->min_value_type=='specific'
			&& !empty($coupon_row->min_value) && round($specific_min_value,4)<$coupon_row->min_value ) {
				return $this->return_false('errMinVal');
			}	


		}
		
		else return $this->return_false('invalid function type');


		
		// for zero value coupons
		$coupon_row->coupon_value = (double)$coupon_row->coupon_value;
		if(empty($coupon_row->coupon_value) && empty($coupon_row->coupon_value_def)) {
			return array(	'redeemed'=>true,
							'coupon_id'=>$coupon_row->id,
							'coupon_code'=>$coupon_row->coupon_code,
							'product_discount'=>0,
							'product_discount_notax'=>0,
							'product_discount_tax'=>0,
							'shipping_discount'=>0,
							'shipping_discount_notax'=>0,
							'shipping_discount_tax'=>0,
							'usedproducts'=>'',
						);
		}
		
		
//foreach($coupon_row->cart_items as $k=>$row) { $coupon_row->cart_items[$k]['product_price'] = $coupon_row->cart_items[$k]['product_price_notax']; }
		
		// ----------------------------------------------------
		// Compute Coupon Discount based on coupon parameters
		// ----------------------------------------------------
		if($coupon_row->function_type == 'giftcert') {
		// gift certificate calculation
			$coupon_value = (float)($coupon_row->coupon_value - $gift_cert_used_value);
			if(!empty($coupon_value) && $coupon_value > 0) {
				$coupon_product_value = $coupon_shipping_value = 0;
				$coupon_product_value_notax = $coupon_shipping_value_notax = 0;
				
				// product
				$total_to_use = $total_to_use_notax = 0;
				$qty = 0;
				foreach($coupon_row->cart_items as $row) {
					$total_to_use += $row['qty'] * $row['product_price'];
					$total_to_use_notax += $row['qty'] * $row['product_price_notax'];
					$usedproductids[$row['product_id']] = $row['product_id'];
					$qty += $row['qty'];
				}
				$this->realtotal_verify($total_to_use,$total_to_use_notax);
				
				if(empty($total_to_use) && empty($coupon_row->giftcert_shipping)) return $this->return_false('errNoRecord');
				
				$postfix = $this->giftcert_discount_before_tax ? '_notax' : '';

				if(!empty($total_to_use)) { 
				# product calculation 
					$coupon_product_value = $coupon_product_value_notax = min(${'total_to_use'.$postfix},$coupon_value);
					
					if($this->giftcert_discount_before_tax) $coupon_product_value *= 1+(($total_to_use-$total_to_use_notax)/$total_to_use_notax);
					else $coupon_product_value_notax /= 1+(($total_to_use-$total_to_use_notax)/$total_to_use_notax);
					if($coupon_product_value>$total_to_use) $coupon_product_value = $total_to_use;
					if($coupon_product_value_notax>$total_to_use_notax) $coupon_product_value_notax = $total_to_use_notax;
				}

				{ # shipping calculation
					$total_shipping_notax = $coupon_row->giftcert_shipping_notax;
					$total_shipping = $coupon_row->giftcert_shipping;
						
					if( !empty(${'total_shipping'.$postfix}) && $coupon_value>${'coupon_product_value'.$postfix})
						$coupon_shipping_value = $coupon_shipping_value_notax = min((float) ${'total_shipping'.$postfix},$coupon_value-${'coupon_product_value'.$postfix});

					if(!empty($coupon_shipping_value)) {
						if($this->giftcert_discount_before_tax) $coupon_shipping_value *= 1+(($total_shipping-$total_shipping_notax)/$total_shipping_notax);
						else $coupon_shipping_value_notax /= 1+(($total_shipping-$total_shipping_notax)/$total_shipping_notax);
						if($coupon_shipping_value>$total_shipping) $coupon_shipping_value = $total_shipping;
						if($coupon_shipping_value_notax>$total_shipping_notax) $coupon_shipping_value_notax = $total_shipping_notax;
					}
				}
				
				// Total Amount 
				$_SESSION_product = $coupon_product_value;
				$_SESSION_product_notax = $coupon_product_value_notax;
				$_SESSION_shipping = $coupon_shipping_value;
				$_SESSION_shipping_notax = $coupon_shipping_value_notax;
				if($this->giftcert_discount_before_tax) {
					$_SESSION_product_tax = $_SESSION_product-$_SESSION_product_notax;
					$_SESSION_shipping_tax = $_SESSION_shipping-$_SESSION_shipping_notax;
				}
				
				
				//track product discount
				$this->calc_product_realprice($coupon_row,null,	$track_product_price,$_SESSION_product,$_SESSION_product_notax,$qty,$usedproductids);
				
			}
		}

		elseif($coupon_row->function_type == 'coupon') {
			if(!empty($coupon_row->coupon_value) ) {
			// product/category discount
				$total = $total_notax = $qty = 0;
				if($coupon_row->discount_type == 'specific') {
				//specific
					foreach($coupon_row->cart_items as $product_id=>$row) {
						$product_id = $row['product_id'];
						if( ($coupon_row->params->asset1_type=='product' && $coupon_row->params->asset1_mode == 'include' && isset($coupon_row->productlist[$product_id]))
						|| 	($coupon_row->params->asset1_type=='product' && $coupon_row->params->asset1_mode == 'exclude' && !isset($coupon_row->productlist[$product_id]))
						||  ($coupon_row->params->asset1_type=='category' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_category']))
						||  ($coupon_row->params->asset1_type=='manufacturer' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_manufacturer']))
						||  ($coupon_row->params->asset1_type=='vendor' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_vendor']))
						) {
							$usedproductids[] = $product_id;
							$qty += $row['qty'];
							$total += $row['qty'] * $row['product_price'];
							$total_notax += $row['qty'] * $row['product_price_notax'];
						}
					}
				}
				elseif($coupon_row->discount_type == 'overall') {
				// product total including tax
					foreach($coupon_row->cart_items as $row) {
						$usedproductids[] = $row['product_id'];
						$qty += $row['qty'];
						$total += $row['qty'] * $row['product_price'];
						$total_notax += $row['qty'] * $row['product_price_notax'];
					}
				}
				if(!empty($total)) {
					
					$_SESSION_product = $_SESSION_product_notax = $coupon_row->coupon_value;
					if($coupon_row->coupon_value_type == 'percent') {
						$_SESSION_product = round( $total * $_SESSION_product / 100, 4);
						$_SESSION_product_notax = round( $total_notax * $_SESSION_product_notax / 100, 4);
					}
					else {
						if($this->coupon_discount_before_tax) $_SESSION_product *= 1+(($total-$total_notax)/$total_notax);
						else $_SESSION_product_notax /= 1+(($total-$total_notax)/$total_notax);
					}
					
					if( $total < $_SESSION_product ) $_SESSION_product = (float)$total;
					if( $total_notax < $_SESSION_product_notax ) $_SESSION_product_notax = (float)$total_notax;
					
					$this->realtotal_verify($_SESSION_product,$_SESSION_product_notax);

					if($this->coupon_discount_before_tax) $_SESSION_product_tax = $_SESSION_product-$_SESSION_product_notax;

					//track product discount
					$this->calc_product_realprice($coupon_row,$coupon_row->coupon_value,$track_product_price,$_SESSION_product,$_SESSION_product_notax,$qty,$usedproductids);
				}
			}

			elseif(empty($coupon_row->coupon_value) && !empty($coupon_row->coupon_value_def) && preg_match("/^(\d+\-\d+([.]\d+)?;)+(\[[_a-z]+\=[a-z]+(\&[_a-z]+\=[a-z]+)*\])?$/",$coupon_row->coupon_value_def)) {
			// cumulative coupon calculation
				$vdef_table = $vdef_options = array();
				$each_row = explode(';',$coupon_row->coupon_value_def);
					
				//options
				$tmp = end($each_row);
				if(substr($tmp,0,1)=='[') {
					parse_str(trim($tmp,'[]'),$vdef_options);
					array_pop($each_row);
				}
				reset($each_row);
					
				foreach($each_row as $row) {
					if(strpos($row,'-')!==false) {
						list($p,$v) = explode('-',$row);
						$vdef_table[$p] = $v;
					}
				}
				$max_qty = 0;
				if(!empty($vdef_table)) {
					if(sizeof($vdef_table)>=2) {
						$tmp_table = $vdef_table;
						$tmp = array_pop($tmp_table);
						if(empty($tmp)) {
							krsort($tmp_table,SORT_NUMERIC);
							$max_qty = key($tmp_table);
						}
					}
			
			
					$qty = $qty_distinct = $total = $total_notax = 0;
				
					if($coupon_row->discount_type=='overall') {
						foreach($coupon_row->cart_items as $row) {
							$total_qty = 0;
							if(empty($max_qty)) $total_qty = $row['qty'];
							else {
								if($qty >= $max_qty);
								elseif(($qty+$row['qty']) <= $max_qty) $total_qty = $row['qty'];
								else $total_qty = $max_qty-$qty;
							}
							if(!empty($total_qty)) {
								$usedproductids[] = $row['product_id'];
								$total += $total_qty * $row['product_price'];
								$total_notax += $total_qty * $row['product_price_notax'];
							}
							$qty += $row['qty'];
							$qty_distinct += 1; 
						}
						
					} elseif($coupon_row->discount_type=='specific') {
						foreach($coupon_row->cart_items as $row) {
							$product_id = $row['product_id'];
							if( ($coupon_row->params->asset1_type=='product' && $coupon_row->params->asset1_mode == 'include' && isset($coupon_row->productlist[$product_id]))
								|| 	($coupon_row->params->asset1_type=='product' && $coupon_row->params->asset1_mode == 'exclude' && !isset($coupon_row->productlist[$product_id]))
								||  ($coupon_row->params->asset1_type=='category' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_category']))
								||  ($coupon_row->params->asset1_type=='manufacturer' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_manufacturer']))
								||  ($coupon_row->params->asset1_type=='vendor' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_vendor']))
							) {
								$total_qty = 0;
								if(empty($max_qty)) $total_qty = $row['qty'];
								else {
									if($qty >= $max_qty);
									elseif(($qty+$row['qty']) <= $max_qty) $total_qty = $row['qty'];
									else $total_qty = $max_qty-$qty;
								}
								if(!empty($total_qty)) {
									$usedproductids[] = $product_id;
									$total += $total_qty * $row['product_price'];
									$total_notax += $total_qty * $row['product_price_notax'];
								}
								$qty += $row['qty'];
								$qty_distinct += 1; 
							}
						}
					}
					if(!empty($qty)) {
						
						if(!empty($max_qty)) array_pop($vdef_table);
						krsort($vdef_table,SORT_NUMERIC);
						if(!empty($vdef_options['qty_type']) && $vdef_options['qty_type']=='distinct') $qty = $qty_distinct;

						foreach($vdef_table as $pcount=>$val) {
							if($pcount <= $qty) {
								$coupon_value = $val;
								break;
							}
						}
						if(!empty($coupon_value)) {
							$_SESSION_product = $_SESSION_product_notax = $coupon_value;

							if($coupon_row->coupon_value_type == 'percent') {
								$_SESSION_product = round( $total * $_SESSION_product / 100, 4);
								$_SESSION_product_notax = round( $total_notax * $_SESSION_product_notax / 100, 4);
							}
							else {
								if($this->coupon_discount_before_tax) $_SESSION_product *= 1+(($total-$total_notax)/$total_notax);
								else $_SESSION_product_notax /= 1+(($total-$total_notax)/$total_notax);
							}

							
							if( $total < $_SESSION_product ) $_SESSION_product = (float)$total;
							if( $total_notax < $_SESSION_product_notax ) $_SESSION_product_notax = (float)$total_notax;
							
							$this->realtotal_verify($_SESSION_product,$_SESSION_product_notax);

							if($this->coupon_discount_before_tax) $_SESSION_product_tax = $_SESSION_product-$_SESSION_product_notax;

							//track product discount
							$this->calc_product_realprice($coupon_row,$coupon_value,$track_product_price,$_SESSION_product,$_SESSION_product_notax,$qty,$usedproductids);
						} else {
						// cumulative coupon, threshold not reached
							return $this->return_false('errProgressiveThreshold');
						}
					}
				}
			}
		}
				
				
		elseif($coupon_row->function_type=='shipping') {
		// shipping discount
			$shipping_property = $this->get_storeshipping();
			$total = (float) $shipping_property->total;
			$total_notax = (float) $shipping_property->total_notax;

			if(!empty($total)) {
				$coupon_value = $coupon_row->coupon_value;
				$_SESSION_shipping = $_SESSION_shipping_notax = $coupon_row->coupon_value;
				if($coupon_row->coupon_value_type == 'percent') {
					$_SESSION_shipping = round( $total * $_SESSION_shipping / 100, 4);
					$_SESSION_shipping_notax = round( $total_notax * $_SESSION_shipping_notax / 100, 4);
				}
				else {
					if($this->coupon_discount_before_tax) $_SESSION_shipping *= 1+(($total-$total_notax)/$total_notax);
					else $_SESSION_shipping_notax /= 1+(($total-$total_notax)/$total_notax);
				}
				
				if( $total < $_SESSION_shipping ) $_SESSION_shipping = (float)$total;
				if( $total_notax < $_SESSION_shipping_notax ) $_SESSION_shipping_notax = (float)$total_notax;
				
				if($this->coupon_discount_before_tax) $_SESSION_shipping_tax = $_SESSION_shipping-$_SESSION_shipping_notax;
			}
		}
		
		elseif($coupon_row->function_type=='buy_x_get_y') {
		//$products_x_count = $products_y_count = 0;
		//$products_x_list = $products_y_list = $asset1list = array();
			
			$valid_items = array();
			$potential_items = $potential_items_details = array();
			$coupon_row->params->asset1_qty = (int)$coupon_row->params->asset1_qty;
			$coupon_row->params->asset2_qty = (int)$coupon_row->params->asset2_qty;
		
			$i=0;
			foreach($coupon_row->cart_items as $product_id=>$row) {
				if(!isset($products_y_list[$row['product_id']])) continue;
				for($j=0; $j<$row['qty']; $j++) { 
					if(!empty($coupon_row->params->product_match)) {
						$potential_items[$row['product_id']][$i] = $row['product_price'];
						$potential_items_details[$row['product_id']][$i] = array('product_id'=>$row['product_id'],'price'=>$row['product_price'],'product_price_notax'=>$row['product_price_notax']);
					}
					else {
						$potential_items[0][$i] = $row['product_price'];
						$potential_items_details[0][$i] = array('product_id'=>$row['product_id'],'price'=>$row['product_price'],'product_price_notax'=>$row['product_price_notax']);
					}
					$i++;
				}
			}
			
			if(!empty($potential_items)
			&& !empty($coupon_row->params->asset1_qty) && $coupon_row->params->asset1_qty>0
			&& !empty($coupon_row->params->asset2_qty) && $coupon_row->params->asset2_qty>0) {
				

				if(!empty($coupon_row->params->product_match)) {
					if($coupon_row->params->buy_xy_process_type == 'first');
					else {
						$tester = array();
						foreach($potential_items as $k=>$r1) {
							foreach($r1 as $r2) {
								$tester[$k] = $r2;
								break;
							}
						}
						if($coupon_row->params->buy_xy_process_type == 'lowest') asort($tester,SORT_NUMERIC);
						elseif($coupon_row->params->buy_xy_process_type == 'highest') arsort($tester,SORT_NUMERIC);

						$tmp = $potential_items;
						$potential_items = array();
						foreach($tester as $key=>$val) { $potential_items[$key] = $tmp[$key]; }
					}
				}
				else {
					if($coupon_row->params->buy_xy_process_type == 'first');
					elseif($coupon_row->params->buy_xy_process_type == 'lowest') asort($potential_items[0],SORT_NUMERIC);
					elseif($coupon_row->params->buy_xy_process_type == 'highest') arsort($potential_items[0],SORT_NUMERIC);
				}
				
				foreach($potential_items as $pindex=>$current_potential_item) {
					$t_products_x_count = !empty($coupon_row->params->product_match) ? count($current_potential_item) : $products_x_count;

				
					while($t_products_x_count>=$coupon_row->params->asset1_qty) { 
						$t_products_x_count -= $coupon_row->params->asset1_qty;
						$items = array();
						for($j=0;$j<$coupon_row->params->asset2_qty;$j++) {
							if(empty($current_potential_item)) break 2;
							
							$keys = array_keys($current_potential_item);
							$pkey = array_shift($keys);
							$item = $potential_items_details[$pindex][$pkey];
							unset($current_potential_item[$pkey]);
							if(in_array($item['product_id'],$products_x_list)) $t_products_x_count -= 1;
			
							if($t_products_x_count<0) break 2; // not enough products, error
							$items[] = $item;
						}
						$valid_items = array_merge($valid_items,$items);
					}
					
				}
//printrx($valid_items);
				
				if(!empty($coupon_row->params->max_discount_qty)) $valid_items = array_slice($valid_items,0,$coupon_row->params->max_discount_qty*$coupon_row->params->asset2_qty);
				
				if (empty($valid_items)) {
					return $this->return_false('errBuyXYList1IncludeEmpty');
				}

				$total = $total_notax = 0;
				$qty = count($valid_items);
				foreach($valid_items as $product_key=>$item) {
					$total += $item['price'];
					$total_notax += $item['product_price_notax'];
					$usedproductids[$item['product_id']] = $item['product_id'];
				}
					
				if(!empty($total)) {
					$_SESSION_product = $_SESSION_product_notax = $coupon_row->coupon_value;
					if($coupon_row->coupon_value_type == 'percent') {
						$_SESSION_product = round( $total * $_SESSION_product / 100, 4);
						$_SESSION_product_notax = round( $total_notax * $_SESSION_product_notax / 100, 4);
					}
					else {
						if($this->coupon_discount_before_tax) $_SESSION_product *= 1+(($total-$total_notax)/$total_notax);
						else $_SESSION_product_notax /= 1+(($total-$total_notax)/$total_notax);
					}
					
					if( $total < $_SESSION_product ) $_SESSION_product = (float)$total;
					if( $total_notax < $_SESSION_product_notax ) $_SESSION_product_notax = (float)$total_notax;
					
					$this->realtotal_verify($_SESSION_product,$_SESSION_product_notax);

					if($this->coupon_discount_before_tax) $_SESSION_product_tax = $_SESSION_product-$_SESSION_product_notax;

					//track product discount
					$this->calc_product_realprice($coupon_row,$coupon_row->coupon_value,$track_product_price,$_SESSION_product,$_SESSION_product_notax,$qty,$usedproductids);
				}
			}
			
		}
		


		if(!empty($_SESSION_product) || !empty($_SESSION_shipping)) {
			return array(	'redeemed'=>true,
							'coupon_id'=>$coupon_row->id,
							'coupon_code'=>$coupon_row->coupon_code,
							'product_discount'=>$_SESSION_product,
							'product_discount_notax'=>$_SESSION_product_notax,
							'product_discount_tax'=>$_SESSION_product_tax,
							'shipping_discount'=>$_SESSION_shipping,
							'shipping_discount_notax'=>$_SESSION_shipping_notax,
							'shipping_discount_tax'=>$_SESSION_shipping_tax,
							'usedproducts'=>!empty($usedproductids) ? implode(',',$usedproductids) : '',
						);
		}
	}

	protected function get_awocouponusers($id) {
		$db = JFactory::getDBO();	
		$db->setQuery('SELECT user_id FROM #__awocoupon_user WHERE coupon_id='.(int)$id);
		$tmp = $db->loadObjectList();
		$rtn = array();
		foreach($tmp as $tmp2) $rtn[$tmp2->user_id] = $tmp2->user_id;
		return $rtn;
	}
	
	protected function get_awocouponusergroup($id) {
		$db = JFactory::getDBO();	
		$db->setQuery('SELECT shopper_group_id FROM #__awocoupon_usergroup WHERE coupon_id='.(int)$id);
		$tmp = $db->loadObjectList();
		$rtn = array();
		foreach($tmp as $tmp2) $rtn[$tmp2->shopper_group_id] = $tmp2->shopper_group_id;
		return $rtn;
	}
	
	protected function get_awocouponasset($id,$type,$_table='1') {
		if($_table != '1' && $_table!='2') return;
		
		$db = JFactory::getDBO();	
		$db->setQuery('SELECT asset_id FROM #__awocoupon_asset'.$_table.' WHERE asset_type="'.awolibrary::dbEscape($type).'" AND coupon_id='.(int)$id);
		$tmp = $db->loadObjectList();
		$rtn = array();
		foreach($tmp as $tmp2) $rtn[$tmp2->asset_id] = $tmp2->asset_id;
		return $rtn;
	}
	
	protected function calc_product_realprice($coupon_row,$coupon_percent,$track_product_price,$coupon_value,$coupon_value_notax,$qty,$usedproductids) {
		if($track_product_price) {
			//track product discount
			$tracking_discount = 0;
			$tracking_discount_notax = 0;
			if($coupon_row->function_type!='buy_x_get_y' && $coupon_row->coupon_value_type == 'percent') {
				foreach($this->cart->items as $k=>$row) {
					if(!in_array($row['product_id'],$usedproductids)) continue;
					//if(!empty($usedproductids) && !in_array($row['product_id'],$usedproductids)) continue;
					
					$discount = round($row['product_price']*$coupon_percent/100,4);
					$this->cart->items[$k]['product_price'] -= $discount;
					$this->cart->items[$k]['totaldiscount'] += $row['qty']*$discount;
					$tracking_discount += $row['qty']*$discount;
					
					$discount = round($row['product_price_notax']*$coupon_percent/100,4);
					$this->cart->items[$k]['product_price_notax'] -= $discount;
					$this->cart->items[$k]['totaldiscount_notax'] += $row['qty']*$discount;
					$tracking_discount_notax += $row['qty']*$discount;
					
				}
				//penny problem
				if($tracking_discount != $coupon_value) {
					$this->cart->items[$k]['product_price'] -= $tracking_discount-$coupon_value;
					$this->cart->items[$k]['totaldiscount'] += $tracking_discount-$coupon_value;
				}
				if($tracking_discount_notax != $coupon_value_notax) {
					$this->cart->items[$k]['product_price_notax'] -= $tracking_discount_notax-$coupon_value_notax;
					$this->cart->items[$k]['totaldiscount_notax'] += $tracking_discount_notax-$coupon_value_notax;
				}
			}
			else {
				//make sure all the money is distributed
				$fail_safe = 0;
				while($tracking_discount < $coupon_value) {
					$each_discount = round(($coupon_value-$tracking_discount)/$qty,4);
					
					foreach($this->cart->items as $k=>$row) {
						if(!in_array($row['product_id'],$usedproductids)) continue;
						//if(!empty($usedproductids) && !in_array($row['product_id'],$usedproductids)) continue;
						
						$discount = min($each_discount,$row['product_price']);
						$this->cart->items[$k]['product_price'] -= $discount;
						$this->cart->items[$k]['totaldiscount'] += $row['qty']*$discount;
						$tracking_discount += $row['qty']*$discount;
					}
					
					$fail_safe++; if($fail_safe==200) break;
				}
				//penny problem
				if($tracking_discount != $coupon_value) {
					foreach($this->cart->items as $k=>$row) {
						if(!in_array($row['product_id'],$usedproductids)) continue;
						//if(!empty($usedproductids) && !in_array($row['product_id'],$usedproductids)) continue;
						
						$discount = min(($coupon_value-$tracking_discount)/$row['qty'],$row['product_price']);
						$this->cart->items[$k]['product_price'] -= $discount;
						$this->cart->items[$k]['totaldiscount'] += $row['qty']*$discount;
						$tracking_discount += $row['qty']*round($discount,4);
					}
				}
				
				
				//make sure all the money is distributed
				$fail_safe = 0;
				while($tracking_discount_notax < $coupon_value_notax) {
					$each_discount = round(($coupon_value_notax-$tracking_discount_notax)/$qty,4);
					
					foreach($this->cart->items as $k=>$row) {
						if(!in_array($row['product_id'],$usedproductids)) continue;
						
						$discount = min($each_discount,$row['product_price_notax']);
						$this->cart->items[$k]['product_price_notax'] -= $discount;
						$this->cart->items[$k]['totaldiscount_notax'] += $row['qty']*$discount;
						$tracking_discount_notax += $row['qty']*$discount;
					}
					
					$fail_safe++; if($fail_safe==200) break;
				}
				//penny problem
				if($tracking_discount_notax != $coupon_value_notax) {
					foreach($this->cart->items as $k=>$row) {
						if(!in_array($row['product_id'],$usedproductids)) continue;
						
						$discount = min(($coupon_value_notax-$tracking_discount_notax)/$row['qty'],$row['product_price_notax']);
						$this->cart->items[$k]['product_price_notax'] -= $discount;
						$this->cart->items[$k]['totaldiscount_notax'] += $row['qty']*$discount;
						$tracking_discount_notax += $row['qty']*round($discount,4);
					}
				}
						
				
			}
		}
	}

    protected function cleanup_coupon_code_helper( $order_id ) {
	// remove the coupon coupon_code(s)
		
		$coupon_session = $this->session->get('coupon', '', 'awocoupon');
		if(empty($coupon_session) ) return null;
		$coupon_session = unserialize($coupon_session);
		
		$this->session->set('coupon', null, 'awocoupon');
//printr($coupon_session);
		

		$db = JFactory::getDBO();	
		$user = JFactory::getUser ();

		$order_id = (int)$order_id;
		$user_email = $this->get_orderemail($order_id);

		if(empty($order_id)) $order_id = 'NULL';
		$user_email = empty($user_email) ? 'NULL' : '"'.awolibrary::dbEscape($user_email).'"';
		
		
		$children_coupons = $coupon_session['processed_coupons'];

		$coupon_ids = implode(',',array_keys($children_coupons));
		$sql = 'SELECT id,num_of_uses_type,num_of_uses,function_type,coupon_value FROM #__awocoupon WHERE estore="'.$this->estore.'" AND published=1 AND id IN ('.$coupon_ids.')';
		$db->setQuery( $sql );
		$rows = $db->loadObjectList();

		//$parent_coupon_id = (int)$coupon_session['coupon_id'];
		$parents = array();
		
		$coupon_details = awolibrary::dbEscape(json_encode($coupon_session));
		
		foreach($rows as $coupon_row) {
		// coupon found
		
			// mark coupon used
			$parent_coupon_id = (int)$children_coupons[$coupon_row->id]['coupon_entered_id'];
			if($parent_coupon_id!=$coupon_row->id) $parents[] = $parent_coupon_id;
			$usedproducts = !empty($children_coupons[$coupon_row->id]['usedproducts']) 
							? $children_coupons[$coupon_row->id]['usedproducts'] 
							: 'NULL';
							
			$postfix = '';
			if(($coupon_row->function_type == 'giftcert' && $this->giftcert_discount_before_tax)
			|| ($coupon_row->function_type != 'giftcert' && $this->coupon_discount_before_tax)) $postfix = '_notax';
			$shipping_discount = (float)$children_coupons[$coupon_row->id]['shipping_discount'.$postfix];
			$product_discount = (float)$children_coupons[$coupon_row->id]['product_discount'.$postfix];
			
			
			
			$sql = 'INSERT INTO #__awocoupon_history (estore,coupon_entered_id,coupon_id,user_id,user_email,coupon_discount,shipping_discount,order_id,productids,details)
				    VALUES ("'.$this->estore.'",'.$parent_coupon_id.','.$coupon_row->id.','.$user->id.','.$user_email.','.$product_discount.','.$shipping_discount.',"'.$order_id.'","'.$usedproducts.'","'.$coupon_details.'")';
			$db->setQuery( $sql );
			$db->query();
				
			if($coupon_row->function_type == 'giftcert') {
			// gift certificate
				$sql = 'SELECT SUM(coupon_discount+shipping_discount) as total FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_id='.$coupon_row->id.' GROUP BY coupon_id';
				$db->setQuery($sql);
				$total = $db->loadResult();
				if(!empty($total) && $total>=$coupon_row->coupon_value) {
				// credits maxed out
					$db->setQuery( 'UPDATE #__awocoupon SET published=-1 WHERE id='.$coupon_row->id );
					$db->query();
				}
			}
			elseif($coupon_row->function_type == 'coupon' && !empty($coupon_row->num_of_uses)) {
				
				if($coupon_row->num_of_uses_type == 'per_user') {
				// collect uses
					$coupon_row->userlist = array();		
					$sql = 'SELECT user_id FROM #__awocoupon_user WHERE coupon_id='.$coupon_row->id;
					$db->setQuery($sql);
					$tmp = $db->loadObjectList();
					foreach($tmp as $tmp2) $coupon_row->userlist[$tmp2->user_id] = $tmp2->user_id;

					if(!empty($coupon_row->userlist)) {
					// limited amount of users so can be removed, cant remove if no users since new registration users can use coupon
						$sql = 'SELECT user_id,COUNT(id) AS cnt FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_id='.$coupon_row->id.' GROUP BY coupon_id,user_id HAVING cnt>='.$coupon_row->num_of_uses;
						$db->setQuery($sql);
						$tmp = $db->loadObjectList();
						$used_array = array();
						foreach($tmp as $tmp2) $used_array[$tmp2->user_id] = $tmp2->user_id;
						$diff = array_diff($coupon_row->userlist,$used_array);
						if(empty($diff)) {
						// all users have used their coupons and can now be deleted
							$db->setQuery( 'UPDATE #__awocoupon SET published=-1 WHERE id='.$coupon_row->id );
							$db->query();
						}
					}
				}
				elseif($coupon_row->num_of_uses_type == 'total') {
				// limited amount of uses so can be removed
					$sql = 'SELECT COUNT(id) FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_id='.$coupon_row->id.' GROUP BY coupon_id';
					$db->setQuery($sql);
					$num = $db->loadResult();
					if(!empty($num) && $num>=$coupon_row->num_of_uses) {
					// already used max number of times
						$db->setQuery( 'UPDATE #__awocoupon SET published=-1 WHERE id='.$coupon_row->id );
						$db->query();
					}
				}
			}
		}


		foreach($parents as $parent_coupon_id) {
			$sql = 'SELECT id,num_of_uses_type,num_of_uses,function_type FROM #__awocoupon WHERE estore="'.$this->estore.'" AND published=1 AND id='.$parent_coupon_id;
			$db->setQuery( $sql );
			$parent_row = $db->loadObject();
			if(!empty($parent_row) && $parent_row->function_type=='parent') {
				if($parent_row->num_of_uses_type == 'total') {
				// limited amount of uses so can be removed
					$sql = 'SELECT COUNT(DISTINCT order_id) FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_entered_id='.$parent_row->id.' GROUP BY coupon_entered_id';
					$db->setQuery($sql);
					$num = $db->loadResult();
					if(!empty($num) && $num>=$parent_row->num_of_uses) {
					// already used max number of times
						$sql = 'UPDATE #__awocoupon SET published=-1 WHERE estore="'.$this->estore.'" AND id='.$parent_row->id;
						$db->setQuery( $sql );
						$db->query();
					}
				}
			}
		}
		
		$this->initialize_coupon();


		return true;
	}

	protected function cleanup_ordercancel_helper( $order_id, $order_status) {

		$order_id = (int)$order_id;
		if(empty($order_id)) return;
		
		$_cancelled_statuses = $this->params->get('ordercancel_order_status', '');
		if(empty($_cancelled_statuses)) return;
		if(!is_array($_cancelled_statuses)) $_cancelled_statuses = array($_cancelled_statuses);
		if(!in_array($order_status,$_cancelled_statuses)) return;
		
		$db = JFactory::getDBO();
		$db->setQuery('SELECT h.id,h.coupon_id,c.published FROM #__awocoupon_history h LEFT JOIN #__awocoupon c ON c.id=h.coupon_id WHERE h.order_id='.(int)$order_id);
		$rows = $db->loadObjectList();
		foreach($rows as $row) {
			$history_id = (int)$row->id;
			if(!empty($history_id)) {
				$db->setQuery('DELETE FROM #__awocoupon_history WHERE id='.$history_id);
				$db->query();
			}
				
			if($row->published == -1) {
				$db->setQuery('UPDATE #__awocoupon SET published=1 WHERE id='.$row->coupon_id);
				$db->query();
			}
		}
	}
	
	
	protected function validate_num_uses($coupon_row) {
		if(!empty($coupon_row->num_of_uses)) {
		// number of use check for parent coupon
			$db = JFactory::getDBO();	
			$user = JFactory::getUser();
			$user_id = (int)$user->id;
			if(empty($user_id)) {
			}
			if($coupon_row->num_of_uses_type=='per_user') {
			// check to make sure user has not used it more than the limit
				$num = 0;
				if(!empty($user_id)) {
					$sql = 'SELECT COUNT(DISTINCT order_id) FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_entered_id='.$coupon_row->id.' AND user_id='.$user_id.' AND (user_email IS NULL OR user_email="") GROUP BY coupon_entered_id,user_id';
					$db->setQuery($sql);
					$num = (int)$db->loadResult();
				}
				if(!$this->is_customer_num_uses($coupon_row->id,$coupon_row->num_of_uses,$num,true)) {
				// per user: already used max number of times
					return 'errUserMaxUse';
				}
			} elseif($coupon_row->num_of_uses_type=='total') {
			// check to make sure it has not been used more than the limit
				$sql = 'SELECT COUNT(DISTINCT order_id) FROM #__awocoupon_history WHERE estore="'.$this->estore.'" AND coupon_entered_id='.$coupon_row->id.' GROUP BY coupon_entered_id';
				$db->setQuery($sql);
				$num = $db->loadResult();
				if(!empty($num) && $num>=$coupon_row->num_of_uses) {
				// total: already used max number of times
					return 'errTotalMaxUse';
				}
			}
		}
		
		return null;
	}
	
	protected function initialize_coupon() { $this->session->set('coupon', 0, 'awocoupon'); }
	protected function initialize_coupon_auto() { $this->session->set('coupon_auto', 0, 'awocoupon'); }
	
	
	protected function set_coupon_auto($coupon_rows) {
		if(empty($coupon_rows)) $this->initialize_coupon_auto();
		else {
			$master_list = new stdClass();
			$master_list->uniquecartstring = $this->getuniquecartstringauto();
			$master_list->coupons = $coupon_rows;
			$this->session->set('coupon_auto', serialize($master_list), 'awocoupon');
		}
	}
	
	protected function get_coupon_auto() {
		$coupon_row = $this->session->get('coupon_auto', '', 'awocoupon');
		if(!empty($coupon_row)) {
			$coupon_row = unserialize($coupon_row);
			if(!empty($coupon_row->coupons)) return $coupon_row->coupons;
		}
		return '';
	}
	
	protected function calc_coupon_session($master_output) {
		$language = JFactory::getLanguage();
		$language->load('com_awocoupon',JPATH_ADMINISTRATOR);

		$product_discount = $product_discount_notax = $product_discount_tax = $shipping_discount = $shipping_discount_notax = $shipping_discount_tax = 0;
		$usedproducts = '';
		$coupon_codes = $coupon_codes_noauto = $usedcoupons = array();
		$auto_codes = $this->get_coupon_auto();

		
		foreach($master_output as $coupon_id=>$r) {
		
			if(empty($r[1]['force_add']) && empty($r[1]['product_discount']) && empty($r[1]['shipping_discount'])) continue;
			$coupon_codes[] = $r[1]['coupon_code'];
			
			$isauto = false;
			if(!empty($auto_codes))  { 
				foreach($auto_codes as $auto_code) {
					if($auto_code->id == $r[1]['coupon_id']) {
						$isauto = true;
						break;
					}
				}
			}

			$display_text = '';
			if(!empty($r[0]->note)) {
				$match = array();
				preg_match('/{customer_display_text:(.*)?}/i',$r[0]->note,$match);
				if(!empty($match[1])) $display_text = $match[1];
			}
			
			if(!empty($display_text)) $coupon_codes_noauto[] = $display_text;
			elseif(!$isauto) $coupon_codes_noauto[] = $r[1]['coupon_code'];
			//$coupon_codes_noauto[] = !empty($display_text) 
			//							? $display_text
			//							: (!$isauto ? $r[1]['coupon_code'] : '('.JText::_('COM_AWOCOUPON_CP_DISCOUNT_AUTO').')');
			
			$product_discount += $r[1]['product_discount'];
			$product_discount_notax += $r[1]['product_discount_notax'];
			$product_discount_tax += $r[1]['product_discount_tax'];
			$shipping_discount += $r[1]['shipping_discount'];
			$shipping_discount_notax += $r[1]['shipping_discount_notax'];
			$shipping_discount_tax += $r[1]['shipping_discount_tax'];
			if(!empty($r[1]['usedproducts'])) $usedproducts .= $r[1]['usedproducts'].',';
			if(!empty($r[2])) $usedcoupons = $usedcoupons + $r[2];
			else {
				$usedcoupons[$r[1]['coupon_id']] = array(
						'coupon_entered_id'=>$r[1]['coupon_id'],
						'coupon_code'=>$r[1]['coupon_code'],
						'product_discount'=>$r[1]['product_discount'],
						'product_discount_notax'=>$r[1]['product_discount_notax'],
						'product_discount_tax'=>$r[1]['product_discount_tax'],
						'shipping_discount'=>$r[1]['shipping_discount'],
						'shipping_discount_notax'=>$r[1]['shipping_discount_notax'],
						'shipping_discount_tax'=>$r[1]['shipping_discount_tax'],
						'usedproducts'=>$r[1]['usedproducts'],
						'isauto'=>$isauto,
						'isgift'=>$r[0]->function_type=='giftcert' ? true : false,

					);
			}
		}
		if(empty($usedcoupons)) return null;
		
		if(!empty($auto_codes) && count($coupon_codes_noauto)!=count($coupon_codes)) array_unshift($coupon_codes_noauto,'('.JText::_('COM_AWOCOUPON_CP_DISCOUNT_AUTO').')');
		
		$session_array = array(
			'redeemed'=>true,
			'uniquecartstring'=>$this->getuniquecartstring(implode(';',$coupon_codes)),
			'coupon_id'=>count($coupon_codes)==1 ? key($master_output) : '--multiple--',
			'coupon_code'=>implode(', ',$coupon_codes_noauto),
			'coupon_code_internal'=>implode(';',$coupon_codes),
			'product_discount'=>$product_discount,
			'product_discount_notax'=>$product_discount_notax,
			'product_discount_tax'=>$product_discount_tax,
			'shipping_discount'=>$shipping_discount,
			'shipping_discount_notax'=>$shipping_discount_notax,
			'shipping_discount_tax'=>$shipping_discount_tax,
			'productids'=>$usedproducts,
			'processed_coupons'=>$usedcoupons,
			'cart_items'=>$this->cart->items,
		);
		$this->session->set('coupon', serialize($session_array), 'awocoupon');
		
		return $session_array;
		
	}
	
	protected function realtotal_verify(&$_SESSION_product,&$_SESSION_product_notax) { return; }

	protected function get_storeshipping_isdefaultbypass($coupon_id) { return false; }
	
	protected function is_customer_num_uses($coupon_id,$max_num_uses,$customer_num_uses,$is_parent=false) {
		
		$customer_num_uses = (int)$customer_num_uses;
		$max_num_uses = (int)$max_num_uses;
		
		if(!empty($customer_num_uses) && $customer_num_uses>=$max_num_uses) {
		// per user: already used max number of times
			return false;
		}
		
		return true;

	}

	protected function return_false($key) {
		static $awocoupon_err_msg;

		if($this->reprocess) return;
		
		if(		(empty($this->coupon_row) && empty($awocoupon_err_msg[-1]))
			||	(!empty($this->coupon_row)
						&& $this->coupon_row->function_type != 'parent'
						&& empty($this->coupon_row->isauto)
						&& empty($awocoupon_err_msg[$this->coupon_row->id]))
		) {
			$awocoupon_err_msg[empty($this->coupon_row) ? -1 : $this->coupon_row->id] = 1;
			// display error to screen, if coupon is being set
			$err = $this->params->get($key, $this->default_err_msg);
			if(!empty($err)) JFactory::getApplication()->enqueueMessage((!empty($this->coupon_row) ? $this->coupon_row->coupon_code.': ' : '').$err,'error');
		}

		return false;
	}

	private function buyxy_addtocart(&$coupon_row,$products_x_count,$products_x_list,$asset2list) {

					
		$potential_items = array();
		$coupon_row->params->asset1_qty = (int)$coupon_row->params->asset1_qty;
		$coupon_row->params->asset2_qty = (int)$coupon_row->params->asset2_qty;
		$products_y_list = array();
		$products_y_count = 0;
		if($coupon_row->params->asset2_mode == 'include') {
			foreach($coupon_row->cart_items as $row) {
				if(isset($asset2list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset2_type]])) {
					$products_y_count += $row['qty'];
					$products_y_list[$row['product_id']] = $row['product_id'];
				}
			}
		}
		elseif($coupon_row->params->asset2_mode == 'exclude') {
			foreach($coupon_row->cart_items as $row) {
				if(!isset($asset2list[@$coupon_row->cart_items_def[$row['product_id']][$coupon_row->params->asset2_type]])) {
					$products_y_count += $row['qty'];
					$products_y_list[$row['product_id']] = $row['product_id'];
				}
			}
		}


		
		$i=0;
		foreach($coupon_row->cart_items as $product_id=>$row) {
			for($j=0; $j<$row['qty']; $j++) { 
				if(!empty($coupon_row->params->product_match)) {
					$potential_items[$row['product_id']][$i] = $row['product_id'];
				}
				else {
					$potential_items[0][$i] = $row['product_id'];
				}
				$i++;
			}
		}
					
		if(empty($potential_items)
		|| empty($coupon_row->params->asset1_qty) && $coupon_row->params->asset1_qty>0
		|| empty($coupon_row->params->asset2_qty) && $coupon_row->params->asset2_qty>0) return;
			
				
		if(!empty($coupon_row->params->max_discount_qty));
				
		$adding = array();			
		$used_max_discount_qty = 0;
		foreach($potential_items as $pindex=>$current_potential_item) {
			
			$t_products_x_count = !empty($coupon_row->params->product_match) ? count($current_potential_item) : $products_x_count;
			$t_products_y_count = 0;
			while($t_products_x_count>0) { 
				if(!empty($coupon_row->params->max_discount_qty) && $used_max_discount_qty>=$coupon_row->params->max_discount_qty) break;
				for($i=0; $i<$coupon_row->params->asset1_qty; $i++) {
					$is_unset = false;
					foreach($current_potential_item as $ppindex=>$product_id) {
						if($t_products_x_count<=0) break 3;
						if(isset($products_x_list[$product_id])) {
							$is_unset = true;
							unset($current_potential_item[$ppindex]);
							$t_products_x_count--;
							break;
						}
					}
					if(!$is_unset) break 2;
				}
				
				$used_max_discount_qty++;
				
				for($i=0; $i<$coupon_row->params->asset2_qty; $i++) {
					$isfound_ppindex = -1;
					foreach($current_potential_item as $ppindex=>$product_id) {
						if(isset($products_y_list[$product_id])) {
							$isfound_ppindex = $ppindex;
							unset($current_potential_item[$ppindex]);
							break;
						}
					}
					if($isfound_ppindex==-1) $t_products_y_count += 1;
				}
			}
			/*while($t_products_x_count>=$coupon_row->params->asset1_qty) { 
				if(!empty($coupon_row->params->max_discount_qty) && $used_max_discount_qty>=$coupon_row->params->max_discount_qty) break 2;
				for($i=0; $i<$coupon_row->params->asset1_qty; $i++) {
					$is_unset = false;
					foreach($current_potential_item as $ppindex=>$product_id) {
						if(isset($products_x_list[$product_id])) {
							unset($current_potential_item[$ppindex]);
							$is_unset = true;
							break;
						}
					}
					if(!$is_unset) break 2;
				}
				
				$used_max_discount_qty++;
				//$t_products_x_count -= $coupon_row->params->asset1_qty+$coupon_row->params->asset2_qty;
				$t_products_x_count -= $coupon_row->params->asset1_qty;
				$t_products_y_count += $coupon_row->params->asset2_qty;
			}
			
			if(empty($t_products_y_count)) continue;
			
			foreach($current_potential_item as $product_id) {
				if(empty($t_products_y_count)) break;
				if($coupon_row->params->asset2_mode == 'include') {
					$is_in_list = false;
					foreach($coupon_row->cart_items as $row) {
						if(isset($asset2list[@$coupon_row->cart_items_def[$product_id][$coupon_row->params->asset2_type]])) {
							$is_in_list = true;
							break;
						}
					}
					if (!$is_in_list)continue;
				}
				elseif($coupon_row->params->asset2_mode == 'exclude') {
					$is_not_in_list = false;
					foreach($coupon_row->cart_items as $row) {
						if(!isset($asset2list[@$coupon_row->cart_items_def[$product_id][$coupon_row->params->asset2_type]])) {
							$is_not_in_list = true;
							break;
						}
					}
					if(!$is_not_in_list) continue;
				}
				$t_products_y_count--;
			}*/
			if(empty($t_products_y_count) || $t_products_y_count<0) continue;
			
			@$adding[$pindex] += $t_products_y_count;

			if(!empty($coupon_row->params->max_discount_qty) && $used_max_discount_qty>=$coupon_row->params->max_discount_qty) break;
		}
		
		if(empty($adding)) return;
		

		foreach($adding as $item_id=>$qty) {
			if(!empty($item_id)) $this->add_to_cart($item_id,$qty);
			else {
				$product_id = $this->buyxy_getproduct($coupon_row->params->asset2_mode,$coupon_row->params->asset2_type,$asset2list);
				if(!empty($product_id)) $this->add_to_cart($product_id,$qty);
			}
		}
		
		$this->define_cart_items();
		foreach($this->cart->items as $k=>$r) {
			$this->cart->items[$k]['totaldiscount'] = 0;
			$this->cart->items[$k]['totaldiscount_notax'] = 0;
			
			$is_found = false;
			foreach($coupon_row->cart_items as $k2=>$item) {
				if($item['product_id']==$r['product_id'] && $item['product_price']==$r['product_price'] && $item['discount']==$r['discount']) {
					$is_found = true;
					$coupon_row->cart_items[$k2]['qty'] = $r['qty'];
					break;
				}
			}
			if(!$is_found) {
				$coupon_row->cart_items[] = $this->cart->items[$k];
				$coupon_row->cart_items_def[$r['product_id']]['product'] = $r['product_id'];
				if($coupon_row->params->asset2_type!='product') 
					$coupon_row->cart_items_def[$r['product_id']][$coupon_row->params->asset2_type] = $coupon_row->params->asset2_mode=='include' ? current($asset2list) : -1;
			}
		}
		
		return true;
					
	}
	
	
	private function array_iunique($array) { return array_intersect_key($array,array_unique(array_map('strtolower',$array))); }	
	private function in_arrayi($needle, $haystack) { return in_array(strtolower($needle), array_map('strtolower', $haystack)); }
}

