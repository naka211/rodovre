<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 * Originally created by Stanislav Scholtz, RuposTel.com
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


$file = JRequest::getVar('file', ''); 
$b64 = $this->model->getRawCouponImage($file); 
if (!empty($b64)) {
	$fi = pathinfo($file); 
	$ext = strtolower($fi['extension']);
	Header('Content-Type: image/'.$ext);
	echo base64_decode($b64);  
}
else echo 'Not found';

JFactory::getApplication()->close(); 
