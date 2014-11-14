<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

defined('_JEXEC') or die();
class JmsHelper {
	function getMediaDetail($img_id) {		
		$db = JFactory::getDBO ();
		
		$query = "SELECT a.file_title
		FROM #__virtuemart_medias AS a 
		LEFT JOIN #__virtuemart_product_medias AS b ON a.virtuemart_media_id = b.virtuemart_media_id 
		WHERE a.virtuemart_media_id = $img_id";
		$db->setQuery($query);		
		$img_name = $db->loadResult();		
		return $img_name;
		
	}
	function getProductColors($product_id) {
		$db = JFactory::getDbo();
		$query = "SELECT a.custom_value FROM #__virtuemart_product_customfields AS a
		LEFT JOIN #__virtuemart_customs AS b ON a.virtuemart_custom_id = b.virtuemart_custom_id 		  
		WHERE custom_element='jmscolor' AND virtuemart_product_id = $product_id";
		$db->setQuery($query);
		
		$color_ids = $db->loadObjectList();
		if(count($color_ids)==0) return null;
		$colors = array();
		for($i=0;$i<count($color_ids);$i++) {			
			$query = "SELECT a.color_imgs,b.color_icon FROM #__jmsvm_product_colors AS a 
			LEFT JOIN #__jmsvm_colors AS b ON a.color_id = b.id
			WHERE b.id = ".$color_ids[$i]->custom_value." AND a.product_id = $product_id";
			$db->setQuery($query);
			//echo $query;
			$tmp = $db->LoadObject();
			$imgs = explode(",",$tmp->color_imgs);
			$colors[$i]->color_id = $color_ids[$i]->custom_value;
			$colors[$i]->img = JmsHelper::getMediaDetail($imgs[0]);
			$colors[$i]->img2 = JmsHelper::getMediaDetail($imgs[1]);
			$colors[$i]->color_icon = $tmp->color_icon;
		}
		return 	$colors;
	}
}
?> 