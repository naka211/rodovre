/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/
function check_in_str(x,str) {
		if(str.indexOf(x) >= 0) {
			return true;
		} else {	
			return false;
		}	
}
function delete_action() {
	$$('.delete-img').addEvent('click', function(event){		
		var imgli = this.getParent();
		var imgul = imgli.getParent();
		var color_id = imgul.id.substr(9,2);
		var img_id = this.rel;
		var current_val = $('color_imgs' + color_id).value;
		var _exist = false;
		var c_pos = current_val.indexOf(img_id);
		if(check_in_str(img_id,current_val)) {
			_exist = true;			
			var new_val =  current_val.replace(img_id,'');			
			new_val =  new_val.replace(",,",',');
			if(new_val[0]==",") {
				new_val = new_val.substring(1,new_val.length);
			}
			if(new_val[new_val.length-1]==",") {
				new_val = new_val.substring(0,new_val.length-1);
			}
		}		
		
		if(_exist) {
			$('color_imgs' + color_id).value = new_val;
			imgli.remove();
		}
		
		
	});
}

window.addEvent ('domready', function () {
	if($('color_icon')!=null) 
		$('color_icon').addEvent('change', function(event){
			var site_url = $('site_url').value;
			$('color-icon-preview').innerHTML = '<img src="' + site_url + 'administrator/components/com_jmsvmcustom/assets/color_icons/'+ this.value + '" />';
		});	
	if($$('.images')!=null) 
		$$('.images').addEvent('change', function(event){			
			var select_act = this.getSelected();
			var img_name = select_act[0].get('text');			
			var imgb = this.getParent();
			var color_id = imgb.id.substr(8,2);
			var mpp = $('mpp').value;			
			$('img-preview' + color_id).innerHTML = '<img src="' + mpp + img_name + '" />';
		});	
	$$('.add-img').addEvent('click', function(event){
		var mpp = $('mpp').value;		
		var color_id = this.rel;
		var img_select = $$('#imgs-box' + color_id + ' select').getSelected();
		var img_name = img_select[0].get('text');
		var img_id = img_select[0].get('value');
		var current_val = $('color_imgs' + color_id).value;		
		if(current_val.indexOf(img_id) >= 0) {			
			var c_pos = current_val.indexOf(img_id);			
			if(((c_pos + img_id.length + 1) >= current_val.length) && (current_val[c_pos-1]==',')) {
				alert('image has existed in list');
				return false;
			}		
			if(current_val[c_pos + img_id.length + 1]==',' && ((current_val[c_pos-1]==',') || (c_pos==0))) {
				alert('image has existed in list');
				return false;
			}
			if(current_val==img_id) {
				alert('image has existed in list');
				return false;
			}		
			
		}	
		var html = '<a class="hasTip" title="&lt;img src=&quot;' + mpp + img_name + '&quot; /&gt;">' + img_name + '</a><a class="delete-img" rel="' + img_id + '"></a>';
		
		var imgs_list = $('imgs-list' + color_id);
		
		var new_img = new Element('li', {
			 'html': html
			});
		new_img.injectInside(imgs_list);
		
		var myTips = new Tips($$('.hasTip'), {
			maxTitleChars: 50 
		});
		
		if(current_val.length > 0)
			new_val = current_val + ',' + img_id;
		else 	
			new_val = img_id;
		$('color_imgs' + color_id).value = new_val;
			
		delete_action();
	});
	delete_action();
	
});