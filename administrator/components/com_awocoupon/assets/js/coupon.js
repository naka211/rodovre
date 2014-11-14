/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 

function close_panes() {
    if( typeof Joomla != 'undefined' ){
		//$j('#extra_options .panel h3.pane-toggler-down').removeClass('pane-toggler-down').addClass('pane-toggler'); // j17
		//$j('#extra_options .panel div.pane-down').removeClass('pane-down').addClass('pane-hide').css({'height':'0'}); // j17
		jQuery("#extra_options .panel h3").removeClass("pane-toggler-down").addClass("pane-toggler");
		jQuery("#extra_options .panel div.pane-slider").hide();
	} else {
		//$j('#extra_options .panel h3.jpane-toggler-down').removeClass('jpane-toggler-down').addClass('jpane-toggler'); // j15
		//$j('#extra_options .panel div.jpane-slider').removeClass('pane-down').addClass('pane-hide').css({'height':'0'}); // j15
		jQuery("#extra_options .panel h3").removeClass("jpane-toggler-down").addClass("jpane-toggler");
		jQuery("#extra_options .panel div.mypane-slider").hide();
	}
}
function open_pane(pane) {	
    if( typeof Joomla != 'undefined' ){
		//$j('#'+pane).removeClass('pane-toggler').addClass('pane-toggler-down'); // j17
		//$j('#'+pane).parent().find('div.pane-slider').removeClass('pane-hide').addClass('pane-down').css({'height':'auto'}); // j17
		jQuery('#'+pane).removeClass("pane-toggler").addClass("pane-toggler-down");
		jQuery('#'+pane).parent().find(".pane-slider").show().css({"height":"auto"});
	} else { 
		//$j('#'+pane).removeClass('jpane-toggler').addClass('jpane-toggler-down'); // j15
		//$j('#'+pane).parent().find('div.jpane-slider').removeClass('pane-hide').addClass('pane-down').css({'height':'auto'}); // j15
		
		jQuery('#'+pane).removeClass("jpane-toggler").addClass("jpane-toggler-down");
		jQuery('#'+pane).parent().find(".mypane-slider").show().css({"height":"auto"});

	}
	
}

function user_type_change(is_edit) {
	var form = document.adminForm;
	is_edit = (is_edit == undefined) ? false : is_edit;
	
	if(!is_edit) { var tbl = document.getElementById('tbl_users'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);} }
	form.user_name.value = '';
	form.user_id.value = '';
	view_some('user');

	getjqdd('user_search','user_id','ajax_elements',form.user_type.value,base_url,undefined,'btn_user_search');

}
function funtion_type_change(is_edit) {
	var form = document.adminForm;
	
	is_edit = (is_edit == undefined) ? false : is_edit;
	if(!is_edit) resetall();
	else hideall();
	
	if(form.function_type.value == '');
	else if(form.function_type.value == 'giftcert') {
		$j('#tr_coupon_code,#tr_published,#tr_coupon_value,#fs_optionals,#tr_startdate,#tr_expiration,#tr_exclude_giftcert').show();
		
		$j('#pn_asset span, #title_assets').html(str_asset);
		$j('#pn_asset').parent().show();
		$j('#span_asset1_type').show();
		open_pane('pn_asset');
		asset_type_change(1,is_edit);
		
		
		form.asset2_function_type.value = '';
		$j('#span_asset2_type').hide();
		asset_type_change(2,is_edit);
		$j('#pn_asset2 span, #title_assets').html(str_shipping);
		$j('#pn_asset2').parent().show();
		getjqdd('asset_search2','asset_id2','ajax_elements','shipping',base_url,undefined,'btn_asset_search2');
		

	}
	else funtion_type2_change(is_edit);

}
function funtion_type2_change(is_edit) {
	var form = document.adminForm;
	is_edit = (is_edit == undefined) ? false : is_edit;
	
	if(!is_edit) { 
		view_some('asset');
		var tbl = document.getElementById('tbl_assets'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);} 
		var tbl = document.getElementById('tbl_assets2'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);} 
	}
	form.asset_name.value = '';
	form.asset_id.value = '';
	close_panes();
	$j('#pn_asset2').parent().hide();

	if(form.function_type.value == 'parent') {
		form.asset1_function_type.value = 'coupon';
		$j('#span_asset1_type').hide();
		asset_type_change(1,is_edit);
	
		$j('#tr_discount_type,#tr_or,#tr_coupon_value_def,#tr_exclude_special,'+
		   '#tr_exclude_giftcert,#tr_product_match,#tr_addtocart,#tr_coupon_value_type,#tr_coupon_value,'+
		   '#fs_users,#tr_f2_mode,#td_userfields,#div_asset_qty,#div_asset2_qty,#tr_buy_xy_type,#tr_max_discount_qty').hide();
		$j('#pn_user').parent().hide();

		$j('#pn_asset span, #title_assets').html(str_coupons);
		$j('#fs_assets,#tr_coupon_code,#tr_published,#tr_parent_type,#tr_num_of_uses,#fs_optionals,#tr_startdate,#tr_expiration').show();
		$j('#pn_asset').parent().show();
		
		open_pane('pn_asset');
	} 			
	else {
		$j('#tr_discount_type,#tr_or,#tr_coupon_value_def,#tr_exclude_special,#tr_exclude_giftcert,#tr_product_match,#tr_addtocart,#tr_parent_type,#div_asset_qty,#div_asset2_qty,#tr_buy_xy_type,#tr_max_discount_qty').hide()

		$j('#pn_asset span, #title_assets').html(str_asset);
		$j('#tr_coupon_code,#tr_published,#tr_discount_type,#tr_coupon_value_type,'+
		   '#tr_coupon_value,#fs_optionals,#tr_num_of_uses,#tr_min_value,#tr_startdate,'+
		   '#tr_expiration,#fs_users,#fs_assets,#tr_f2_mode,#td_userfields,#span_asset2_type,#span_asset1_type').show();
		$j('#pn_user').parent().show();
		$j('#pn_asset').parent().show();
		
		if(form.function_type.value == 'buy_x_get_y') {
			$j('#pn_asset span, #title_assets').html(str_buy_x);
			$j('#pn_asset2 span, #title_assets').html(str_get_y);
			$j('#pn_asset2').parent().show();
			asset_type_change(1,is_edit);
			asset_type_change(2,is_edit);
				
			$j('#tr_discount_type,#tr_or,#tr_coupon_value_def,#tr_exclude_special,#tr_exclude_giftcert,#tr_parent_type').hide();
			$j('#div_asset_qty,#div_asset2_qty,#tr_buy_xy_type,#tr_max_discount_qty,#tr_product_match,#tr_addtocart').show();

			open_pane('pn_asset2');
		}
		else if(form.function_type.value == 'shipping') {
			//form.discount_type.selectedIndex = 0;
			//$j('#adminForm select[name=discount_type]').children('option[value=buy_x_get_y]').attr('disabled', true);
			
			form.asset1_function_type.value = 'shipping';
			$j('#span_asset1_type').hide();
			asset_type_change(1,is_edit);
			$j('#pn_asset span, #title_assets').html(str_shipping);

			form.asset2_function_type.value = 'product';
			$j('#span_asset2_type').hide();
			asset_type_change(2,is_edit);
			
			$j('#pn_asset2 span, #title_assets').html(str_products);
			$j('#pn_asset2').parent().show();
			getjqdd('asset_search2','asset_id2','ajax_elements','product',base_url,undefined,'btn_asset_search2');
		}
		else {
			asset_type_change(1,is_edit);
			
			//$j('#adminForm select[name=discount_type]').children('option[value=buy_x_get_y]').removeAttr('disabled');
			$j('#tr_or,#tr_coupon_value_def,#tr_exclude_special,#tr_exclude_giftcert').show();
		}
		
			

		open_pane('pn_asset');

	}
}

function asset_type_change(intype,is_edit) {
	var form = document.adminForm;
	
	is_edit = (is_edit == undefined) ? false : is_edit;
	
	if(intype==1) {
		if(form.function_type.value=='shipping') val = 'shipping';
		else if(form.function_type.value=='parent') val = 'parent';
		else val = form.asset1_function_type.value;		
		if(val=='') $j('#div_asset1_inner').hide();
		else {
			if(!is_edit) { var tbl = document.getElementById('tbl_assets'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);}  }
			view_some('asset');
			getjqdd('asset_search','asset_id','ajax_elements',val,base_url,undefined,'btn_asset_search');
			$j('#div_asset1_inner').show();
		}
	
	}
	else if(intype==2) {
		val = form.asset2_function_type.value;
		if(form.function_type.value=='giftcert') val = 'shipping';
		
		if(val=='') $j('#div_asset2_inner').hide();
		else {
			if(!is_edit) { var tbl = document.getElementById('tbl_assets2'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);}  }
			view_some('asset2');
			getjqdd('asset_search2','asset_id2','ajax_elements',val,base_url,undefined,'btn_asset_search2');
			$j('#div_asset2_inner').show();
		}
	}
	
	
	

}


function resetall() {
	var form = document.adminForm;
	form.coupon_code.value = '';
	form.parent_type.value = '';
	form.published.selectedIndex = 0; 
	form.coupon_value_type.selectedIndex = 0;
	form.discount_type.selectedIndex = 0;
	form.coupon_value.value = '';
	form.coupon_value_def.value = '';
	form.num_of_uses.value = '';
	form.min_value.value = '';
	
	form.startdate_date.value = '';
	form.expiration_date.value = '';
	blur_help_text(form.startdate_date,'YYYY-MM-DD');
	blur_help_text(form.expiration_date,'YYYY-MM-DD');
	blur_help_text(form.startdate_time,'hh:mm:ss');
	blur_help_text(form.expiration_time,'hh:mm:ss');
	
	//form.elements['_userlist'].selectedIndex = -1;
	var tbl = document.getElementById('tbl_users'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);}
	
	//form.elements['_couponlist'].selectedIndex = -1;
	var tbl = document.getElementById('tbl_assets'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);}
	form.asset_name.value = '';
	form.asset_id.value = '';

	var tbl = document.getElementById('tbl_assets2'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);}
	form.asset_name2.value = '';
	form.asset_id2.value = '';

	
	form.elements['user_type'].selectedIndex = 0;
	user_type_change();
	
	hideall();
}
function hideall() {
	$j('#tr_coupon_code,#tr_published,#tr_parent_type,#tr_coupon_value_type,'+
	   '#tr_discount_type,#tr_coupon_value,#tr_or,#tr_coupon_value_def,#tr_exclude_special,'+
	   '#tr_exclude_giftcert,#tr_product_match,#tr_addtocart,#fs_optionals,#tr_num_of_uses,#tr_min_value,#tr_startdate,'+
	   '#tr_expiration,#fs_users,#fs_assets,#tr_buy_xy_type,#tr_max_discount_qty').hide()
	$j('#pn_user').parent().hide();
	$j('#pn_asset').parent().hide();
	$j('#pn_asset2').parent().hide();
}

function view_all(type) {
	form = document.adminForm;

	if(type=='asset') {
		if(form.function_type.value=='shipping') field = 'shipping';
		else if(form.function_type.value=='parent') field = 'parent';
		else field = form.asset1_function_type.value;
		sel = form._assetlist; 
	} else if(type=='asset2') {
		field = form.function_type.value=='giftcert' ? 'shipping' : form.asset2_function_type.value;
		sel = form._assetlist2; 
	} else if(type=='user') {
		field = form.user_type.value;
		sel = form._userlist; 
	} else return;

	if(field== '') return;
    if( typeof Joomla == 'undefined' ){
		$j('#pn_'+type).parent().find('div.jpane-slider').css({'height':'auto'}); // j15
	}
	
	$j('#div_'+type+'_simple_table').hide();
	$j('#div_'+type+'_advanced_table').show();
	$j('#div_'+type+'_advanced_grid').hide();
	
	jQuery('#img_'+type+'_simple_link,#img_'+type+'_advanced_link,#img_'+type+'_grid_link').removeClass("c_table_select");
	jQuery('#img_'+type+'_advanced_link').addClass("c_table_select");
	
	$j.getJSON( 
		base_url, 
		{option:my_option, task:'ajax_elements_all', type:field, tmpl:'component', no_html:1}, 
		function(data) {
//alert(JSON.stringify(data));
			i=0;
			sel.options.length=0;
			$j.each(data, function(key, val) {
				sel.options[i++] = new Option(val.label,val.id);
			})
		}
	);
	
}




function grid_view_all(type) {
	form = document.adminForm;
	
	grid_type = '';
	if(type=='asset') {
		if(form.function_type.value=='parent') grid_type = 'parent';
		else if(form.function_type.value=='shipping') grid_type = 'shipping';
		else if(form.asset1_function_type.value=='product') grid_type = 'product';
		else if(form.asset1_function_type.value=='category') grid_type = 'category';
		else if(form.asset1_function_type.value=='manufacturer') grid_type = 'manufacturer';
		else if(form.asset1_function_type.value=='vendor') grid_type = 'vendor';
		else if(form.asset1_function_type.value=='shipping') grid_type = 'shipping';
	}
	else if(type=='asset2') {
		if(form.function_type.value=='giftcert') grid_type = 'shipping';
		else if(form.function_type.value=='shipping') grid_type = 'product';
		else if(form.asset2_function_type.value=='product') grid_type = 'product';
		else if(form.asset2_function_type.value=='category') grid_type = 'category';
		else if(form.asset2_function_type.value=='manufacturer') grid_type = 'manufacturer';
		else if(form.asset2_function_type.value=='vendor') grid_type = 'vendor';
		else if(form.asset2_function_type.value=='shipping') grid_type = 'shipping';
	}
	else if(type=='user') {
		if(form.user_type.value=='user') grid_type = 'user';
		else if(form.user_type.value=='usergroup') grid_type = 'usergroup';
	}

	if(grid_type!='') {
		$j('#div_'+type+'_simple_table').hide();
		$j('#div_'+type+'_advanced_table').hide();
		$j('#div_'+type+'_advanced_grid').show();

		jQuery('#img_'+type+'_simple_link,#img_'+type+'_advanced_link,#img_'+type+'_grid_link').removeClass("c_table_select");
		jQuery('#img_'+type+'_grid_link').addClass("c_table_select");

		var cols = {
			id: "ID",
			label: str_name
		};
		if( grid_type=='product' ) {
			cols['sku'] = "SKU";
			cols['product_name'] = str_product;
		}	
		else if( grid_type=='category' ) {}	
		else if( grid_type=='manufacturer' ) {}	
		else if( grid_type=='vendor' ) {}	
		else if( grid_type=='shipping' ) {
			cols['carrier'] = str_shipping_module;
		}	
		
		else if( grid_type=='user') {
			cols['username']=str_username;
			cols['lastname']=str_last_name;
			cols['firstname']=str_first_name;
		}
		else if( grid_type=='usergroup') {}
		else if( grid_type=='parent') {}
	
		options = {
			ajax_url:base_url,
			selection_function: 'grid_add_selection',
			selection_params: {type:type},
			param_task:'ajax_elements_grid',
			param_type:grid_type,
			columns: cols,
			lang_pq_grid: {
				strLoading: "Loading",
				strNothingFound: "Nothing found"
			},
			lang_pq_pager: {
				strPage:str_pq_pageof,
				strFirstPage:"",
				strPrevPage:"",
				strNextPage:"",
				strLastPage:"",
				strRefresh:"Refresh",	
				strRpp:str_pq_displaynum,
				strDisplay:""	
			}
			
			
		}
		
		getjqgrid(type+'_search_grid',base_url,options);
		
	}
		
	return;
	
	

	if(type=='asset') {
		if(form.function_type.value=='shipping') field = 'shipping';
		else if(form.function_type.value=='parent') field = 'parent';
		else field = form.asset1_function_type.value;
		sel = form._assetlist; 
	} else if(type=='asset2') {
		field = form.function_type.value=='giftcert' ? 'shipping' : form.asset2_function_type.value;
		sel = form._assetlist2; 
	} else if(type=='user') {
		field = form.user_type.value;
		sel = form._userlist; 
	} else return;

	if(field== '') return;
    if( typeof Joomla == 'undefined' ){
		$j('#pn_'+type).parent().find('div.jpane-slider').css({'height':'auto'}); // j15
	}
	
	$j('#div_'+type+'_simple_table').hide();
	$j('#div_'+type+'_advanced_table').show();
	$j('#div_'+type+'_advanced_grid').hide();
	
	
	$j.getJSON( 
		base_url, 
		{option:my_option, task:'ajax_elements_all', type:field, tmpl:'component', no_html:1}, 
		function(data) {
//alert(JSON.stringify(data));
			i=0;
			sel.options.length=0;
			$j.each(data, function(key, val) {
				sel.options[i++] = new Option(val.label,val.id);
			})
		}
	);
	
}

function grid_add_selection(id,label,options) {
	var type = '';
	if(options['type']!=undefined) type = options['type'];

	form = document.adminForm;
	if(type == 'asset') {
		value_list_id = 'assetlist[]';
		tbl = 'tbl_assets';
	} else if(type == 'asset2') {
		value_list_id = 'assetlist2[]';
		tbl = 'tbl_assets2';
	} else if(type== 'user') {
		value_list_id = 'userlist[]';
		tbl = 'tbl_users';
	} else return;
	
	// set coupon to specific if new and assets are selected
	if(form.function_type.value!='shipping' && form.elements[value_list_id]==undefined && tbl=='tbl_assets') form.discount_type.selectedIndex = 1;
	

	// do not add duplicates
	valueDD = form.elements[value_list_id];
	if(valueDD!=undefined) {
		if(valueDD.value != undefined && valueDD.value==id) return;
		else {
			is_continue = false;
			for(j=0,len2=valueDD.length; j<len2; j++) if(valueDD[j].value==id) {is_continue = true; break;}
			if(is_continue) return;
		}
	}

	// add body
	$j('#'+tbl+' > tbody:last').append(
		'<tr id="'+tbl+'_tr'+id+'">\
			<td>'+id+'</td>\
			<td>'+label+'</td>\
			<td class="last" align="right">'+
					(form.function_type.value=='parent' ? '<button type="button" onclick="moverow(\''+tbl+'_tr'+id+'\',\'up\');" >&#8593;</button>'+
									  '<button type="button" onclick="moverow(\''+tbl+'_tr'+id+'\',\'down\');" >&#8595;</button>&nbsp; ' : '')+
					'<button type="button" onclick="deleterow(\''+tbl+'_tr'+id+'\');return false;" >X</button>\
					<input type="hidden" name="'+value_list_id+'" value="'+id+'"></td>\
					<input type="hidden" name="'+type+'namelist['+id+']" value="'+label+'">\
		</tr>'
	); 
	
	
}



function view_some(type) {
	$j('#div_'+type+'_advanced_table').hide();
	$j('#div_'+type+'_simple_table').show();
	$j('#div_'+type+'_advanced_grid').hide();
	
	jQuery('#img_'+type+'_simple_link,#img_'+type+'_advanced_link,#img_'+type+'_grid_link').removeClass("c_table_select");
	jQuery('#img_'+type+'_simple_link').addClass("c_table_select");

}

function dd_itemselectf(type) {

	form = document.adminForm;
	if(type=='asset') {
		id = form.asset_id.value;
		name = form.asset_name.value;
		value_list_id = 'assetlist[]';
		value_list_name = 'assetnamelist['+id+']';
		tbl = 'tbl_assets';
	} else if(type=='asset2') {
		id = form.asset_id2.value;
		name = form.asset_name2.value;
		value_list_id = 'assetlist2[]';
		value_list_name = 'assetnamelist2['+id+']';
		tbl = 'tbl_assets2';
	} else if(type == 'user') {
		id = form.user_id.value;
		name = form.user_name.value;
		value_list_id = 'userlist[]';
		value_list_name = 'usernamelist['+id+']';
		tbl = 'tbl_users';
	} else return;

	// set coupon to specific if new and assets are selected
	if(form.function_type.value!='shipping' && form.id.value=='' && form.elements[value_list_id]==undefined && tbl=='tbl_assets') form.discount_type.selectedIndex = 1;
	if(trim(id)!='') {

		// do not add duplicates
		valueDD = form.elements[value_list_id];
		if(valueDD!=undefined) {
			if(valueDD.value != undefined && valueDD.value==id) return;
			else {
				is_continue = false;
				for(j=0,len2=valueDD.length; j<len2; j++) if(valueDD[j].value==id) {is_continue = true; break;}
				if(is_continue) return;
			}
		}

		// add body
		$j('#'+tbl+' > tbody:last').append(
			'<tr id="'+tbl+'_tr'+id+'">'+
				'<td>'+id+'</td>'+
				'<td>'+name+'</td>'+
				'<td class="last" align="right">'+
						(form.function_type.value=='parent' ? '<button type="button" onclick="moverow(\''+tbl+'_tr'+id+'\',\'up\');" >&#8593;</button>'+
										  '<button type="button" onclick="moverow(\''+tbl+'_tr'+id+'\',\'down\');" >&#8595;</button>&nbsp; ' : '')+
						'<button type="button" onclick="deleterow(\''+tbl+'_tr'+id+'\');return false;" >X</button>'+
						'<input type="hidden" name="'+value_list_id+'" value="'+id+'">'+
						'<input type="hidden" name="'+value_list_name+'" value="'+name+'">'+
				'</td>'+
			'</tr>'
		); 
	}
	
	
}
function dd_itemselectg(type) {
	form = document.adminForm;
	if(type == 'asset') {
		value_list_id = 'assetlist[]';
		tbl = 'tbl_assets';
		searchDD = form.elements['_assetlist'];
	} else if(type == 'asset2') {
		value_list_id = 'assetlist2[]';
		tbl = 'tbl_assets2';
		searchDD = form.elements['_assetlist2'];
	} else if(type== 'user') {
		value_list_id = 'userlist[]';
		tbl = 'tbl_users';
		searchDD = form.elements['_userlist'];
	} else return;
	
	// set coupon to specific if new and assets are selected
	if(form.function_type.value!='shipping' && form.elements[value_list_id]==undefined && tbl=='tbl_assets') form.discount_type.selectedIndex = 1;
	
	for(var i=0, len=searchDD.options.length;i<len;i++) {
		if(searchDD.options[i].selected) {
			id = searchDD.options[i].value;
			if(trim(id)=='') continue;

			name = searchDD.options[i].innerHTML;
	
			// do not add duplicates
			valueDD = form.elements[value_list_id];
			if(valueDD!=undefined) {
				if(valueDD.value != undefined && valueDD.value==id) continue;
				else {
					is_continue = false;
					for(j=0,len2=valueDD.length; j<len2; j++) if(valueDD[j].value==id) {is_continue = true; break;}
					if(is_continue) continue;
				}
			}
			// add body
			$j('#'+tbl+' > tbody:last').append(
				'<tr id="'+tbl+'_tr'+id+'">'+
					'<td>'+id+'</td>'+
					'<td>'+name+'</td>'+
					'<td class="last" align="right">'+
							(form.function_type.value=='parent' ? '<button type="button" onclick="moverow(\''+tbl+'_tr'+id+'\',\'up\');" >&#8593;</button>'+
											  '<button type="button" onclick="moverow(\''+tbl+'_tr'+id+'\',\'down\');" >&#8595;</button>&nbsp; ' : '')+
							'<button type="button" onclick="deleterow(\''+tbl+'_tr'+id+'\');return false;" >X</button>'+
							'<input type="hidden" name="'+value_list_id+'" value="'+id+'"></td>'+
							'<input type="hidden" name="'+type+'namelist['+id+']" value="'+name+'">'+
				'</tr>'
			); 
		}
	}
	
	
}
function dd_searchg(type) {
	if(type=='asset') {
		var input_text = 'asset_search_txt';
		var searchDD = document.adminForm.elements['_assetlist'];
	} else if(type=='asset2') {
		var input_text = 'asset_search_txt2';
		var searchDD = document.adminForm.elements['_assetlist2'];
	} else if(type=='user') {
		var input_text = 'user_search_txt';
		var searchDD = document.adminForm.elements['_userlist'];
	} else return;
	
	//searchDD.multiple = false;
	var input=document.getElementById(input_text).value.toLowerCase();
	if(trim(input)=='') { searchDD.selectedIndex = -1; return; }
	
	searchDD.selectedIndex = -1;
	var output = searchDD.options;
	for(var i=0, len=output.length;i<len;i++) { if(output[i].text.toLowerCase().indexOf(input)==0){ output[i].selected=true; break; } }
	
	//searchDD.multiple = true;
	
}


function deleterow(id) { var tr = document.getElementById(id); tr.parentNode.removeChild(tr); }
function moverow(id,direction) {

	var tr = document.getElementById(id);
	var tbl = tr.parentNode;
	
	clickedRowIndex = 0;
	for(i=0; i<tbl.rows.length; i++) { if(tbl.rows[i].id == id) { clickedRowIndex = i; break; } }

	if(direction=='up' && clickedRowIndex <=0) return false;
	else if(direction=='down' && clickedRowIndex ==(tbl.rows.length-1)) return false;

	if(direction=='up') adjacentRowIndex = clickedRowIndex-1;
	else if(direction=='down')  adjacentRowIndex = clickedRowIndex+1;
	else return;

	clickedrow=tbl.getElementsByTagName('tr')[clickedRowIndex];
	adjacentrow= tbl.getElementsByTagName('tr')[adjacentRowIndex];
 
	clickedrow_clone=clickedrow.cloneNode(true);
	adjacentrow_clone=adjacentrow.cloneNode(true);

	adjacentrow = tbl.replaceChild(clickedrow_clone,adjacentrow);
	clickedrow = tbl.replaceChild(adjacentrow_clone,clickedrow);
 
} 







function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancelcoupon') {
		submitform( pressbutton );
		return;
	}

	else if (pressbutton == 'savecoupon' || pressbutton=='applycoupon') {
		var err = '';
		if(trim(form.coupon_code.value)=='') err += '\n'+str_coup_err_valid_code;
		if(form.user_type.value!='user' && form.user_type.value!='usergroup') err += '\n'+str_coup_err_valid_usertype;

		if(form.function_type.value == 'giftcert') {
			if(trim(form.coupon_value.value)=='' || isNaN(form.coupon_value.value) || form.coupon_value.value<0.01) err += '\n'+str_coup_err_valid_value; 
		} 
		else if(form.function_type.value == 'parent') {
			if(form.parent_type.value=='' || (form.parent_type.value!='first' && form.parent_type.value!='all' && form.parent_type.value!='allonly' && form.parent_type.value!='lowest' && form.parent_type.value!='highest'))
				err += '\n'+str_parent_type+': '+str_selection_error;
			if(form.elements['assetlist[]']==undefined) err += '\n'+str_coupon+': '+str_selection_error;
		} 
		else {
			if(trim(form.coupon_value_type.value)=='') err += '\n'+str_coup_err_value_type; 

			if(form.function_type.value == 'coupon') {
				if(form.coupon_value.value!='' && (isNaN(form.coupon_value.value) || form.coupon_value.value<0)) err += '\n'+str_coup_err_valid_value;
				if(form.coupon_value_def.value!='' && !/^(\d+\-\d+([.]\d+)?;)+(\[[_a-z]+\=[a-z]+(\&[_a-z]+\=[a-z]+)*\])?$/.test(form.coupon_value_def.value)) err += '\n'+str_coup_err_valid_def;
				if(trim(form.coupon_value.value)==''  && trim(form.coupon_value_def.value)=='') err += '\n'+str_coup_err_valid_value;
				if(form.discount_type.value=='' || (form.discount_type.value!='specific' && form.discount_type.value!='overall')) err += '\n'+str_coup_err_valid_discount;
				myasset = form.elements['assetlist[]'];
				myradio = form.asset1_mode;
				if(trim(form.discount_type.value)=='specific' && myasset==undefined) err += '\n'+str_selection_error;
				
				if(myasset!=undefined) {
					is_checked = false;
					for(var i=0,len=myradio.length; i < len; i++) {
						if(myradio[i].checked) {
							is_checked = true;
							break;
						}
					}
					if(!is_checked) err += '\n'+str_coup_err_choose_inclexcl;
				}
			} 
			else if(form.function_type.value == 'shipping') {
				if(trim(form.coupon_value.value)=='' || isNaN(form.coupon_value.value) || form.coupon_value.value<0) err += '\n'+str_coup_err_valid_value; 
				if(form.elements['assetlist[]']!=undefined) {
					is_checked = false;
					for(var i=0,len=form.asset1_mode.length; i < len; i++) {
						if(form.asset1_mode[i].checked) {
							is_checked = true;
							break;
						}
					}
					if(!is_checked) err += '\n'+str_coup_err_choose_inclexcl;
					
					if(form.elements['assetlist2[]']!=undefined) {
						is_checked = false;
						for(var i=0,len=form.asset2_mode.length; i < len; i++) {
							if(form.asset2_mode[i].checked) {
								is_checked = true;
								break;
							}
						}
						if(!is_checked) err += '\n'+str_coup_err_choose_inclexcl;
					}
				}
				
			}
			else if(form.function_type.value == 'buy_x_get_y') {
			
				if(isNaN(form.coupon_value.value) || form.coupon_value.value<0) err += '\n'+str_coup_err_valid_value;
				if(form.buy_xy_process_type.value=='' || (form.buy_xy_process_type.value!='first' && form.buy_xy_process_type.value!='lowest' && form.buy_xy_process_type.value!='highest'))
					err += '\n'+str_parent_type+': '+str_selection_error;
					
				if(trim(form.max_discount_qty.value)!='' && !isUnsignedInteger(form.max_discount_qty.value)) err += '\n'+str_coup_err_discount_qty;

				if(form.elements['assetlist[]']==undefined) err += '\n'+str_buy_x+': '+str_selection_error;
				if(form.elements['assetlist2[]']==undefined) err += '\n'+str_get_y+': '+str_selection_error;									
				
				if(!isUnsignedInteger(form.asset1_qty.value)) err += '\n'+str_buy_x+' -> '+str_number+': '+str_coup_err_valid_input;
				if(!isUnsignedInteger(form.asset2_qty.value)) err += '\n'+str_get_y+' -> '+str_number+': '+str_coup_err_valid_input;
			}
			else err += '\n'+str_coup_err_invalid;
				
			if(form.num_of_uses_type.value!='' && form.num_of_uses_type.value!='total' && form.num_of_uses_type.value!='per_user') err += '\n'+str_coup_err_valid_uses_type;
			if(trim(form.num_of_uses.value)!='' && !isUnsignedInteger(form.num_of_uses.value)) err += '\n'+str_coup_err_valid_uses;
			if( (form.num_of_uses_type.value!='' && trim(form.num_of_uses.value)=='') || (form.num_of_uses_type.value=='' && trim(form.num_of_uses.value)!='')) err += '\n'+str_coup_err_valid_uses;
			if(trim(form.min_value.value)!='' && form.min_value.value!=0 && (isNaN(form.min_value.value) || form.min_value.value<0.01)) err += '\n'+str_coup_err_valid_min; 
		}
		
			
		is_start = true;
		if(form.startdate_date.value=='YYYY-MM-DD') form.startdate_date.value = '';
		if(form.startdate_time.value=='hh:mm:ss') form.startdate_time.value = '';
		if(form.startdate_date.value=='' && form.startdate_time.value!='') {
			is_start = false;
			err += '\n'+str_startdate+': '+str_coup_err_valid_input;
		} else if(form.startdate_date.value!='') {
			if(!/^\d{4}\-\d{2}\-\d{2}$/.test(form.startdate_date.value)) {
				is_start = false;
				err += '\n'+str_startdate+': '+str_coup_err_valid_input;
			} else {
				yyyy = form.startdate_date.value.substr(0,4);
				mm = form.startdate_date.value.substr(5,2);
				dd = form.startdate_date.value.substr(8,2);
				if(yyyy>2100 || mm>12 || dd>31) {
					is_start = false;
					err += '\n'+str_startdate+': '+str_coup_err_valid_input;
				}
			}
			if(is_start && form.startdate_time.value!='' && !/^\d{2}\:\d{2}\:\d{2}$/.test(form.startdate_time.value)) {
				is_start = false;
				err += '\n'+str_startdate+': '+str_coup_err_valid_input;
			} else {
				hh = form.startdate_time.value.substr(0,2);
				mm = form.startdate_time.value.substr(3,2);
				ss = form.startdate_time.value.substr(6,2);
				if(hh>23 || mm>59 || ss>59) {
					is_start = false;
					err += '\n'+str_startdate+': '+str_coup_err_valid_input;
				}
			}
		} else is_start = false;


		is_end = true;
		if(form.expiration_date.value=='YYYY-MM-DD') form.expiration_date.value = '';
		if(form.expiration_time.value=='hh:mm:ss') form.expiration_time.value = '';
		if(form.expiration_date.value=='' && form.expiration_time.value!='') {
			is_end = false;
			err += '\n'+str_coup_err_valid_expiration;
		} else if(form.expiration_date.value!='') {
			if(!/^\d{4}\-\d{2}\-\d{2}$/.test(form.expiration_date.value)) {
				is_end = false;
				err += '\n'+str_coup_err_valid_expiration;
			} else {
				yyyy = form.expiration_date.value.substr(0,4);
				mm = form.expiration_date.value.substr(5,2);
				dd = form.expiration_date.value.substr(8,2);
				if(yyyy>2100 || mm>12 || dd>31) {
					is_end = false;
					err += '\n'+str_coup_err_valid_expiration;
				}
			}
			if(is_end && form.expiration_time.value!='' && !/^\d{2}\:\d{2}\:\d{2}$/.test(form.expiration_time.value)) {
				is_end = false;
				err += '\n'+str_coup_err_valid_expiration;
			} else {
				hh = form.expiration_time.value.substr(0,2);
				mm = form.expiration_time.value.substr(3,2);
				ss = form.expiration_time.value.substr(6,2);
				if(hh>23 || mm>59 || ss>59) {
					is_end = false;
					err += '\n'+str_coup_err_valid_expiration;
				}
			}
		} else is_end = false;
		if(is_start && is_end) {
			c1 = (form.startdate_date.value.substr(0,4)+form.startdate_date.value.substr(5,2)+form.startdate_date.value.substr(8,2)+'.'
					+form.startdate_time.value.substr(0,2)+form.startdate_time.value.substr(3,2)+form.startdate_time.value.substr(6,2))*1;
			c2 = (form.expiration_date.value.substr(0,4)+form.expiration_date.value.substr(5,2)+form.expiration_date.value.substr(8,2)+'.'
					+(form.expiration_time.value=='' ? '235959' : form.expiration_time.value.substr(0,2)+form.expiration_time.value.substr(3,2)+form.expiration_time.value.substr(6,2)))*1;
			if(c1>c2) err += '\n'+str_startdate+'/'+str_expiration+': '+str_coup_err_valid_input;
		}

		if(form.published.value=='' || (form.published.value!='1' && form.published.value!='-1' && form.published.value!='-2')) err += '\n'+str_coup_err_valid_publish;

		if(err != '') {
			blur_help_text(form.startdate_date,'YYYY-MM-DD');
			blur_help_text(form.expiration_date,'YYYY-MM-DD');
			blur_help_text(form.startdate_time,'hh:mm:ss');
			blur_help_text(form.expiration_time,'hh:mm:ss');
			alert(err);
		}
		else {
			var is_submit = true;
			if(trim(form.expiration_date.value)!='') {
				d = form.expiration_date.value.substr(0,4)+form.expiration_date.value.substr(5,2)+form.expiration_date.value.substr(8,2);
				d = d*1;
				if(d < str_coup_date) {
					if(!confirm(str_coup_err_confirm_expiration)) is_submit = false;
				}
			}
			if(is_submit) submitform( pressbutton );
		}
		return; 
	}

}

function focus_help_text(item,txt) {
	if(item.value == txt) {
		item.style.color="#000000";
		item.value = '';
	}
}
function blur_help_text(item,txt) {
	if(item.value=='' || item.value == txt) {
		item.style.color="#aaaaaa";
		item.value=txt;
	} else item.style.color="#000000";
}

function generate_code(estore) {
	$j.ajax({
		type: "POST",
		url: base_url,
		data: "option="+my_option+"&task=ajax_generate_coupon_code&tmpl=component&no_html=1&estore="+estore,
		success: function( data ) {
			var form = document.adminForm;
			form.coupon_code.value = data;
		}
	});
}

