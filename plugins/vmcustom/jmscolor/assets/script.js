/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/
jQuery(document).ready(function($) {
	function get_imgname(src) {
		if(src.lastIndexOf("/")>=0) {
			return src.substring(src.lastIndexOf("/") + 1, src.length);
		}
		 return '';
	}
	function check_in_str(x,str) {
		if(str.indexOf(x) >= 0) {
			return true;
		} else {	
			return false;
		}	
	}
	var jmscolors = $('.jmscolors');
	var jmscolor_parent = jmscolors.parent();		
	var hinput = jmscolor_parent.children("input[type='hidden']");		
	//var hinput = $(".product-field-type-E input[type='hidden']");
	for(i=0;i<hinput.length;i++) {
		if(hinput[i].className!='imgs') {
			hinput[i].remove();
		}
	}
	//var pspan = $(".product-field-type-E .price-plugin");
	var pspan = jmscolor_parent.children(".price-plugin");
	for(i=0;i<pspan.length;i++) {			
		pspan.remove();			
	}
		
	$("form.js-recalculate").each(function(){
		if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
			var id= $(this).find('input[name="virtuemart_product_id[]"]').val();
			Virtuemart.setproducttype($(this),id);	
		}
	});
	var jmscustomselected = $('.jmscustomprice:checked');	
	if(jmscustomselected) {
		var color_id = 	jmscustomselected.attr('rel');
		var color_imgs = $('#imgs' + color_id).val();
		var imgs = $$('.product-image');		
		for(i=0;i<imgs.length;i++) {
			imgs[i].show();
			var img_name = get_imgname(imgs[i].src);			
			if(!check_in_str(img_name,color_imgs)) 
				imgs[i].hide();			
		}	
	}
	$('.jmscustomprice').click(function() {			
		var color_id = $(this).attr('rel');
		var color_imgs = $('#imgs' + color_id).val();
		var imgs = $$('.product-image');		
		for(i=0;i<imgs.length;i++) {
			imgs[i].show();
			var img_name = get_imgname(imgs[i].src);			
			if(!check_in_str(img_name,color_imgs)) 
				imgs[i].hide();			
		}		
			
		var jmscolor_selected = $('#jmscolor_selected');
		jmscolor_selected.val(color_id);
	});
});	