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

jQuery(document).ready(function() {
	getjqdd('coupon_id_search','coupon_id','ajax_elements','coupons_noauto',"<?php echo JURI::base(true); ?>/index.php");

});



function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'CANCELcouponsauto') {
		submitform( pressbutton );
		return;
	}

	else if (pressbutton == 'SAVEcouponsauto') {
		var err = '';
		
		if(form.coupon_id.value == '') err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM')); ?>';
		if(form.published.value=='' || (form.published.value!='1' && form.published.value!='-1')) err += '\n<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_PUBLISHED').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';

		if(err != '') alert(err);
		else { submitform( pressbutton ); }
		return; 
	}

}

//-->
</script>

<form action="index.php" method="post" id="adminForm" name="adminForm">


<div class="width-100">
	<fieldset class="adminform"><legend><?php echo JText::_( 'COM_AWOCOUPON_GBL_ADD' ); ?></legend>
		<table class="admintable">
			<tr>
				<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_COUPON'); ?></label></td>
				<td><input type="text" size="30" value="" id="coupon_id_search" class="inputbox ac_input"/><input type="hidden" name="coupon_id" value="<?php echo $this->row->coupon_id; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></label></td>
				<td><?php echo $this->lists['published']; ?></td>
			</tr>
		</table>
	</fieldset>
	
</div>



<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="view" value="couponsauto" />
<input type="hidden" name="layout" value="edit" />
<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="mask" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>