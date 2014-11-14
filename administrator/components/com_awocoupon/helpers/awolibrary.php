<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awocouponModel.php';
require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awocouponView.php';

class awoLibrary {

	static function getInstalledEstores() {
		$valid_estores = array(
				'hikashop'=>'Hikashop',
				'redshop'=>'redShop',
				'virtuemart'=>'Virtuemart',
				'virtuemart1'=>'Virtuemart 1',
		);
		$estores = array();
		$dir = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore';
		$dh  = opendir($dir);
		while (false !== ($name = readdir($dh))) {
			if($name=='.') continue;
			if($name=='..') continue;
			if(!is_dir($dir.'/'.$name)) continue;
			if(isset($valid_estores[$name])) $estores[] = $name;
		}


		$installedestores = array();
		foreach($estores as $estore) {
			if (!class_exists( 'AwoCoupon'.$estore.'Helper' )) require JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.$estore.'/helper.php';
			if(call_user_func(array('AwoCoupon'.$estore.'Helper','isInstalled'))) {
				$installedestores[] = $estore;
			}
		}
		return $installedestores;
	}

	static function profile_sendEmail($user,$codes,$profile,$tag_replace,$force_send=false) {
	
		jimport('joomla.mail.helper');
		//email codes
		if (!JMailHelper::isEmailAddress($user->email)) {
			return false;
		}

		$db  = JFactory::getDBO();

		// load language file
		$language = JFactory::getLanguage();
		$language->load('com_awocoupon',JPATH_ADMINISTRATOR);
				
		$languages = array();
        $languages[] = $user->getParam('language');
		$params = JComponentHelper::getParams('com_languages');
		$languages[] = $params->get('site');
		$languages[] = $params->get('administrator');
		$languages[] = 'en-GB';
		$lang_text = implode('","',array_unique($languages));

		$sql = 'SELECT text
				  FROM #__awocoupon_lang_text
				 WHERE elem_id='.$profile['email_subject_lang_id'].' AND lang IN ("'.$lang_text.'")
				 ORDER BY FIELD(lang,"'.$lang_text.'")
				 LIMIT 1';
		$db->setQuery($sql);
		$profile['lang_email_subject'] = $db->loadResult();
		$sql = 'SELECT text
				  FROM #__awocoupon_lang_text
				 WHERE elem_id='.$profile['email_body_lang_id'].' AND lang IN ("'.$lang_text.'")
				 ORDER BY FIELD(lang,"'.$lang_text.'")
				 LIMIT 1';
		$db->setQuery($sql);
		$profile['lang_email_body'] = $db->loadResult();
		$sql = 'SELECT text
				  FROM #__awocoupon_lang_text
				 WHERE elem_id='.$profile['voucher_text_lang_id'].' AND lang IN ("'.$lang_text.'")
				 ORDER BY FIELD(lang,"'.$lang_text.'")
				 LIMIT 1';
		$db->setQuery($sql);
		$profile['lang_voucher_syntax'] = $db->loadResult();
		$sql = 'SELECT text
				  FROM #__awocoupon_lang_text
				 WHERE elem_id='.$profile['voucher_text_exp_lang_id'].' AND lang IN ("'.$lang_text.'")
				 ORDER BY FIELD(lang,"'.$lang_text.'")
				 LIMIT 1';
		$db->setQuery($sql);
		$profile['lang_voucher_exp_syntax'] = $db->loadResult();

		//print codes
		$text_gift = '';
		$attachments = $attachments_ids = array();
		$myprofiles = array();
		$cleanup_data = array();
		
		
		foreach($codes as $k=>$row) {
			$expiration_text = !empty($row->expiration) ? str_replace('{expiration}',trim($row->expiration,'"'),$profile['lang_voucher_exp_syntax']) : '';
			$text_gift .= str_replace(
						array('{voucher}','{price}','{expiration_text}'),
						array($row->coupon_code,$row->coupon_price,$expiration_text),
						$profile['lang_voucher_syntax']
			);
			
			if($profile['message_type']=='html' && !is_null($profile['image'])) {
				$r_file = self::writeToImage($row->coupon_code,$row->coupon_price,!empty($row->expiration) ? strtotime($row->expiration) : 0,'email',$row->profile,null,$tag_replace);
				if($r_file === false) {
					if(!$force_send) {
						self::profile_cleanupError($cleanup_data, 'cannot create voucher images');
						return false;
					}
				} else {
					$attachments[] = $r_file;
					$attachments_ids[] = $row->id;
					$cleanup_data['files'][] = $r_file;
				}
			}
		}
				


		//vendor info
		$from_name = $profile['from_name'];
		if(empty($from_name)) {
			$config = JFactory::getConfig ();
			$from_name = $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'fromname' );
		}
		$from_email = $profile['from_email'];
		if(empty($from_email)) {
			$config = JFactory::getConfig ();
			$from_email = $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'mailfrom' );
		}
				
		// message info
		$subject = $profile['lang_email_subject'];
		$bcc = !empty($profile['bcc_admin']) ? $from_email : null;
		$message = $profile['lang_email_body'];
		$is_embed = false;
		$embed_text = '';
		if($profile['message_type']=='text') {
			if(strpos($message,'{vouchers}')===false) $message .= "\r\n".'{vouchers}';
		} else {
			$text_gift = nl2br($text_gift);
			if(empty($attachments) && strpos($message,'{vouchers}')===false) $message .= "<br />".'{vouchers}';
			if(!empty($attachments)) {
				if(strpos($message,'{image_embed}')!==false) {
					$is_embed = true;
					$i = 0;
					foreach($attachments as $attachment) $embed_text .= '<div><img src="cid:couponimageembed'.(++$i).'"></div>';
				}
			}
		}
		array_push($tag_replace['find'],'{image_embed}','{vouchers}');
		array_push($tag_replace['replace'],$embed_text,$text_gift);
		$message = str_replace(	$tag_replace['find'],$tag_replace['replace'],$message );
		
		if ( self::sendMail($from_email, $from_name, $user->email, $subject, $message, $profile['message_type']=='html' ? 1 : 0, $bcc, $attachments, $is_embed) !== true && !$force_send ) {
			self::profile_cleanupError($cleanup_data, 'cannot mail codes');
			return false;
		}
		
		// save for display in front end
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
		$awoparams = new awoParams();

		if((int)$awoparams->get('enable_frontend_image', 0)==1) {
			if(!class_exists('JFolder')) jimport( 'joomla.filesystem.folder');
			if(!class_exists('JFile')) jimport( 'joomla.filesystem.file');

			$dir = JPATH_SITE.'/media/com_awocoupon/customers'; 
			if (!file_exists($dir)) JFolder::create($dir); 
			@$user->id = (int)$user->id;
			if (!empty($user->id)) {
				$dir = $dir.'/'.$user->id;
				if (!file_exists($dir)) {
					JFolder::create($dir); 
					// basic security
					file_put_contents($dir.'/.htaccess', 'error 500 !'); 
					file_put_contents($dir.'/index.html', 'error!'); 
				}
				
				foreach($attachments as$k=>$file) {
					@$coupon_id = (int)$attachments_ids[$k];
					if(empty($coupon_id)) continue;
					if(empty($file) || !file_exists($file)) continue;
					
					$f2 = file_get_contents($file); 
					$fi = pathinfo($file); 
					$filename = JFile::makeSafe($fi['basename']); 
					$fcontent = urldecode('%3c%3fphp+die()%3b+%3f%3e').base64_encode($f2);
						   
					// might not be compatible with FTP-based access
					file_put_contents($dir.'/'.$filename.'.php', urldecode('%3c%3fphp+die()%3b+%3f%3e').base64_encode($f2)); 
								
					// add table link
					$db->setQuery('INSERT INTO #__awocoupon_image (coupon_id,user_id,filename) VALUES ('.$coupon_id.','.$user->id.',"'.self::dbEscape($filename).'")
											ON DUPLICATE KEY UPDATE filename="'.self::dbEscape($filename).'"');
					$db->query();
				}
			}
		}
			
			
			
		//delete created images
		if(!empty($cleanup_data['files'])) { foreach($cleanup_data['files'] as $file) { if(!empty($file)) { @unlink($file); } } }
		
		return true;
	
	}
	static function profile_cleanupError($cleanup_data, $message) {
		$db  = JFactory::getDBO();
//trigger_error($message);

		if(!empty($cleanup_data['files'])) {
			foreach($cleanup_data['files'] as $file) {
				if(!empty($file)) { @unlink($file); }
			}
		}
		
		return false;
	}

	static function writeToImage($code,$value,$expiration,$output,$profile=null,$profile_id=null,$dynamic_text=null){
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		
		if(!empty($profile_id)) {
			$db  = JFactory::getDBO();
			$sql = 'SELECT id,message_type,image,coupon_code_config,coupon_value_config,expiration_config,freetext1_config,freetext2_config,freetext3_config
					  FROM #__awocoupon_profile WHERE id='.(int)$profile_id.' AND message_type="html"';
			$db->setQuery( $sql );
			$profile = $db->loadAssocList();
			if(!empty($profile)) $profile = self::decrypt_profile(current($profile));
		}

		if(empty($profile)) return false;
		if(is_null($profile['image'])) return false;
		
		
		$baseimg = com_awocoupon_GIFTCERT_IMAGES.'/'.$profile['image'];

		if(!file_exists($baseimg)) return false;
		$image_parts = pathinfo($baseimg);
		$accepted_formats = array('png','jpg');
		if(!in_array($image_parts['extension'],$accepted_formats)) return false;
		
		// create image
		switch($image_parts['extension']) {
			case 'png': {
				if(!($im = @imagecreatefrompng($baseimg))) return false; 		
				imagealphablending($im, true); 									// setting alpha blending on
				imagesavealpha($im, true); 										// save alphablending setting (important)
				break;
			}
			case 'jpg': {
				if(!($im = @imagecreatefromjpeg($baseimg))) return false;
				break;
			}
		}
		
		if(self::writeToImage_helper($im,$code,$profile['coupon_code_config'])===false) return false;
		if(self::writeToImage_helper($im,$value,$profile['coupon_value_config'])===false) return false;
		if(!empty($expiration) && !empty($profile['expiration_config'])) {
			$str = date($profile['expiration_config']['text'],$expiration);
			if(self::writeToImage_helper($im,$str,$profile['expiration_config'])===false) return false;
		}
		if(!empty($profile['freetext1_config'])) {
			if(!empty($dynamic_text['find'])) $profile['freetext1_config']['text'] = str_replace($dynamic_text['find'],$dynamic_text['replace'],$profile['freetext1_config']['text']);
			if(self::writeToImage_helper($im,$profile['freetext1_config']['text'],$profile['freetext1_config'])===false) return false;
		}
		if(!empty($profile['freetext2_config'])) {
			if(!empty($dynamic_text['find'])) $profile['freetext2_config']['text'] = str_replace($dynamic_text['find'],$dynamic_text['replace'],$profile['freetext2_config']['text']);
			if(self::writeToImage_helper($im,$profile['freetext2_config']['text'],$profile['freetext2_config'])===false) return false;
		}
		if(!empty($profile['freetext3_config'])) {
			if(!empty($dynamic_text['find'])) $profile['freetext3_config']['text'] = str_replace($dynamic_text['find'],$dynamic_text['replace'],$profile['freetext3_config']['text']);
			if(self::writeToImage_helper($im,$profile['freetext3_config']['text'],$profile['freetext3_config'])===false) return false;
		}
		
		
		$args = (object)array(
			'code'=>$code,
			'value'=>$value,
			'expiration'=>$expiration,
			'dynamic_text'=>$dynamic_text,
			'profile'=>$profile,
			'is_preview'=> $output=='screen' ? true : false,
			'is_request'=> $output=='email' || !empty($profile_id) ? true : false,

		);
		JPluginHelper::importPlugin('awocoupon');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('giftcertimageOnBeforeCreate', array(&$im,$args));
		
		
		if($output=='screen') return $im;
		elseif($output=='email') {
			$path = com_awocoupon_GIFTCERT_TEMP;
			if(!is_dir($path)) if(!mkdir($path, 0777, true)) return false;

			// write coupon code
			switch($image_parts['extension']) {
				case 'png': {
					$filename = time().mt_rand().'.png';
					imagepng($im,$path.'/'.$filename);					// save image to file
					break;
				}
				case 'jpg': {
					$filename = time().mt_rand().'.jpg';
					imagejpeg($im,$path.'/'.$filename,82);					// save image to file
					break;
				}
			}

			imagedestroy($im);									// destroy resource

			return $path.'/'.$filename;
		}
		
		imagedestroy($im);										// destroy resource

	}
	static function writeToImage_helper(&$im,$text,$config){
		// write coupon code
		//$text = mb_encode_numericentity($text, array (0x0, 0xffff, 0, 0xffff), 'UTF-8');
		$font = com_awocoupon_GIFTCERT_FONTS.'/'.$config['font'];
		if(!file_exists($font)) return false;
		$rgb = self::html2rgb($config['color']);
		$color = imagecolorallocate($im,$rgb[0],$rgb[1],$rgb[2]);							// create the text color
		$align_func = 'imagettftextL';
		if($config['align'] == 'R') $align_func = 'imagettftextR';
		elseif($config['align'] == 'C') $align_func = 'imagettftextC';
		self::$align_func(
			$im,
			$config['size'],
			$config['y'],
			$color,
			$font,
			$text,
			$config['pad']
		);				// add the word 'code'
		return true;
	}
	static function imagettftextR($image, $fontsize, $y, $fontcolor, $font, $str, $padding=1) {
		$bbox = imagettfbbox ($fontsize, 0, $font, $str);
		$textWidth = $bbox[2] - $bbox[0];
		imagettftext ($image, $fontsize, 0, ImageSX($image)-$textWidth-$padding, $y, $fontcolor, $font, $str);
	}
	static function imagettftextL($image, $fontsize, $y, $fontcolor, $font, $str, $padding=1) {
		//imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text )
		imagettftext ($image, $fontsize, 0, $padding, $y, $fontcolor, $font, $str);
	}
	static function imagettftextC($image, $fontsize, $y, $fontcolor, $font, $str) {
		$bbox = imagettfbbox ($fontsize, 0, $font, $str);
		$textWidth = $bbox[2] - $bbox[0];
		imagettftext ($image, $fontsize, 0, (int)(ImageSX($image)-$textWidth)/2, $y, $fontcolor, $font, $str);
	}

	static function decrypt_profile($profile) {
		if($profile['message_type']=='html') {
			$profile['coupon_code_config'] = unserialize($profile['coupon_code_config']);
			$profile['coupon_value_config'] = unserialize($profile['coupon_value_config']);
			if(!empty($profile['expiration_config'])) $profile['expiration_config'] = unserialize($profile['expiration_config']);
			if(!empty($profile['freetext1_config'])) $profile['freetext1_config'] = unserialize($profile['freetext1_config']);
			if(!empty($profile['freetext2_config'])) $profile['freetext2_config'] = unserialize($profile['freetext2_config']);
			if(!empty($profile['freetext3_config'])) $profile['freetext3_config'] = unserialize($profile['freetext3_config']);
		}
		return $profile;				
	}
	
	static function html2rgb($color){    if ($color[0] == '#')        $color = substr($color, 1);    if (strlen($color) == 6)        list($r, $g, $b) = array($color[0].$color[1],                                 $color[2].$color[3],                                 $color[4].$color[5]);    elseif (strlen($color) == 3)        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);    else        return false;    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);    return array($r, $g, $b);}
	static function rgb2html($r, $g=-1, $b=-1){    if (is_array($r) && sizeof($r) == 3)        list($r, $g, $b) = $r;    $r = intval($r); $g = intval($g);    $b = intval($b);    $r = dechex($r<0?0:($r>255?255:$r));    $g = dechex($g<0?0:($g>255?255:$g));    $b = dechex($b<0?0:($b>255?255:$b));    $color = (strlen($r) < 2?'0':'').$r;    $color .= (strlen($g) < 2?'0':'').$g;    $color .= (strlen($b) < 2?'0':'').$b;    return '#'.$color;}
	
	static function sendMail($from, $fromname, $to, $subject, $body, $mode=0,  $bcc=null, $attachments=null, $is_embed=false ) {
//trigger_error(print_r(array('from'=>$from, 'fromname'=>$fromname, 'to'=>$to, 'subject'=>$subject, 'body'=>$body, 'mode'=>$mode,  'bcc'=>$bcc, 'attachments'=>$attachments),1));
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awomail.php';

	 	// Get a JMail instance
		$mail = new awoMail();

		$mail->setSender(array($from, $fromname));
		$mail->setSubject($subject);
		$mail->setBody($body);

		// Are we sending the email as HTML?
		if ( $mode ) {
			$mail->IsHTML(true);
		}

		$mail->addRecipient($to);
		$mail->addCC(null);
		$mail->addBCC($bcc);
		if(!empty($attachments) && is_array($attachments)) {
			$i = 0;
			foreach($attachments as $attachment) {
				$image_part = pathinfo($attachment);
				if($is_embed) $mail->AddEmbeddedImage($attachment,'couponimageembed'.(++$i),'voucher'.($i).'.'.$image_part['extension']);
				else $mail->AddAttachment($attachment,'voucher'.(++$i).'.'.$image_part['extension']);
			}
		}

		return  $mail->Send();
	}
	

	static function generate_coupon_code($estore='virtuemart') {
		$salt = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		do { $coupon_code = self::randomCode(rand(8,12),$salt); } while (self::isCodeUsed($coupon_code,$estore));
		return $coupon_code;
	}
	static function isCodeUsed($code,$estore) {
		$db = JFactory::getDBO();
			
		$sql = 'SELECT id FROM #__awocoupon WHERE estore="'.self::dbEscape($estore).'" AND coupon_code="'.$code.'"';
		$db->setQuery( $sql );
		$id = $db->loadResult();
		
		if(empty($id)) return false;
		return true;
	}
	static private function randomCode($length,$chars){
		$rand_id='';
		$char_length = strlen($chars);
		if($length>0) { for($i=1; $i<=$length; $i++) { $rand_id .= $chars[mt_rand(0, $char_length-1)]; } }
		return $rand_id;
	}
	
	
	
	static function installplugin($estore) {
		$db  = JFactory::getDBO();
		$plugins = array();

		$dir = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.$estore.'/extensions/plugins';			
			
		if(!class_exists('JFolder')) jimport( 'joomla.filesystem.folder');
		$files = JFolder::files($dir, '\.xml$', 1, true);
		if (!count($files)) return false;

		foreach ($files as $file) {
			$plugins = version_compare( JVERSION, '1.6.0', 'ge' )
						? self::pluginattr25($plugins,$file)
						: self::pluginattr15($plugins,$file);
		}
		
		if(empty($plugins)) return false;

		foreach($plugins as $plugin) { if(!self::installpluginrun($plugin)) return false; }

		return true;
		
	}
	
	static function installpluginrun($plugin) {
		if(empty($plugin['dir']) || empty($plugin['name']) || empty($plugin['group'])) return false;

		$db  = JFactory::getDBO();
		
		jimport('joomla.installer.installer');
		$installer = new JInstaller;
		
		if (!$installer->install($plugin['dir'])) return false;
						
		$sql = version_compare( JVERSION, '1.6.0', 'ge' )
					? 'UPDATE #__extensions SET enabled=1 WHERE type="plugin" AND element='.$db->Quote($plugin['name']).' AND folder='.$db->Quote($plugin['group'])
					: 'UPDATE #__plugins SET published=1 WHERE element='.$db->Quote($plugin['name']).' AND folder='.$db->Quote($plugin['group']);
		$db->setQuery($sql);
		$db->query();
		return true;
	}

	static function uninstallplugin($estore) {
		$db  = JFactory::getDBO();
		$plugins = array();

		$dir = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.$estore.'/extensions/plugins';			
			
		if(!class_exists('JFolder')) jimport( 'joomla.filesystem.folder');
		$files = JFolder::files($dir, '\.xml$', 1, true);
		if (!count($files)) return false;

		foreach ($files as $file) {
			$plugins = version_compare( JVERSION, '1.6.0', 'ge' )
						? self::pluginattr25($plugins,$file)
						: self::pluginattr15($plugins,$file);
		}

		if(empty($plugins)) return false;
		
		jimport('joomla.installer.installer');
		$installer = new JInstaller;
		
		foreach($plugins as $plugin) {
		

			$sql = version_compare( JVERSION, '1.6.0', 'ge' )
						? 'SELECT `extension_id` FROM #__extensions WHERE `type`="plugin" AND element='.$db->Quote($plugin['name']).' AND folder='.$db->Quote($plugin['group'])
						: 'SELECT `id` FROM #__plugins WHERE element = '.$db->Quote($plugin['name']).' AND folder='.$db->Quote($plugin['group']);
			$db->setQuery($sql);
			$db->query();
			$ids = $db->loadObjectList();
			foreach ($ids as $id) $result = $installer->uninstall('plugin', $id->extension_id);
		}

		return true;
		
	}


	public static function pluginattr15($plugins, $file) {

		$xmlDoc = & JFactory::getXMLParser();
		$xmlDoc->resolveErrors(true);

		if (!$xmlDoc->loadXML($file, false, true)) {
			unset ($xmlDoc);
			return;
		}
		$root = & $xmlDoc->documentElement;
		if (!is_object($root) || ($root->getTagName() != "install" && $root->getTagName() != 'mosinstall')) {
			unset($xmlDoc);
			return;
		}

		$p_group = $p_name = '';
		$type = $root->getAttribute('type');
		$p_group = $root->getAttribute('group');
		foreach($root->childNodes as $k=>$item) {
			if($item->nodeName == 'files') {
				$item2 = $item->firstChild;
				$p_name = $item2->getAttribute('plugin');
			}
		}
		
		unset ($xmlDoc);

		if(!empty($p_group) && !empty($p_name)) $plugins[] = array('dir'=>dirname($file),'group'=>$p_group,'name'=>$p_name);
		return $plugins;
	}
	public static function pluginattr25($plugins,$file) {
	
		if (!$xml = JFactory::getXML($file)) return;
		if ($xml->getName() != 'install' && $xml->getName() != 'extension') { unset($xml); return; }

		$p_group = $p_name = '';
		$type = (string) $xml->attributes()->type;
		$p_group = (string) $xml->attributes()->group;
		$p_name = (string) $xml->files->filename->attributes()->plugin;
			
		if(empty($p_group) || empty($p_name)) { unset($xml); return; }
			
		unset($xml);
		if(!empty($p_group) && !empty($p_name)) $plugins[] = array('dir'=>dirname($file),'group'=>$p_group,'name'=>$p_name);
	
		return $plugins;
	}

	static function getLangUser($user_id=0) {
		$languages = array();
		$user_id = (int)$user_id;
		
		if(empty($user_id)) $user = JFactory::getUser();
		else $user = JFactory::getUser($user_id);
				
        $languages[] = $user->getParam('language');
		
		$params = JComponentHelper::getParams('com_languages');
		$languages[] = $params->get('site');
		$languages[] = $params->get('administrator');

		$languages[] = 'en-GB';
		
		return array_unique($languages);
	}


	static function getLangUserData($elem_id,$user_id=0,$default=null) {
		$elem_id = (int)$elem_id;
		if(empty($elem_id)) return;
		
		$db  = JFactory::getDBO();
		
		static $stored_languages;
		if (!isset($stored_languages[$user_id])) $stored_languages[$user_id] = self::getLangUser($user_id);
		
		$languages = implode('","',$stored_languages[$user_id]);
		$sql = 'SELECT text FROM #__awocoupon_lang_text WHERE elem_id='.$elem_id.' AND lang IN ("'.$languages.'") ORDER BY FIELD(lang,"'.$languages.'") LIMIT 1';
		$db->setQuery($sql);
		$text = $db->loadResult();
		
		return !empty($text) ? $text : $default;
						
	}
	
	
	static function setLangData($elem_id,$text) {
		$elem_id = (int)$elem_id;
		
		$db  = JFactory::getDBO();
				
		//$text = self::dbEscape(str_replace( '<br>', '<br />', $text));
		$text = self::dbEscape($text);
		
		$params = JComponentHelper::getParams('com_languages');
		$lang = $params->get('administrator');
				
		if(empty($elem_id)) {
			if(empty($text)) return;
			
			$db->setQuery('SELECT MAX(elem_id) FROM #__awocoupon_lang_text');
			$elem_id = (int)$db->loadResult()+1;
			$sql = 'INSERT INTO #__awocoupon_lang_text (elem_id,lang,text) VALUES ('.$elem_id .',"'.$lang.'","'.$text.'")';
					
		}
		else {
			$db->setQuery('SELECT id FROM #__awocoupon_lang_text WHERE elem_id='.$elem_id.' AND lang="'.$lang.'"');
			$lang_id = $db->loadResult();
			if(empty($lang_id)) {
				if(empty($text)) return;
				$sql = 'INSERT INTO #__awocoupon_lang_text (elem_id,lang,text) VALUES ('.$elem_id .',"'.$lang.'","'.$text.'")';
			}
			else $sql = 'UPDATE #__awocoupon_lang_text SET text="'.$text.'" WHERE id='.$lang_id;
		}
		$db->setQuery($sql);
		$db->query();
		
		
		
		return $elem_id;

	}
	
	static function getliveupdateinfo() {
		$license = $localkey = '';
		$db = JFactory::getDBO();
		$db->setQuery('SELECT id,value FROM #__awocoupon_license');
		$rows = $db->loadObjectList();
		foreach($rows as $row) {
			if($row->id == 'license') $license = $row->value;
			elseif($row->id == 'local_key') $localkey = $row->value;
		}
		return array(
			'host'=>JURI::root(),
			'license'=>$license,
			'local_key'=>$localkey,
		);
	}

	static function getCaseSensitive() {
		$db = JFactory::getDBO();
		$db->setQuery('SHOW FULL COLUMNS FROM #__awocoupon LIKE "coupon_code"');
		$rtn = array_change_key_case((array)$db->loadObject());
		return substr($rtn['collation'],-4)=='_bin' ? true : false;
	}

	static function dbEscape($value,$extra=false) {
		$db = JFactory::getDBO();
		if(version_compare( JVERSION, '1.6.0', 'ge' )) $value = $db->escape($value,$extra);
		else $value = $db->getEscaped($value,$extra);
		
		return $value;
	}
}


if (!function_exists('printr')) { function printr($a) { echo '<pre>'.print_r($a,1).'</pre>'; } }
if (!function_exists('printrx')) { function printrx($a) { echo '<pre>'.print_r($a,1).'</pre>'; exit; } }
if (!function_exists('awotrace')) {
	function awotrace() {
		$data = debug_backtrace();
		$rtn = array();
		foreach($data as $r) $rtn[] = @$r['file'].':'.@$r['line'].' function '.@$r['function'];
		return array_reverse($rtn);
	}
}
if( !function_exists('json_encode') ) {
	require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/JSON.php';
	function json_encode($data) {
		$json = new Services_JSON();
		return( $json->encode($data) );
	}
}

if( !function_exists('json_decode') ) {
	require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/JSON.php';
	function json_decode($data, $bool=false) {
		if ($bool) $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		else $json = new Services_JSON();
		return( $json->decode($data) );
	}
}

if (!function_exists('curl_setopt_array')) {
	function curl_setopt_array(&$ch, $curl_options) {
		foreach ($curl_options as $option => $value) {
			if (!curl_setopt($ch, $option, $value)) {
				return false;
			} 
		}
		return true;
	}
}
