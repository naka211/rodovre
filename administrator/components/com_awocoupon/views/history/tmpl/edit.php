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

function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'CANCELhistory') {
		submitform( pressbutton );
		return;
	}

	else if (pressbutton == 'SAVEhistory') {
		var err = '';
		
		if(form.coupon_id.value == '') err += '\n<?php echo JText::_('COM_AWOCOUPON_CP_COUPON_CODE').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION'); ?>';
		if(trim(form.username.value)=='') err += '\n<?php echo JText::_('COM_AWOCOUPON_GBL_USERNAME').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_INPUT'); ?>';		

		if(err != '') alert(err);
		else { submitform( pressbutton ); }
		return; 
	}

}

//-->
</script>

<form action="index.php" method="post" id="adminForm" name="adminForm">



<table class="admintable">
	<tr>
		<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_COUPON_CODE'); ?></label></td>
		<td><?php echo $this->lists['couponlist']; ?></td>
	</tr>
	<tr>
		<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_GBL_USERNAME' ); ?></label></td>
		<td><input class="inputbox" type="text" name="username" size="30" maxlength="255" value="<?php echo $this->row->username; ?>" /></td>
	</tr>
	<tr>
		<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_GBL_EMAIL' ); ?></label></td>
		<td><input class="inputbox" type="text" name="user_email" size="30" maxlength="255" value="<?php echo $this->row->user_email; ?>" /></td>
	</tr>
	<tr>
		<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_DISCOUNT').' ('.JText::_('COM_AWOCOUPON_CP_PRODUCT').')'; ?></label></td>
		<td><input class="inputbox" type="text" name="coupon_discount" size="30" maxlength="255" value="<?php echo $this->row->coupon_discount; ?>" /></td>
	</tr>
	<tr>
		<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_DISCOUNT').' ('.JText::_('COM_AWOCOUPON_CP_SHIPPING').')'; ?></label></td>
		<td><input class="inputbox" type="text" name="shipping_discount" size="30" maxlength="255" value="<?php echo $this->row->shipping_discount; ?>" /></td>
	</tr>
	<tr>
		<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_GC_ORDER_NUM' ); ?></label></td>
		<td><input class="inputbox" type="text" name="order_id" size="30" maxlength="255" value="<?php echo $this->row->order_id; ?>" /></td>
	</tr>
</table>








<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="view" value="history" />
<input type="hidden" name="layout" value="edit" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="mask" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>