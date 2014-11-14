<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/
 
// no direct access
defined('_JEXEC') or die('Restricted access');

class VmcustomHelper {
	
	static function treeSelectList( &$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected ) {

		$children = array();

		if(count($src_list)) {
			foreach ($src_list as $v ) {
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		$ilist = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		foreach ($ilist as $v){
			$id = $v->id;
			
			$pre    = '- ';
			$spacer = '&nbsp;&nbsp;';
			

			$txt = '';
			$parent_id = $v->parent_id;
			while($parent_id != 0){
				$txt .= $pre;
				$parent_id = $ilist[$parent_id]->parent_id;
			}
			$txt  .= $v->title;
			$ilist[$id]->treename = $txt;
		}
		$this_treename = '';
		if(count($ilist)) {
			foreach ($ilist as $item) {
				if ($this_treename) {
					if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) {
						$tgt_list[] = JHTML::_('select.option', $item->id, $item->treename );
					}
				} else {
					if ($item->id != $src_id) {
						$tgt_list[] = JHTML::_('select.option', $item->id, $item->treename );
					} else {
						$this_treename = "$item->treename/";
					}
				}
			}
		}
		return JHTML::_('select.genericlist', $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
	}
	public static function check_media_exist($media_id,$product_id) {
		$db = JFactory::getDBO ();
		
		$query = "SELECT count(*) 
		FROM #__virtuemart_medias AS a 
		LEFT JOIN #__virtuemart_product_medias AS b ON a.virtuemart_media_id = b.virtuemart_media_id 
		WHERE b.virtuemart_product_id = $product_id
		AND a.virtuemart_media_id = $media_id";
		;				
		$db->setQuery($query);
				
		return $db->loadResult();
	}
	public static function getImgFileName($imgpath) {
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$media_product_path = VmConfig::get('media_product_path');		
		$pos = strlen($media_product_path);
		return substr($imgpath,$pos,strlen($imgpath)-$pos);
	}
	static function getImages($product_id) {
		
		$db = JFactory::getDBO ();
		
		$query = "SELECT a.file_url AS text,a.virtuemart_media_id AS value 
		FROM #__virtuemart_medias AS a 
		LEFT JOIN #__virtuemart_product_medias AS b ON a.virtuemart_media_id = b.virtuemart_media_id 
		WHERE b.virtuemart_product_id = $product_id";				
		$db->setQuery($query);		
		$imgs = $db->loadObjectList();
		for($i=0;$i<count($imgs);$i++) {
			$imgs[$i]->text = VmcustomHelper::getImgFileName($imgs[$i]->text);
		}
		return JHTML::_('select.genericlist', $imgs,'images','class="images" size="1"','value', 'text', null );
		
	}
	
	public static function getImageDetail($img_id) {
		
		$db = JFactory::getDBO ();		
		$query = "SELECT a.file_url
		FROM #__virtuemart_medias AS a 
		LEFT JOIN #__virtuemart_product_medias AS b ON a.virtuemart_media_id = b.virtuemart_media_id 
		WHERE a.virtuemart_media_id = $img_id";
		$db->setQuery($query);		
		$img_url = $db->loadResult();		
		$img_name = VmcustomHelper::getImgFileName($img_url);
		return $img_name;
		
	}

}
?>