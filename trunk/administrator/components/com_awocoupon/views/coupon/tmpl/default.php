<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<script language="javascript" type="text/javascript">
<!--
var $j = jQuery.noConflict();  // added so jquery does not conflict with mootools

$j(document).ready(function() {

	var form = document.adminForm;
	funtion_type_change(true);
	user_type_change(true);
	
	blur_help_text(form.startdate_date,'YYYY-MM-DD');
	blur_help_text(form.expiration_date,'YYYY-MM-DD');
	blur_help_text(form.startdate_time,'hh:mm:ss');
	blur_help_text(form.expiration_time,'hh:mm:ss');
	
});


var my_option = "com_awocoupon";
var base_url = "<?php echo JURI::base(true); ?>/index.php";

var str_cum_title = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE_DEFINITION')); ?>';
var str_cum_lbl1 = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PRODUCT_NUM')); ?>';
var str_cum_lbl2 = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE')); ?>';
var str_cum_new = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ADD_ENTRY')); ?>';
var str_cum_subm = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_SUBMIT')); ?>';
var str_cum_qty_type = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_APPLY_DISTINCT_COUNT')); ?>';

var str_coupons = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_COUPONS')); ?>';
var str_product = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PRODUCT')); ?>';
var str_products = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PRODUCTS')); ?>';
var str_categories = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_CATEGORYS')); ?>';
var str_manufacturers = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_MANUFACTURERS')); ?>';
var str_vendors = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VENDORS')); ?>';
var str_shipping = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_SHIPPING')); ?>';


var str_coup_date = <?php echo date('Ymd'); ?>;
var str_coup_err_invalid = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_INVALID')); ?>';
var str_coup_err_valid_code = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_value_type = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_value = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_def = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE_DEFINITION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_discount = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_choose_product = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC')); ?>';
var str_coup_err_choose_category = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC')); ?>';
var str_coup_err_choose_manufacturer = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC')); ?>';
var str_coup_err_choose_vendor = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC')); ?>';
var str_coup_err_choose_inclexcl = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_SELECT_MODE')); ?>';
var str_coup_err_valid_uses_type = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_NUMBER_USES_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_uses = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_NUMBER_USES').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_min = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE_MIN').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_expiration = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_publish = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PUBLISHED').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_confirm_expiration = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_EXPIRATION_PAST')); ?>';
var str_coup_err_valid_usertype = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_USER_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_input = '<?php echo addslashes(JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_discount_qty = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_MAX_DISCOUNT_QTY').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';


var str_startdate = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_DATE_START')); ?>';
var str_expiration = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_EXPIRATION')); ?>';

var str_id = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_ID')); ?>';
var str_name = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_NAME')); ?>';

var str_selection_error = '<?php echo addslashes(JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION')); ?>';
var str_parent_type = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PARENT_TYPE')); ?>';
var str_coupon = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_COUPON')); ?>';
var str_asset = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ASSET')); ?>';

var str_buy_x = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_BUY_X')); ?>';
var str_get_y = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_GET_Y')); ?>';
var str_number = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_NUMBER')); ?>';


var str_pq_displaynum = '<?php echo addslashes(JText::_('JGLOBAL_DISPLAY_NUM')); ?>';
var str_pq_pageof = '<?php $x = preg_replace('/\%s/','{0}',JText::_('JLIB_HTML_PAGE_CURRENT_OF_TOTAL'),1); $x = preg_replace('/\%s/','{1}',$x,1); echo addslashes($x); ?>';

var str_shipping_module = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_SHIPPING_MODULE')); ?>';
var str_last_name = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_LAST_NAME')); ?>';
var str_first_name = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_FIRST_NAME')); ?>';
var str_username = '<?php echo addslashes(JText::_('COM_AWOCOUPON_GBL_USERNAME')); ?>';


//-->
</script>

<form action="index.php" method="post" id="adminForm" name="adminForm" id="link-form" class="form-validate">


	<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_AWOCOUPON_CP_COUPON_DETAILS'); ?></legend>

		<table class="admintable">
			<tr id="tr_function_type" >
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ); ?></label></td>
				<td><?php echo $this->lists['function_type']; ?></td>
			</tr>
			<tr id="tr_coupon_code" style="display:none;">
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></label></td>
				<td><input class="inputbox" type="text" name="coupon_code" size="30" maxlength="255" value="<?php echo $this->row->coupon_code; ?>" />
					<button type="button" onclick="generate_code('<?php echo AWOCOUPON_ESTORE; ?>')"><?php echo JText::_( 'COM_AWOCOUPON_CP_GENERATE_CODE' ); ?></button>
				</td>
			</tr>
			<tr id="tr_published" style="display:none;">
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></label></td>
				<td><?php echo $this->lists['published']; ?></td>
			</tr>
			<tr id="tr_parent_type" style="display:none;">
				<td class="key" nowrap><label><?php
					echo 	JHTML::tooltip(JText::_('COM_AWOCOUPON_CP_PARENT_ALL_WARNING'), $this->AWOCOUPON_lang['parent_type']['all'], 'tooltip.png', '', '', false).
							'&nbsp;'.
							JText::_( 'COM_AWOCOUPON_CP_PARENT_TYPE' ); ?></label></td>
				<td><?php echo $this->lists['parent_type']; ?></td>
			</tr>
			<tr id="tr_coupon_value_type" style="display:none;">
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PERCENT_AMOUNT' ); ?></label></td>
				<td><?php echo $this->lists['coupon_value_type']; ?></td>
			</tr>
			<tr id="tr_discount_type" style="display:none;">
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DISCOUNT_TYPE' ); ?></label></td>
				<td><?php echo $this->lists['discount_type']; ?></td>
			</tr>
			<tr id="tr_coupon_value" style="display:none;">
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></label></td>
				<td><input class="inputbox" type="text" name="coupon_value" size="30" maxlength="255" value="<?php echo $this->row->coupon_value; ?>" /></td>
			</tr>
			<tr id="tr_or" style="display:none;"><td class="key" colspan="2" align="center"><?php echo JText::_( 'COM_AWOCOUPON_GBL_OR' ); ?></td></tr>
			<tr id="tr_coupon_value_def" style="display:none;">
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_DEFINITION' ); ?></label></td>
				<td><input class="inputbox" type="text" name="coupon_value_def" size="26" onfocus="showvaluedefinition();" maxlength="255" value="<?php echo $this->row->coupon_value_def; ?>" /><input type="button" value="..." onclick="showvaluedefinition();"></td>
			</tr>
			<tr id="tr_buy_xy_type" style="display:none;">
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PARENT_TYPE' ); ?></label></td>
				<td><?php echo $this->lists['buy_xy_process_type']; ?></td>
			</tr>
		</table>

		</fieldset>


		<fieldset  id="fs_optionals" class="adminform" style="display:none;">
		<legend><?php echo JText::_('COM_AWOCOUPON_CP_OPTIONAL_FIELDS'); ?></legend>


		<table class="admintable">
			<tr id="tr_num_of_uses" style="display:none;"><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES' ); ?></label></td>
				<td><?php echo $this->lists['num_of_uses_type']; ?>
					<input class="inputbox" type="text" name="num_of_uses" size="2" maxlength="255" value="<?php echo $this->row->num_of_uses; ?>" /> #
				</td>
			</tr>
			<tr id="tr_min_value" style="display:none;"><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_MIN' ); ?></label></td>
				<td><?php echo $this->lists['min_value_type']; ?>
					<input class="inputbox" type="text" name="min_value" size="8" maxlength="255" value="<?php echo $this->row->min_value; ?>" />
				</td>
			</tr>
			<tr id="tr_max_discount_qty" style="display:none;"><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_MAX_DISCOUNT_QTY' ); ?></label></td>
				<td><input class="inputbox" type="text" name="max_discount_qty" size="8" maxlength="255" value="<?php echo $this->row->max_discount_qty; ?>" /></td>
			</tr>
			<tr id="tr_startdate" style="display:none;"><td class="key" nowrap valign="top"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DATE_START' ); ?></label></td>
				<td><?php echo JHTML::calendar($this->row->startdate_date, 'startdate_date', 'startdate_date', '%Y-%m-%d',
									array('size'=>'12',
									'maxlength'=>'10',
									'onfocus'=>'focus_help_text(this,\'YYYY-MM-DD\')',
									'onchange'=>'blur_help_text(this,\'YYYY-MM-DD\')',
								));
					?>
					<input class="inputbox" type="text" name="startdate_time" size="1" style="width:49px;" maxlength="8" value="<?php echo $this->row->startdate_time; ?>"
						onfocus="focus_help_text(this,'hh:mm:ss')" onblur="blur_help_text(this,'hh:mm:ss')"  />
				</td>
			</tr>
			<tr id="tr_expiration" style="display:none;"><td class="key" nowrap valign="top"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ); ?></label></td>
				<td><?php echo JHTML::calendar($this->row->expiration_date, 'expiration_date', 'expiration_date', '%Y-%m-%d',
									array('size'=>'12',
									'maxlength'=>'10',
									'onfocus'=>'focus_help_text(this,\'YYYY-MM-DD\')',
									'onchange'=>'blur_help_text(this,\'YYYY-MM-DD\')',
								));
					?>
					<input class="inputbox" type="text" name="expiration_time" size="1" maxlength="8" value="<?php echo $this->row->expiration_time; ?>" style="width:49px;"
						onfocus="focus_help_text(this,'hh:mm:ss')" onblur="blur_help_text(this,'hh:mm:ss')"  />
				</td>
			</tr>
			
			<tr id="tr_product_match" style="display:none;"><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DONT_MIX_PRODUCTS' ); ?></label></td>
				<td><input class="inputbox" type="checkbox" name="product_match" value="1" <?php if($this->row->product_match==1) echo 'CHECKED'; ?> /></td>
			</tr>
			<tr id="tr_addtocart" style="display:none;"><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_ADDTOCART_GETY' ); ?></label></td>
				<td><input class="inputbox" type="checkbox" name="addtocart" value="1" <?php if($this->row->addtocart==1) echo 'CHECKED'; ?> /></td>
			</tr>
			<tr id="tr_exclude_special" style="display:none;"><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXCLUDE_SPECIAL' ); ?></label></td>
				<td><input class="inputbox" type="checkbox" name="exclude_special" value="1" <?php if($this->row->exclude_special==1) echo 'CHECKED'; ?> /></td>
			</tr>
			<tr id="tr_exclude_giftcert" style="display:none;"><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXCLUDE_GIFTCERT' ); ?></label></td>
				<td><input class="inputbox" type="checkbox" name="exclude_giftcert" value="1" <?php if($this->row->exclude_giftcert==1) echo 'CHECKED'; ?> /></td>
			</tr>
			<tr><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_ADMIN_NOTE' ); ?></label></td>
				<td><textarea cols="18" rows="3" name="note" style="width:147px;"><?php echo $this->row->note; ?></textarea>
			</tr>
		</table>

		</fieldset>
	</div>

	<div class="width-50 fltrt">
		<?php
		//echo JHtml::_('sliders.start','extra_options', array());
		echo awoJHtmlSliders::start('extra_options', array('closeAll'=>1));
		//echo JHtml::_('sliders.panel',JText::_('COM_AWOCOUPON_CP_CUSTOMERS'), 'pn_user');
		echo awoJHtmlSliders::panel(JText::_('COM_AWOCOUPON_CP_CUSTOMERS'), 'pn_user');
		?>


			<div id="div_users" style="padding:10px;">
				<div><?php echo $this->lists['user_type']; ?></div>
				<div style="padding-top:5px;">
					<a href="javascript:view_some('user');"><img id="img_user_simple_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table1.png" class="c_table_select" style="height:22px;" /></a>
					&nbsp; <a href="javascript:view_all('user');"><img id="img_user_advanced_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table2.png" style="height:22px;" /></a>
					&nbsp; <a href="javascript:grid_view_all('user');"><img id="img_user_grid_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table3.png" style="height:22px;" /></a>
				</div>
				<div id="div_user_simple_table">
					<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
					<input class="inputbox" type="text" id="user_search" name="user_name" size="60" maxlength="255" value="" />
					<input type="hidden" name="user_id" value="" />
					<button id="btn_user_search" type="button" onclick="dd_itemselectf('user'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
				</div>
				
				<div id="div_user_advanced_table" style="display:none;">
					<div>
						<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
						<input type="text" id="user_search_txt" size="60" onkeyup="dd_searchg('user')">
						<button onclick="dd_itemselectg('user'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
					</div>
					<select name="_userlist" MULTIPLE class="inputbox" size="2" style="width:100%; height:160px;" ondblclick="dd_itemselectg('user')"></select>
					<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
					<br />
				</div>
				
				<div id="div_user_advanced_grid" style="display:none;">
					<div id="user_search_grid"></div>
					<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
					<br />
				</div>

				<div class="asset_holder">
					<table id="tbl_users" class="adminlist" cellspacing="1">
					<thead><tr><th><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th><th><?php echo JText::_('COM_AWOCOUPON_GBL_NAME'); ?></th><th>&nbsp;</th></tr></thead>
					<tbody>
					<?php  foreach($this->row->userlist as $row) { ?>
						<tr id="tbl_users_tr<?php echo $row->user_id; ?>">
							<td><?php echo $row->user_id; ?></td>
							<td><?php echo $row->user_name; ?></td>
							<td class="last" align="right">
								<button type="button" onclick="deleterow('tbl_users_tr<?php echo $row->user_id; ?>');return false;" >X</button>
								<input type="hidden" name="userlist[]" value="<?php echo $row->user_id; ?>">
								<input type="hidden" name="usernamelist[<?php echo $row->user_id; ?>]" value="<?php echo $row->user_name; ?>"></td>
						</tr>
					<?php } ?>
					</tbody></table>
				</div>
				<div id="tr_u_mode">
					<input type="hidden" name="user_mode" value="" />
					<input type="radio" name="user_mode" value="include" <?php echo empty($this->row->user_mode) || $this->row->user_mode=='include' ? 'CHECKED' : ''; ?> /><?php echo JText::_('COM_AWOCOUPON_CP_INCLUDE'); ?> &nbsp;&nbsp;
					<input type="radio" name="user_mode" value="exclude" <?php echo $this->row->user_mode=='exclude' ? 'CHECKED' : ''; ?> /><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE'); ?>
				</div>
			</div>

				

		<?php
		//echo JHtml::_('sliders.panel',JText::_('COM_AWOCOUPON_DH_COUPON_RECENT'), 'pn_asset');
		echo awoJHtmlSliders::panel(JText::_('COM_AWOCOUPON_DH_COUPON_RECENT'), 'pn_asset');
		?>


			<div style="padding:10px;">
				<div id="div_asset_qty" style="display:none;">
					<span style="width:70px;display:inline-block;padding-bottom:5px;"><?php echo JText::_('COM_AWOCOUPON_GBL_NUMBER'); ?>:</span>
					<input type="text" name="asset1_qty" value="<?php echo $this->row->asset1_qty; ?>" size="4" />
				</div>

				<div id="div_asset1_type">
					<span id="span_asset1_type">
						<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_TYPE'); ?></span>
						<?php echo $this->lists['asset1_function_type']; ?>
					</span>
				</div>
				
				<div id="div_asset1_inner" style="display:none;">
					<div style="padding-top:5px;">
						<a href="javascript:view_some('asset');"><img id="img_asset_simple_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table1.png" class="c_table_select" style="height:22px;" /></a>
						&nbsp; <a href="javascript:view_all('asset');"><img id="img_asset_advanced_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table2.png" style="height:22px;" /></a>
						&nbsp; <a href="javascript:grid_view_all('asset');"><img id="img_asset_grid_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table3.png" style="height:22px;" /></a>
					</div>
					<div id="div_asset_simple_table">
						<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
						<input class="inputbox" type="text" id="asset_search" name="asset_name" size="60" maxlength="255" value="" />
						<input type="hidden" name="asset_id" value="" />
						<button id="btn_asset_search" type="button" onclick="dd_itemselectf('asset'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
					</div>
							
					<div id="div_asset_advanced_table" style="display:none;">
						<div>
							<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
							<input type="text" id="asset_search_txt" size="60" onkeyup="dd_searchg('asset')">
							<button onclick="dd_itemselectg('asset'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
						</div>
						<select name="_assetlist" MULTIPLE class="inputbox" size="2" style="width:100%; height:160px;" ondblclick="dd_itemselectg('asset')"></select>
						<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
						<br />
					</div>

					<div id="div_asset_advanced_grid" style="display:none;">
						<div id="asset_search_grid"></div>
						<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
						<br />
					</div>
					
					<div class="asset_holder">
						<table id="tbl_assets" class="adminlist" cellspacing="1">
						<thead><tr><th><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th><th><?php echo JText::_('COM_AWOCOUPON_GBL_NAME'); ?></th><th>&nbsp;</th></tr></thead>
						<tbody>
						<?php  foreach($this->row->assetlist as $row) { ?>
							<tr id="tbl_assets_tr<?php echo $row->asset_id; ?>">
								<td><?php echo $row->asset_id; ?></td>
								<td><?php echo $row->asset_name; ?></td>
								<td class="last" align="right">
									<?php if($this->row->function_type == 'parent') { ?>
										<button type="button" onclick="moverow('tbl_assets_tr<?php echo $row->asset_id; ?>','up');" >&#8593;</button><button 
												type="button" onclick="moverow('tbl_assets_tr<?php echo $row->asset_id; ?>','down');" >&#8595;</button>&nbsp; 
									<?php } ?>
									<button type="button" onclick="deleterow('tbl_assets_tr<?php echo $row->asset_id; ?>');return false;" >X</button>
									<input type="hidden" name="assetlist[]" value="<?php echo $row->asset_id; ?>">
									<input type="hidden" name="assetnamelist[<?php echo $row->asset_id; ?>]" value="<?php echo $row->asset_name; ?>"></td>
							</tr>
						<?php } ?>
						</tbody></table>
					</div>
					<div id="tr_f2_mode">
						<input type="hidden" name="asset1_mode" value="" />
						<input type="radio" name="asset1_mode" value="include" <?php echo empty($this->row->asset1_mode) || $this->row->asset1_mode=='include' ? 'CHECKED' : ''; ?> /><?php echo JText::_('COM_AWOCOUPON_CP_INCLUDE'); ?> &nbsp;&nbsp;
						<input type="radio" name="asset1_mode" value="exclude" <?php echo $this->row->asset1_mode=='exclude' ? 'CHECKED' : ''; ?> /><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE'); ?>
					</div>
				</div>
			</div>


		<?php
		//echo JHtml::_('sliders.panel',JText::_('COM_AWOCOUPON_DH_COUPON_RECENT'), 'pn_asset');
		echo awoJHtmlSliders::panel(JText::_('COM_AWOCOUPON_DH_COUPON_RECENT'), 'pn_asset2');
		?>


			<div style="padding:10px;">
			
				<div id="div_asset2_qty" style="display:none;">
					<span style="width:70px;display:inline-block;padding-bottom:5px;"><?php echo JText::_('COM_AWOCOUPON_GBL_NUMBER'); ?>:</span>
					<input type="text" name="asset2_qty" value="<?php echo $this->row->asset2_qty; ?>" size="4" />
				</div>

				<div id="div_asset2_type">
					<span id="span_asset2_type">
						<span style="width:70px;display:inline-block;padding-bottom:5px;"><?php echo JText::_('COM_AWOCOUPON_GBL_TYPE'); ?>:</span>
						<?php echo $this->lists['asset2_function_type']; ?>
					</span>
				</div>
				
				<div id="div_asset2_inner" style="display:none;">
					<div style="padding-top:5px;">
						<a href="javascript:view_some('asset2');"><img id="img_asset2_simple_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table1.png" class="c_table_select" style="height:22px;" /></a>
						&nbsp; <a href="javascript:view_all('asset2');"><img id="img_asset2_advanced_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table2.png" style="height:22px;" /></a>
						&nbsp; <a href="javascript:grid_view_all('asset2');"><img id="img_asset2_grid_link" src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/c_table3.png" style="height:22px;" /></a>
					</div>
					<div id="div_asset2_simple_table">
						<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
						<input class="inputbox" type="text" id="asset_search2" name="asset_name2" size="60" maxlength="255" value="" />
						<input type="hidden" name="asset_id2" value="" />
						<button id="btn_asset_search2" type="button" onclick="dd_itemselectf('asset2'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
					</div>
					
					<div id="div_asset2_advanced_table" style="display:none;">
						<div>
							<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
							<input type="text" size="60" id="asset_search_txt2" onkeyup="dd_searchg('asset2')">
							<button onclick="dd_itemselectg('asset2'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
						</div>
						<select name="_assetlist2" MULTIPLE class="inputbox" size="2" style="width:100%; height:160px;" ondblclick="dd_itemselectg('asset2')"></select>
						<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
						<br />
					</div>
					
					<div id="div_asset2_advanced_grid" style="display:none;">
						<div id="asset2_search_grid"></div>
						<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
						<br />
					</div>

					<div class="asset_holder">
						<table id="tbl_assets2" class="adminlist" cellspacing="1">
						<thead><tr><th><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th><th><?php echo JText::_('COM_AWOCOUPON_GBL_NAME'); ?></th><th>&nbsp;</th></tr></thead>
						<tbody>
						<?php  foreach($this->row->assetlist2 as $row) { ?>
							<tr id="tbl_assets2_tr<?php echo $row->asset_id; ?>">
								<td><?php echo $row->asset_id; ?></td>
								<td><?php echo $row->asset_name; ?></td>
								<td class="last" align="right">
									<?php if($this->row->function_type == 'parent') { ?>
										<button type="button" onclick="moverow('tbl_assets2_tr<?php echo $row->asset_id; ?>','up');" >&#8593;</button><button 
												type="button" onclick="moverow('tbl_assets2_tr<?php echo $row->asset_id; ?>','down');" >&#8595;</button>&nbsp; 
									<?php } ?>
									<button type="button" onclick="deleterow('tbl_assets2_tr<?php echo $row->asset_id; ?>');return false;" >X</button>
									<input type="hidden" name="assetlist2[]" value="<?php echo $row->asset_id; ?>">
									<input type="hidden" name="asset2namelist[<?php echo $row->asset_id; ?>]" value="<?php echo $row->asset_name; ?>"></td>
							</tr>
						<?php } ?>
						</tbody></table>
					</div>
					<div id="tr_f2_mode2">
						<input type="hidden" name="asset2_mode" value="" />
						<input type="radio" name="asset2_mode" value="include" <?php echo empty($this->row->asset2_mode) || $this->row->asset2_mode=='include' ? 'CHECKED' : ''; ?> /><?php echo JText::_('COM_AWOCOUPON_CP_INCLUDE'); ?> &nbsp;&nbsp;
						<input type="radio" name="asset2_mode" value="exclude" <?php echo $this->row->asset2_mode=='exclude' ? 'CHECKED' : ''; ?> /><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE'); ?>
					</div>
				</div>
			</div>


		<?php
		//echo JHtml::_('sliders.end');
		echo awoJHtmlSliders::end();
		?>
				
	</div>


	<div class="clr"></div>






<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="controller" value="coupons" />
<input type="hidden" name="view" value="coupon" />
<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="mask" value="0" />
</form>
<div class="clr"></div>


<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>