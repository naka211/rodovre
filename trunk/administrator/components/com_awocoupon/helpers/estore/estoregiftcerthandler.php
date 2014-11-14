<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

class AwoCouponEstoreGiftcertHandler {

	var $db = null;
	var $params = null;
	var $cleanup_data = array();
	var $is_entry_new = true;

	function AwoCouponEstoreGiftcertHandler() {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
		
		$this->db  = JFactory::getDBO();
		$this->params = new awoParams();
	}

	
	protected function generate_auto_email($rows) {
		if(empty($rows)) return;

		
		
		$allcodes = array();
		jimport('joomla.mail.helper');
		
		
		$mail_key = array();
				
		// retreive gift cert profile
		$profiles = array();
		$sql = 'SELECT * FROM #__awocoupon_profile WHERE is_default=1';
		$this->db->setQuery( $sql );
		$profile_default = $this->db->loadAssocList();
		if(empty($profile_default)) {
			$sql = 'SELECT * FROM #__awocoupon_profile LIMIT 1';
			$this->db->setQuery( $sql );
			$profile_default = $this->db->loadAssocList();
			if(empty($profile_default)) {
				$this->cleanup_error( 'could not find a gift certificate profile');
				return false;
			}
		}
		$profile_default = awoLibrary::decrypt_profile(current($profile_default));
		
		$purchaser_user_id = $customer_first_name = $customer_last_name = '';
		foreach($rows as $row) {
			$this_mail_key = $this->getproductattributes($row);
			$mail_key[$this_mail_key['email']] = $this_mail_key;
            
			$current_profile = $profile_default;
			if(!empty($row->profile_id)) {
				if(!empty($profiles[$row->profile_id])) $current_profile = $profiles[$row->profile_id];
				else {
					$sql = 'SELECT * FROM #__awocoupon_profile WHERE id='.(int)$row->profile_id;
					$this->db->setQuery( $sql );
					$current_profile = $this->db->loadAssocList();
					$current_profile = empty($current_profile) 
							? $profile_default 
							: awoLibrary::decrypt_profile(current($current_profile));
				}
			}
			$profiles[$current_profile['id']] = $current_profile;
					
			$purchaser_user_id = $row->user_id;
			$customer_first_name = $row->first_name;
			$customer_last_name = $row->last_name;
			if($this->is_entry_new) {
				for($i=0; $i<$row->product_quantity; $i++) {
					$code = $this->get_giftcertcode($row);
					if(empty($code->coupon_id)) {
						$this->cleanup_error('could not create coupon code');
						return;
					}
						
					$this->db->setQuery('SELECT id,coupon_code,expiration,coupon_value,coupon_value_type FROM #__awocoupon WHERE id='.$code->coupon_id);
					$coupon_row = $this->db->loadObject();
					if(empty($coupon_row)) {
						$this->cleanup_error('could not find coupon');
						return;
					}

					$this->db->setQuery('UPDATE #__awocoupon SET order_id='.$row->order_id.' WHERE id='.$code->coupon_id);
					$this->db->query();
						
					$price = '';
					if(!empty($coupon_row->coupon_value))
						$price = $coupon_row->coupon_value_type=='amount' 
										? $this->formatcurrency($coupon_row->coupon_value)
										: round($coupon_row->coupon_value).'%';

					$allcodes[$this_mail_key['email']][] = array(
										'id'=>$coupon_row->id,
										'order_item_id'=>$row->order_item_id,
										'user_id'=>$row->user_id,
										'product_id'=>$row->product_id,
										'product_name'=>$row->order_item_name,
										'email'=>$row->email,
										'code'=>$coupon_row->coupon_code,
										'price'=>$price,
										'expiration'=>$coupon_row->expiration,
										'expirationraw'=>!empty($coupon_row->expiration) ? strtotime($coupon_row->expiration) : 0,
										'profile'=>$current_profile,
										'file'=>'',
								);
					if(!empty($row->vendor_email)) {
						$vendor_codes[$row->vendor_email]['name'] = $row->vendor_name;
						$vendor_codes[$row->vendor_email]['codes'][] = $coupon_row->coupon_code.' - '.$price;
					}
					if(!empty($row->vendor_email) && (int)$this->params->get('giftcert_vendor_enable', 0)==1) {
						$code_format = $this->params->get('giftcert_vendor_voucher_format', '<div>{voucher} - {price} - {product_name}</div>');
						if(strpos($code_format,'{voucher}')===false) $message .= '<div>{voucher}</div>';
						$vendor_codes[$row->vendor_email]['name'] = $row->vendor_name;
						$vendor_codes[$row->vendor_email]['codes'][] = str_replace(
									array('{voucher}','{price}','{product_name}','{purchaser_first_name}','{purchaser_last_name}','{today_date}','{order_id}','{order_number}',),
									array($coupon_row->coupon_code, $price, $row->order_item_name,$row->first_name,$row->last_name,JHTML::_('date',date('Y-m-d H:i:s')),$this->order->order_id,$this->order->order_number),
									$code_format);
					}
				}
			} else {
				if(empty($row->coupons)) continue;
				
				foreach($row->coupons as $kc=>$crow){
					$crow['profile'] = $current_profile;
					if(substr($crow['price'], -1)!='%') $crow['price'] = $this->formatcurrency($crow['price']);
					$allcodes[$this_mail_key['email']][] = $crow;
				}
			}
		}
				
		if(empty($allcodes)) return;
		
		// load language file
		$language = JFactory::getLanguage();
		$language->load('com_awocoupon',JPATH_ADMINISTRATOR);
				
		$codes = array();
				
		$store_name = $this->get_storename();
		$vendor_from_email = $this->get_storeemail();
		$orderstatuses = $this->get_orderstatuslist();


		foreach($allcodes as $this_mail_key=>$mycodes) {
	
			//print codes
			$attachments = array();
			$myprofiles = array();
			$products = array();
			$vouchers = array();
			$user = JFactory::getUser($mycodes[0]['user_id']);
			foreach($mycodes as $k=>$row) {
				$codes_array = array('i'=>$row['order_item_id'],'p'=>$row['product_id'],'c'=>$row['code'],);
				$vouchers[] = (object)array('voucher'=>$row['code'],'price'=>$row['price'],'expiration'=>!empty($row['expiration']) ? trim($row['expiration'],'"') : '');
				$products[$row['product_name']] = 1;
				$myprofiles[$row['profile']['id']] = $row['profile'];
						
				if($row['profile']['message_type']=='html' && !is_null($row['profile']['image'])) {
					$r_file = awoLibrary::writeToImage(
								$row['code'],
								$row['price'],
								$row['expirationraw'],
								'email',
								$row['profile'],
								null,
								array(
									'find'=>array(	'{siteurl}',
											'{store_name}',
											'{purchaser_username}',
											'{purchaser_first_name}',
											'{purchaser_last_name}',
											'{recipient_name}',
											'{recipient_email}',
											'{recipient_message}',
											'{order_id}',
											'{order_number}',
											'{order_status}',
											'{order_total}',
											'{order_date}',
											'{order_link}',
											'{today_date}',
											'{product_name}',
										),
									'replace'=>array(	JURI::root(),
											$store_name,
											$user->username,
											$customer_first_name,
											$customer_last_name,
											$mail_key[$this_mail_key]['recipient_name'],
											$this_mail_key,
											$mail_key[$this_mail_key]['message'],
											$this->order->order_id,
											$this->order->order_number,
											$orderstatuses[$this->order->order_status],
											$this->formatcurrency($this->order->order_total),
											JHTML::_('date', $this->order->created_on),
											$this->siteRoute($this->get_orderlink(),true,-1),
											JHTML::_('date',date('Y-m-d H:i:s')),
											$row['product_name'],
										), 	
									)
							);
					if($r_file === false) {
						$this->cleanup_error('cannot create gift certificate images');
						return false;
					}
					$attachments[] = $r_file;
					$allcodes[$this_mail_key][$k]['file'] = $r_file;
					$this->cleanup_data['files'][] = $r_file;
					
					if((int)$this->params->get('enable_frontend_image', 0)==1) {
						$fi = pathinfo($r_file); 
						$fname = $fi['basename']; 
						$codes_array['f'] = JFile::makeSafe($fi['basename']); 
					}
					
				}
			
				$codes[] = $codes_array;
			
			}
			$allcodes[$this_mail_key]['vouchers'] = $vouchers;
			$allcodes[$this_mail_key]['attachments'] = $attachments;
			$allcodes[$this_mail_key]['profiles'] = $myprofiles;
			$allcodes[$this_mail_key]['products'] = $products;
				
			//email codes
			if (!JMailHelper::isEmailAddress($this_mail_key)) {
				$this->cleanup_error('invalid to email');
				return false;
			}
		}



		foreach($allcodes as $this_mail_key=>$mycodes) {

			$user = JFactory::getUser($mycodes[0]['user_id']);
					
			//USE DEFAULT profile 
			//$profile = $profile_default;
			//if(isset($profiles[$profile_default['id']])) $profile = $profile_default;
			//else {
			//	$profile = count($profiles) == 1 ? current($profiles) : $profile_default;
			//}
			//profile logic 
			$profile = $profile_default;
			if(isset($mycodes['profiles'][$profile_default['id']])) $profile = $profile_default;
			else {
				$profile = count($mycodes['profiles']) == 1 ? current($mycodes['profiles']) : $profile_default;
			}
			
			$profile['lang_email_subject'] = awoLibrary::getLangUserData($profile['email_subject_lang_id'],$user->id);
			$profile['lang_email_body'] = awoLibrary::getLangUserData($profile['email_body_lang_id'],$user->id);
			$profile['lang_voucher_syntax'] = awoLibrary::getLangUserData($profile['voucher_text_lang_id'],$user->id);
			$profile['lang_voucher_exp_syntax'] = awoLibrary::getLangUserData($profile['voucher_text_exp_lang_id'],$user->id);
			
			$mycodes['text_gift'] = '';
			foreach($mycodes['vouchers'] as $voucher) {
				$expiration_text = !empty($voucher->expiration) ? str_replace('{expiration}',$voucher->expiration,$profile['lang_voucher_exp_syntax']) : '';
				$mycodes['text_gift'] .= str_replace(
							array('{voucher}','{price}','{expiration_text}'),
							array($voucher->voucher,$voucher->price,$expiration_text),
							$profile['lang_voucher_syntax']
				);
			}
							
			//vendor info
			$from_name = !empty($profile['from_name']) ? $profile['from_name'] : $store_name;
			$from_email = !empty($profile['from_email']) ? $profile['from_email'] : $vendor_from_email;
				
			// message info
			$to = $this_mail_key;
			$subject = $profile['lang_email_subject'];
			$bcc = !empty($profile['bcc_admin']) ? $from_email : null;
			$message = $profile['lang_email_body'];
			$is_embed = false;
			$embed_text = '';
			if($profile['message_type']=='text') {
				if(strpos($message,'{vouchers}')===false) $message .= "\r\n".'{vouchers}';
			} else {
				$mycodes['text_gift'] = nl2br($mycodes['text_gift']);
				if(is_null($profile['image']) && strpos($message,'{vouchers}')===false) $message .= "<br />".'{vouchers}';
				if(!is_null($profile['image'])) {
					if(strpos($message,'{image_embed}')!==false) {
						$is_embed = true;
						$i = 0;
						foreach($mycodes['attachments'] as $attachment) $embed_text .= '<div><img src="cid:couponimageembed'.(++$i).'"></div>';
					}
				}
			}

			$message = str_replace(	
							array(	'{siteurl}',
									'{store_name}',
									'{vouchers}',
									'{image_embed}',
									'{purchaser_username}',
									'{purchaser_first_name}',
									'{purchaser_last_name}',
									'{recipient_name}',
									'{recipient_email}',
									'{recipient_message}',
									'{order_id}',
									'{order_number}',
									'{order_status}',
									'{order_total}',
									'{order_date}',
									'{order_link}',
									'{today_date}',
									'{product_name}',
								),
							array(	JURI::root(),
									$store_name,
									$mycodes['text_gift'],
									$embed_text,
									$user->username,
									$customer_first_name,
									$customer_last_name,
									$mail_key[$this_mail_key]['recipient_name'],
									$to,
									$profile['message_type']=='html' ? nl2br($mail_key[$this_mail_key]['message']) : $mail_key[$this_mail_key]['message'],
									$this->order->order_id,
									$this->order->order_number,
									$orderstatuses[$this->order->order_status],
									$this->formatcurrency($this->order->order_total),
									JHTML::_('date', $this->order->created_on),
									$this->siteRoute($this->get_orderlink(),true,-1),
									JHTML::_('date',date('Y-m-d H:i:s')),
									implode(', ',array_keys($mycodes['products'])),
								), 
							$message
						);
		
			if ( awoLibrary::sendMail($from_email, $from_name, $to, $subject, $message, $profile['message_type']=='html' ? 1 : 0, $bcc, $mycodes['attachments'], $is_embed) !== true ) {
				$this->cleanup_error('cannot mail codes');
				return false;
			}
		}

		if($this->is_entry_new) {
			//update giftcert table so we dont send them more coupons by mistake
			$codes = urldecode(http_build_query($codes));
			$this->db->setQuery('INSERT INTO #__awocoupon_giftcert_order (estore,order_id,user_id,email_sent,codes) VALUES ("'.$this->estore.'",'.$this->order->order_id.','.$purchaser_user_id.',1,"'.awolibrary::dbEscape($codes).'")');
			$this->db->query();
					
			if((int)$this->params->get('giftcert_vendor_enable', 0)==1 && !empty($vendor_codes)) {
				$t_subject = $this->params->get('giftcert_vendor_subject', 'Vendor Email - Codes');
				$t_message = $this->params->get('giftcert_vendor_email', '');
				if(strpos($t_message,'{vouchers}')===false) $t_message .= '<br /><br />{vouchers}<br />';
				foreach($vendor_codes as $vendor_email=>$codes) {
					if(empty($vendor_email) || !JMailHelper::isEmailAddress($vendor_email)) continue;
					$subject = str_replace(
							array('{vendor_name}','{purchaser_first_name}','{purchaser_last_name}','{order_id}','{order_number}','{today_date}',),
							array($codes['name'],$customer_first_name,$customer_last_name,$this->order->order_id,$this->order->order_number,JHTML::_('date',date('Y-m-d H:i:s'))),
							$t_subject);
					$message = str_replace(
							array('{vendor_name}','{vouchers}','{purchaser_first_name}','{purchaser_last_name}','{order_id}','{order_number}','{today_date}',),
							array($codes['name'],implode('',$codes['codes']),$customer_first_name,$customer_last_name,$this->order->order_id,$this->order->order_number,JHTML::_('date',date('Y-m-d H:i:s'))),
							$t_message);
					awoLibrary::sendMail($vendor_from_email, $store_name, $vendor_email, $subject, $message, 1);
				}
			}
			
			
			
			// save for display in front end
			if((int)$this->params->get('enable_frontend_image', 0)==1) {
				if(!class_exists('JFolder')) jimport( 'joomla.filesystem.folder');
				if(!class_exists('JFile')) jimport( 'joomla.filesystem.file');

				$dir = JPATH_SITE.'/media/com_awocoupon/customers'; 
				if (!file_exists($dir)) JFolder::create($dir); 
				if (!empty($purchaser_user_id)) {
					$dir = $dir.'/'.$purchaser_user_id;
					if (!file_exists($dir)) {
						JFolder::create($dir); 
						// basic security
						file_put_contents($dir.'/.htaccess', 'error 500 !'); 
						file_put_contents($dir.'/index.html', 'error!'); 
					}
						   
					foreach($allcodes as $mycodes) {
						foreach($mycodes as $k=>$row) {
							if(!empty($row['file']) && file_exists($row['file'])) {
						   
								$f2 = file_get_contents($row['file']); 
								$fi = pathinfo($row['file']); 
								$filename = JFile::makeSafe($fi['basename']); 
								$fcontent = urldecode('%3c%3fphp+die()%3b+%3f%3e').base64_encode($f2);
						 // to read this file use:
						 /*
						   $fcontent = file_get_contents(JPATH_SITE.DS.'media'.DS.'customers'.DS.{user_id}.DS.filename_from_db.php); 
						   $fcontent = str_replace('<?php die(); ?>', '', $fcontent); 
						   echo $fcontent; // base64_encoded content of the file
						   
						 */
								// might not be compatible with FTP-based access
								file_put_contents($dir.'/'.$filename.'.php', urldecode('%3c%3fphp+die()%3b+%3f%3e').base64_encode($f2)); 
								
								// add table link
								$this->db->setQuery('INSERT INTO #__awocoupon_image (coupon_id,user_id,filename) VALUES ('.(int)$row['id'].','.$purchaser_user_id.',"'.awolibrary::dbEscape($filename).'")
														ON DUPLICATE KEY UPDATE filename="'.awolibrary::dbEscape($filename).'"');
								$this->db->query();
							}
						}
					} 
				}
			}
			
		}

		//delete created images
		foreach($allcodes as $mycodes) foreach($mycodes as $k=>$row) { if(!empty($row['file'])) { @unlink($row['file']); } }
	
		return true;
	}
	
	
	protected function formatcurrency($val) { return $val; }
	
	protected function getproductattributes($row) { 
		return array('recipient_name'=>$row->first_name.' '.$row->last_name,'email'=>$row->email,'message'=>'');
	}

	protected function cleanup_error($message) {
//trigger_error($message);

		if(!empty($this->cleanup_data['files'])) {
			foreach($this->cleanup_data['files'] as $file) {
				if(!empty($file)) { @unlink($file); }
			}
		}
		
		if(!$this->is_entry_new) return false;
		
		if(!empty($this->cleanup_data['coupon_codes'])) {
			foreach($this->cleanup_data['coupon_codes'] as $coupon_id) {
				$this->db->setQuery('DELETE FROM #__awocoupon WHERE id='.$coupon_id);
				$this->db->query();
			}
		}
		
		if(!empty($this->cleanup_data['manual_codes'])) {
			foreach($this->cleanup_data['manual_codes'] as $product_id=>$codes) {
				$this->db->setQuery('UPDATE #__awocoupon_giftcert_code SET status="active" WHERE estore="'.$this->estore.'" AND status="used" AND product_id='.$product_id.' AND code IN ("'.implode('","',$codes).'")');
				$this->db->query();
			}
		}

		return false;
	}
	
	protected function get_giftcertcode($order_row) {
	
		$update_codes = false;

		$coupon_code = null;
		$expirationdays = null;
		
		$usedstr = !empty($this->cleanup_data['manual_codes'][$order_row->product_id]) ? ' AND code NOT IN ("'.implode('","',$this->cleanup_data['manual_codes'][$order_row->product_id]).'")' : '';
		$sql = 'SELECT code FROM #__awocoupon_giftcert_code WHERE estore="'.$this->estore.'" AND product_id='.$order_row->product_id.' AND status="active" '.$usedstr.' LIMIT 1';
		$this->db->setQuery( $sql );
		$tmp = $this->db->loadResult();
		if(!empty($tmp)) $coupon_code = $tmp;
		
		
		if(!empty($order_row->expiration_number) && !empty($order_row->expiration_type)) {
			if($order_row->expiration_type=='day') $expirationdays = (int)$order_row->expiration_number;
			elseif($order_row->expiration_type=='month') $expirationdays = (int)$order_row->expiration_number*30;
			elseif($order_row->expiration_type=='year') $expirationdays = (int)$order_row->expiration_number*365;
		}
		
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/plgautogenerate.php';
		$rtn = awoAutoGenerate::generateCoupon($order_row->coupon_template_id,$coupon_code,$expirationdays,null,$this->estore);
		if(empty($rtn->coupon_id)) {
			return;
		}
		$this->cleanup_data['coupon_codes'][] = $rtn->coupon_id;
		
		
		if(!empty($coupon_code) && $rtn->coupon_code==$coupon_code) {
			$this->cleanup_data['manual_codes'][$order_row->product_id][] = $rtn->coupon_code;
			$this->db->setQuery('UPDATE #__awocoupon_giftcert_code SET status="used" WHERE estore="'.$this->estore.'" AND product_id='.$order_row->product_id.' AND code="'.$rtn->coupon_code.'" AND status="active"');
			$this->db->query();
		}
		
		return $rtn;
	}


	protected function get_orderstatuslist(){ return array(); }

	protected function siteRoute($urls, $xhtml = true, $ssl = null) {
		
		return JRoute::_(JURI::root().$urls);

		//===================================================================================
		
		$sef_plugins = array(
			'sef',
			'sef_advance',
			'shsef',
			'acesef',
		);
		!is_array($urls) and $urls = array($urls);
		$parsed_urls = array();
		$mainframe = JApplication::getInstance('site');
		$plugins = JPluginHelper::_load();
		$total = count($plugins);
		for($i = 0; $i < $total; $i++) {
			$plugins[$i]->type == 'system' 
				and in_array($plugins[$i]->name,$sef_plugins)
				and JPluginHelper::_import( $plugins[$i] );
		}
		$mainframe->triggerEvent('onAfterInitialise');
		$router = $mainframe->getRouter();
		foreach($urls AS $url) {
			/*
			$uri = $router->build($url); 
			$parsed_url = $uri->toString();
			$adminpos = strpos($parsed_url,'/administrator/');
			if (!($adminpos === false)) {
				$parsed_url = substr($parsed_url,$adminpos+15);
			}
			$parsed_urls[] = $parsed_url;     
			*/


			$uri = $router->build($url);
			$parsed_url = $uri->toString();
			$url = $uri->toString(array('path', 'query', 'fragment'));

			// Replace spaces
			$url = preg_replace('/\s/u', '%20', $url);
			$url = str_replace('/administrator/','/',$url);

			$ssl	= (int) $ssl;
			if ( $ssl ) {
				$uri	         = JURI::getInstance();
				$prefix = $uri->toString( array('host', 'port'));

				// Determine which scheme we want
				$scheme	= ( $ssl === 1 ) ? 'https' : 'http';

				// Make sure our url path begins with a slash
				if ( ! preg_match('#^/#', $url) ) $url	= '/' . $url;

				// Build the URL
				$url	= $scheme . '://' . $prefix . $url;
			}

			if($xhtml)  $url = str_replace( '&', '&amp;', $url );

			$parsed_urls[] = $url;     


		}
		return count($parsed_urls) == 1 ? array_shift($parsed_urls) : $parsed_urls;
	}	

}

