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
	getjqdd('product_id_search','product_id','ajax_elements','productgift',"<?php echo JURI::base(true); ?>/index.php");

});




function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'CANCELgiftcert') {
		submitform( pressbutton );
		return;
	}

	else if (pressbutton == 'SAVEgiftcert') {
		var err = '';
		
		if(form.product_id.value == '') err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PRODUCT').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM')); ?>';
		if(form.coupon_template_id.value == '') err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_TEMPLATE').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM')); ?>';
		if(form.profile_id.value != '' && !isUnsignedInteger(form.profile_id.value)) err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_PROFILE').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION')); ?>';
		if(form.published.value=='' || (form.published.value!='1' && form.published.value!='-1')) err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PUBLISHED').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
		
		if($j.trim(form.expiration_number.value)!='' || form.expiration_type.value!='') {
			if($j.trim(form.expiration_number.value)=='' || form.expiration_type.value=='') err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
			else if(!isUnsignedInteger($j.trim(form.expiration_number.value)) || $j.trim(form.expiration_number.value)<1) err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
			else if(form.expiration_type.value!='day' && form.expiration_type.value!='month' && form.expiration_type.value!='year' ) err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
		}
		

		if(err != '') alert(err);
		else { submitform( pressbutton ); }
		return; 
	}

}

//-->
</script>

<form action="index.php" method="post" id="adminForm" name="adminForm">


<div class="width-100">
	<fieldset class="adminform"><legend><?php echo JText::_( 'COM_AWOCOUPON_CFG_GENERAL' ); ?></legend>
		<table class="admintable">
			<tr>
				<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_PRODUCT'); ?></label></td>
				<td><?php if(!empty($this->row->id)) echo $this->row->product_name.' ('.$this->row->product_sku.') <input type="hidden" name="product_id" value="'.$this->row->product_id.'" />'; 
							else echo '<input type="text" size="30" value="" id="product_id_search" class="inputbox ac_input"/>'; ?>
							<input type="hidden" name="product_id" value="<?php echo $this->row->product_id; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_TEMPLATE'); ?></label></td>
				<td><?php echo $this->lists['templatelist']; ?> <a href="http://awodev.com/documentation/awocoupon-pro/tutorials/how-create-coupon-template" target="_blank"><img src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/question_mark.png" alt="question mark" height="23" /></a></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_PF_IMAGE'); ?></label></td>
				<td><?php echo $this->lists['profilelist']; ?></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></label></td>
				<td><?php echo $this->lists['published']; ?></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ); ?></label></td>
				<td><input class="inputbox" type="text" name="expiration_number" size="10" value="<?php echo $this->row->expiration_number; ?>" /><?php echo $this->lists['expiration_type']; ?></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="adminform"><legend><?php echo JText::_( 'COM_AWOCOUPON_CP_VENDOR' ); ?></legend>
		<table class="admintable">
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_GBL_NAME' ); ?></label></td>
				<td><input class="inputbox" type="text" name="vendor_name" size="30" maxlength="255" value="<?php echo $this->row->vendor_name; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_GBL_EMAIL' ); ?></label></td>
				<td><input class="inputbox" type="text" name="vendor_email" size="30" maxlength="255" value="<?php echo $this->row->vendor_email; ?>" /></td>
			</tr>
		</table>

	</fieldset>
</div>






<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="view" value="giftcert" />
<input type="hidden" name="layout" value="edit" />
<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="mask" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>