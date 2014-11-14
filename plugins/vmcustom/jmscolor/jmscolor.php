<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @License 	http://joommasters.com/lisence.html
 **/

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');

class plgVmCustomJmscolor extends vmCustomPlugin {

	private $stockhandle = 0;
	
	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

	}
	function getImgFileName($imgpath) {
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$media_product_path = VmConfig::get('media_product_path');		
		$pos = strlen($media_product_path);
		return substr($imgpath,$pos,strlen($imgpath)-$pos);
	}
	function getImagesName($imgs) {		
		$db = JFactory::getDBO ();		
		$query = "SELECT a.file_url
		FROM #__virtuemart_medias AS a 
		LEFT JOIN #__virtuemart_product_medias AS b ON a.virtuemart_media_id = b.virtuemart_media_id 
		WHERE a.virtuemart_media_id IN ($imgs)";
		$db->setQuery($query);		
		$img_urls = $db->loadObjectList();
		$imgs = array();
		for($k=0;$k<count($img_urls);$k++) {
			$imgs[] = $this->getImgFileName($img_urls[$k]->file_url);
		}
		return implode(",",$imgs);
		
	}
	
	function getColorName($color_id) {
		$db = JFactory::getDBO ();		
		$query = "SELECT a.color_title
		FROM #__jmsvm_colors AS a
		WHERE id = $color_id";		
		$db->setQuery($query);				
		$color_name = $db->loadResult();		
		return $color_name;
	}	
		
	function getColorList($color_id=null,$row) {
		$db = JFactory::getDBO ();		
		$query = "SELECT a.color_title,id
		FROM #__jmsvm_colors AS a
		WHERE a.published = 1";
		$db->setQuery($query);						
		$colors = $db->loadObjectList();	
		return JHTML::_('select.genericlist', $colors,'field['.$row.'][custom_value]','style="display:block;" size="1"','id', 'color_title', $color_id );
	}
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {
		if ($field->custom_element != $this->_name) return '';
		$colorlist = $this->getColorList($field->custom_value,$row);		
		$html ='<div>';		
		$html .=$colorlist;		
		$html .='</div>';	
		$script = "
			jQuery( function($) {
				var fields = jQuery('input[name=field\\\\[$row\\\\]\\\\[custom_price\\\\]]');
				fields.eq(0).prop(\"readonly\",true);
			});
		";	
		$retValue .= $html.'<script type="text/javascript">'.$script.'</script>';
		$row++;
		return true  ;
	}	
	
	function plgVmOnViewCartModule( $product, $row,&$html) {		
		$html  .= '<div class="jmscolor_attributes">';		
		$html .='<span>'.JText::_($product->productCustom->custom_title).' : '.$this->getColorName($product->productCustom->custom_value).'</span>';
		$html  .= '</div>';	;	
		return true;
	}
	function plgVmOnViewCart($product, $row,&$html) {						
		$html  .= '<div class="jmscolor_attributes">';		
		$html .='<span>'.JText::_($product->productCustom->custom_title).' : '.$this->getColorName($product->productCustom->custom_value).'</span>';
		$html  .= '</div>';	
		return true;
	}
	function plgVmDisplayInOrderBE($item, $row,&$html) {				
		if (empty($item->productCustom->custom_element) or $item->productCustom->custom_element != $this->_name) return '';
		return $this->plgVmOnViewCart($item, $row,$html);
	}
	function plgVmDisplayInOrderFE($item, $row,&$html) {		
		return $this->plgVmOnViewCart($item, $row,$html);
	}
	function getCustomPrice($virtuemart_customfield_id) {
		$db = JFactory::getDbo();
		$query = "SELECT custom_value,virtuemart_product_id FROM #__virtuemart_product_customfields
				 WHERE virtuemart_customfield_id = $virtuemart_customfield_id";				 
		$db->setQuery($query);		
		$x = $db->loadObject();
		
		$query = "SELECT price FROM #__jmsvm_product_colors
				 WHERE color_id = $x->custom_value AND product_id = $x->virtuemart_product_id";				 
		$db->setQuery($query);
		return floatval($db->loadResult());
	}
	function getCustomPriceFromColor($color_id,$product_id) {
		$db = JFactory::getDbo();
		$query = "SELECT price FROM #__jmsvm_product_colors
				 WHERE color_id = $color_id AND product_id = $product_id";				 
		$db->setQuery($query);
		return floatval($db->loadResult());
	}
	function plgVmOnDisplayProductVariantFE($field,&$row,&$group) {		
		JHtml::stylesheet('style.css',JURI::root()."plugins/vmcustom/jmscolor/assets/");		
		// default return if it's not this plugin	
		$db = JFactory::getDbo();
		if ($field->custom_element != $this->_name) return '';	
	 	
		$product_id = JRequest::getInt('virtuemart_product_id');
		$query = "SELECT custom_value FROM `#__virtuemart_product_customfields` 
		WHERE virtuemart_customfield_id = $field->virtuemart_customfield_id";
		$db->setQuery($query);
		$color_id = $db->loadResult();
		//get row 
		$query = "SELECT DISTINCT(a.virtuemart_custom_id) FROM #__virtuemart_product_customfields AS a
		LEFT JOIN #__virtuemart_customs AS b ON a.virtuemart_custom_id = b.virtuemart_custom_id
		WHERE a.virtuemart_product_id = $product_id AND b.is_cart_attribute = 1 ORDER BY a.ordering";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$index = count($rows);
		for($i=0;$i<count($rows);$i++) {
			if($rows[$i]->virtuemart_custom_id==$field->virtuemart_custom_id)
				$index = $i;
		}
		$query = "SELECT a.*,b.color_icon,b.color_title FROM `#__jmsvm_product_colors` AS a 
		LEFT JOIN `#__jmsvm_colors` AS b ON a.color_id = b.id		
		WHERE a.product_id = $product_id AND a.color_id = $color_id";		
		$db->setQuery($query);
		$color = $db->loadObject();
		
		$checked = '';
		if($index==$row) $checked = 'checked="checked"';
		$show_full = intval($this->params->get('display_type'));
		$_class = "";
		if(!$show_full) $_class = ' only_icon';
		$html ='<div class="jmscolors'.$_class.'">'; 
		$html .='<a title="'.$color->color_title.'" rel="'.$color->color_id.'"><img src="'.JURI::Root().'administrator/components/com_jmsvmcustom/assets/color_icons/'.$color->color_icon.'" /></a>';
		$html .= '<input type="hidden" id="imgs'.$color->color_id.'" class="imgs" name="imgs'.$color->color_id.'" value="'.$this->getImagesName($color->color_imgs).'" />';
		$currency = CurrencyDisplay::getInstance ();		
		$custom_price = $this->getCustomPriceFromColor($color_id,$product_id);	 
		if($show_full) {
			$html .='<span class="add-price"> + '.$currency->priceDisplay($custom_price).'</span>';
		}
		$html .= '<input type="radio" '.$checked.' rel="'.$color->color_id.'" class="jmscustomprice" name="customPrice['.$index.']['.$field->virtuemart_custom_id.']" value="'.$field->virtuemart_customfield_id.'" />';				
		$html .='</div>';
		$view = JRequest::getVar('view');
		$task = JRequest::getVar('task');
		if($view=='productdetails' && $task!='recalculate')
			JHtml::script('script.js',JURI::root()."plugins/vmcustom/jmscolor/assets/");
		$group->display .= $html;	
		
		return true;
	}
	
	public function plgVmCalculateCustomVariant($product, &$productCustomsPrice,$selected){		
		$productCustomsPrice->custom_price = $this->getCustomPrice($selected);		
		return true;
	}
	
}

// No closing tag